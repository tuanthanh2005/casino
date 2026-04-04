<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepositOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramNotifier;

class SepayController extends Controller
{
    public function webhook(Request $request)
    {
        // Lấy API Key cấu hình từ database
        $apiKey = \App\Models\NavSetting::get('sepay_api_key'); 
        $headerToken = $request->header('Authorization');

        // Xác thực API Key nếu trong DB có cấu hình sepay_api_key
        if ($apiKey) {
            if ($headerToken !== 'Apikey ' . $apiKey && str_replace('Apikey ', '', $headerToken) !== $apiKey) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
        }

        try {
            $data = $request->all();
            
            // Ghi log để tiện debug
            Log::info('Sepay Webhook: ', $data);

            $id = $data['id'] ?? null;
            $amountIn = (float)($data['amountIn'] ?? 0);
            $code = $data['code'] ?? null; 

            // Nếu Sepay chưa parse được code, thử trích xuất từ nội dung giao dịch
            if (!$code) {
                $content = $data['transactionContent'] ?? '';
                // Mã bắt đầu bằng CTB (Nạp tiền) hoặc NAV (Hỗ trợ MXH)
                if (preg_match('/((?:CTB|NAV)[A-Z0-9]{7,8})/', strtoupper($content), $matches)) {
                    $code = $matches[1];
                }
            }

            // Xử lý nạp tiền / thanh toán tự động
            if ($amountIn > 0 && $code) {
                // 1. Xử lý đơn nạp CTB (DepositOrder)
                if (str_starts_with($code, 'CTB')) {
                    $order = DepositOrder::where('order_code', $code)->where('status', 'pending')->first();
                    
                    if ($order) {
                        DB::transaction(function () use ($order, $amountIn, $id) {
                            $order->status = 'approved';
                            $order->amount = $amountIn;
                            $order->points_credited = $amountIn;
                            $order->approved_at = now();
                            $order->admin_note = 'Auto approved by Sepay. Transaction ID: ' . $id;
                            $order->save();

                            // Cộng tiền
                            $order->user->increment('balance_point', $amountIn);
                        });

                        TelegramNotifier::send(
                            "<b>✅ Auto Nạp Tiền (Sepay)</b>\n"
                            . "Mã: <code>{$order->order_code}</code>\n"
                            . "User: {$order->user->name} (#{$order->user->id})\n"
                            . "Số tiền nhận: " . number_format($amountIn, 0) . " VNĐ"
                        );
                    }
                }
                // 2. Xử lý đơn Hỗ trợ MXH NAV (NavOrder)
                elseif (str_starts_with($code, 'NAV')) {
                    $navOrder = \App\Models\NavOrder::where('order_code', $code)->where('status', 'pending_payment')->first();
                    
                    if ($navOrder) {
                        $navOrder->update([
                            'status' => 'paid',
                            'payment_confirmed_at' => now(),
                            'admin_notes' => trim($navOrder->admin_notes . "\nAuto paid by Sepay. Transaction ID: " . $id),
                        ]);

                        TelegramNotifier::send(
                            "<b>💸 Auto Thanh Toán Đơn NAV (Sepay)</b>\n"
                            . "Mã: <code>{$navOrder->order_code}</code>\n"
                            . "User ID: {$navOrder->user_id}\n"
                            . "Số tiền nhận: " . number_format($amountIn, 0) . " VNĐ\n"
                            . "Trạng thái mới: <b>paid</b>"
                        );
                    }
                }
            }

            // Trả về JSON 200 HTTP OK cho Sepay để không báo lỗi
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Sepay Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
