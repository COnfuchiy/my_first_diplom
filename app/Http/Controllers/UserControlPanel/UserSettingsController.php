<?php


namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityMonitoringReport;
use App\Models\TelegramChats;
use App\Monitoring\TelegramComponent;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserSettingsController  extends Controller
{
    public function index(): Factory|View|Application
    {
        $user = Auth::user();
        $telegramChats = TelegramChats::where('user_id', Auth::id())->get();
        $botLink = TelegramComponent::getBotLink();
        return view('user-settings.index', compact('user','telegramChats','botLink'));
    }

}
