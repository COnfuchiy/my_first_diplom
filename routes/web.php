<?php

use App\Http\Controllers\UserControlPanel\AvailabilityReportController;
use App\Http\Controllers\UserControlPanel\OverviewController;
use App\Http\Controllers\UserControlPanel\PerformanceReportController;
use App\Http\Controllers\UserControlPanel\SiteListController;
use App\Http\Controllers\UserControlPanel\StatisticController;
use App\Http\Controllers\UserControlPanel\TelegramController;
use App\Http\Controllers\UserControlPanel\UserSettingsController;
use App\Jobs\AvailabilityMonitoringJob;
use App\Jobs\PerformanceMonitoringJob;
use App\Jobs\TelegramConfirmJob;
use App\Models\AvailabilityMonitoringReport;
use App\Models\MonitoringSite;
use App\Models\Page;
use App\Models\PerformanceMonitoringReport;
use App\Models\TelegramChats;
use App\Models\User;
use App\Monitoring\BaseMonitoringComponent;
use App\Monitoring\PageSpeedInsightsComponent;
use App\Monitoring\PerformanceMonitoringMainComponent;
use App\Monitoring\PerformanceMonitoringReportComponent;
use App\Monitoring\RequestUrlComponent;
use App\Monitoring\TelegramComponent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/',
    function () {
        return redirect('login');
    }
);

Route::get(
    '/test',
    function () {
        var_dump(MonitoringSite::find(1)->telegramChat);
    }
);

Route::get(
    '/test-cron',
    function () {

    }
);


Route::prefix('user-control-panel')->middleware('auth')->group(
    function () {
        Route::resource('sites', SiteListController::class)->only(
            [
                'index',
                'create',
                'store',
                'destroy',
                'edit',
                'update',
            ]
        );


        Route::resource('availability-reports', AvailabilityReportController::class)->only(
            [
                'index',
                'show',
            ]
        );


        Route::resource('performance-reports', PerformanceReportController::class)->only(
            [
                'index',
                'show',
            ]
        );

        Route::post('/telegram/store', [TelegramController::class, 'store']);
        Route::post('/telegram/{telegram_id}/destroy', [TelegramController::class, 'destroy']);

        Route::post('sites/activity/{site}', [SiteListController::class, 'activity']);

        Route::get('overview', [OverviewController::class, 'index'])->name('overview');

        Route::get('telegram-chats', [UserSettingsController::class, 'index'])->name('telegram-chats');

        Route::get('statistic/{siteId}/pages', [StatisticController::class, 'page']);
        Route::get('statistic/{siteId}/psi', [StatisticController::class, 'psi']);
        Route::get('statistic/{siteId}', [StatisticController::class, 'index'])->name('statistic');
    }
);

Route::post(
    '/telegram-confirm',
    function () {
        Log::info('1');
        TelegramComponent::confirmProcess();
    }
);

Route::get('/test200', function (){

});
Route::get('/test400',function (){
    abort(400);
});
Route::get('/test500',function (){
    abort(500);
});


require __DIR__ . '/auth.php';
