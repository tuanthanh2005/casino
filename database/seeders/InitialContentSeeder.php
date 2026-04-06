<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $categories = [
            // English Categories
            [
                'name' => 'Beginners',
                'slug' => 'beginners',
                'description' => 'Essential guides and tips for those just starting their aquarium journey.',
                'lang' => 'en',
            ],
            [
                'name' => 'Setup Guides',
                'slug' => 'setup-guides',
                'description' => 'Step-by-step instructions on setting up your first tank, substrate, filters, and more.',
                'lang' => 'en',
            ],
            [
                'name' => 'Fish Care',
                'slug' => 'fish-care',
                'description' => 'Learn how to keep your fish healthy, happy, and thriving.',
                'lang' => 'en',
            ],
            [
                'name' => 'Problems & Fixes',
                'slug' => 'problems-fixes',
                'description' => 'Troubleshooting common aquarium issues like cloudy water, algae, and fish illness.',
                'lang' => 'en',
            ],
            [
                'name' => 'Product Reviews',
                'slug' => 'product-reviews',
                'description' => 'Honest reviews of filters, heaters, lighting, and fish food.',
                'lang' => 'en',
            ],
            [
                'name' => 'Comparisons',
                'slug' => 'comparisons',
                'description' => 'Comparing different aquarium equipment and fish species.',
                'lang' => 'en',
            ],
            // Vietnamese Categories
            [
                'name' => 'Cho người mới',
                'slug' => 'cho-nguoi-moi',
                'description' => 'Các hướng dẫn và mẹo thiết yếu cho những người mới bắt đầu hành trình nuôi bể cá.',
                'lang' => 'vi',
            ],
            [
                'name' => 'Hướng dẫn lắp đặt',
                'slug' => 'huong-dan-lap-dat',
                'description' => 'Hướng dẫn từng bước cách thiết lập bể đầu tiên, phân nền, bộ lọc và nhiều hơn nữa.',
                'lang' => 'vi',
            ],
            [
                'name' => 'Chăm sóc cá',
                'slug' => 'cham-soc-ca',
                'description' => 'Tìm hiểu cách giữ cho cá của bạn khỏe mạnh, hạnh phúc và phát triển tốt.',
                'lang' => 'vi',
            ],
            [
                'name' => 'Vấn đề & Khắc phục',
                'slug' => 'van-de-khac-phuc',
                'description' => 'Khắc phục các sự cố bể cá thường gặp như nước đục, rêu hại và bệnh ở cá.',
                'lang' => 'vi',
            ],
            [
                'name' => 'Đánh giá sản phẩm',
                'slug' => 'danh-gia-san-pham',
                'description' => 'Đánh giá trung thực về bộ lọc, máy sưởi, đèn và thức ăn cho cá.',
                'lang' => 'vi',
            ],
            [
                'name' => 'So sánh',
                'slug' => 'so-sanh',
                'description' => 'So sánh các thiết bị bể cá khác nhau và các loài cá.',
                'lang' => 'vi',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug'], 'lang' => $cat['lang']], $cat);
        }

        $cats = Category::all()->groupBy('lang')->map(function ($items) {
            return $items->pluck('id', 'slug');
        });

        $posts = [
            // English Posts
            [
                'title' => 'Best Fish for Beginners',
                'category_slug' => 'beginners',
                'lang' => 'en',
                'content' => 'If you are just starting out with your first aquarium, choosing the right fish is crucial... [Seed Content]',
            ],
            [
                'title' => 'How to Set Up an Aquarium Step by Step',
                'category_slug' => 'setup-guides',
                'lang' => 'en',
                'content' => 'Setting up a fish tank is an exciting process. Here is our step-by-step guide... [Seed Content]',
            ],
            [
                'title' => 'Why Are My Fish Dying?',
                'category_slug' => 'problems-fixes',
                'lang' => 'en',
                'content' => 'It is heartbreaking to find your fish floating at the top of the tank. Here is why it might be happening... [Seed Content]',
            ],
            [
                'title' => 'Best Aquarium Filter for Beginners',
                'category_slug' => 'product-reviews',
                'lang' => 'en',
                'content' => 'A good filtration system is the heart of any aquarium. We review the top 5 beginner filters... [Seed Content]',
            ],
            [
                'title' => 'Cloudy Aquarium Water Fix',
                'category_slug' => 'problems-fixes',
                'lang' => 'en',
                'content' => 'Cloudy water is a common issue for new tanks. Learn how to clear it up quickly... [Seed Content]',
            ],
            [
                'title' => 'Betta Fish Care Guide',
                'category_slug' => 'fish-care',
                'lang' => 'en',
                'content' => 'Bettas are popular for their vibrant colors and personality. Here is how to care for them properly... [Seed Content]',
            ],
            [
                'title' => 'How Often Should You Feed Fish?',
                'category_slug' => 'fish-care',
                'lang' => 'en',
                'content' => 'Overfeeding is a lead cause of water quality issues. Discover the ideal feeding schedule... [Seed Content]',
            ],
            [
                'title' => 'Best Small Fish Tanks',
                'category_slug' => 'product-reviews',
                'lang' => 'en',
                'content' => 'Sometimes a small footprint is all you need. Here are the best nano and small tanks for beginners... [Seed Content]',
            ],
            [
                'title' => 'Sponge Filter vs HOB Filter',
                'category_slug' => 'comparisons',
                'lang' => 'en',
                'content' => 'Both filters have their pros and cons. Let us compare which one is right for your setup... [Seed Content]',
            ],
            [
                'title' => 'Easiest Fish to Take Care Of',
                'category_slug' => 'beginners',
                'lang' => 'en',
                'content' => 'Low maintenance fish are great for busy beginners. Here is our top 10 list... [Seed Content]',
            ],
            // Vietnamese Posts
            [
                'title' => 'Cá tốt nhất cho người mới bắt đầu',
                'category_slug' => 'cho-nguoi-moi',
                'lang' => 'vi',
                'content' => 'Nếu bạn mới bắt đầu với bể cá đầu tiên, việc chọn đúng loại cá là rất quan trọng... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Cách thiết lập bể cá từng bước',
                'category_slug' => 'huong-dan-lap-dat',
                'lang' => 'vi',
                'content' => 'Thiết lập bể cá là một quá trình thú vị. Đây là hướng dẫn từng bước của chúng tôi... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Tại sao cá của tôi bị chết?',
                'category_slug' => 'van-de-khac-phuc',
                'lang' => 'vi',
                'content' => 'Thật đau lòng khi thấy cá của bạn nổi trên mặt nước. Đây là lý do tại sao nó có thể xảy ra... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Bộ lọc bể cá tốt nhất cho người mới',
                'category_slug' => 'danh-gia-san-pham',
                'lang' => 'vi',
                'content' => 'Một hệ thống lọc tốt là trái tim của bất kỳ bể cá nào. Chúng tôi đánh giá 5 bộ lọc hàng đầu... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Cách xử lý nước bể cá bị đục',
                'category_slug' => 'van-de-khac-phuc',
                'lang' => 'vi',
                'content' => 'Nước đục là vấn đề thường gặp ở các bể mới. Tìm hiểu cách làm trong nước nhanh chóng... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Hướng dẫn chăm sóc cá Betta',
                'category_slug' => 'cham-soc-ca',
                'lang' => 'vi',
                'content' => 'Cá Betta nổi tiếng với màu sắc rực rỡ và cá tính. Đây là cách chăm sóc chúng đúng cách... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Bạn nên cho cá ăn bao nhiêu lần?',
                'category_slug' => 'cham-soc-ca',
                'lang' => 'vi',
                'content' => 'Cho ăn quá nhiều là nguyên nhân chính gây ra các vấn đề về chất lượng nước... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Bể cá nhỏ tốt nhất',
                'category_slug' => 'danh-gia-san-pham',
                'lang' => 'vi',
                'content' => 'Đôi khi một không gian nhỏ là tất cả những gì bạn cần. Đây là những bể nano và bể nhỏ tốt nhất... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Lọc mút vs Lọc treo (HOB)',
                'category_slug' => 'so-sanh',
                'lang' => 'vi',
                'content' => 'Cả hai bộ lọc đều có ưu và nhược điểm. Hãy so sánh xem cái nào phù hợp với bạn... [Nội dung hạt giống]',
            ],
            [
                'title' => 'Những loài cá dễ nuôi nhất',
                'category_slug' => 'cho-nguoi-moi',
                'lang' => 'vi',
                'content' => 'Các loài cá ít cần bảo dưỡng rất tuyệt vời cho người mới bận rộn. Đây là danh sách top 10... [Nội dung hạt giống]',
            ],
        ];

        foreach ($posts as $post) {
            $lang = $post['lang'];
            $category_id = $cats[$lang][$post['category_slug']] ?? null;

            if ($category_id) {
                Post::updateOrCreate(
                    ['slug' => Str::slug($post['title']), 'lang' => $lang],
                    [
                        'title' => $post['title'],
                        'category_id' => $category_id,
                        'content' => $post['content'],
                        'slug' => Str::slug($post['title']),
                        'lang' => $lang,
                        'excerpt' => Str::limit(strip_tags($post['content']), 150),
                        'author_id' => $admin->id,
                        'status' => 'published',
                        'published_at' => now(),
                        'is_featured' => rand(0, 5) === 0,
                    ]
                );
            }
        }
    }

}
