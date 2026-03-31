<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Schedule: Chốt kết quả mỗi phút
|--------------------------------------------------------------------------
| Thêm vào crontab server:
|   * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
|
*/
Schedule::command('game:resolve')->everyMinute();
