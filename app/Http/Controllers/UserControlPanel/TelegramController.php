<?php


namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Jobs\TelegramConfirmJob;
use App\Models\TelegramChats;
use App\Monitoring\TelegramComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramController extends Controller
{

    public function store(Request $request)
    {
        if ($request->ajax()) {
            // Validate request params
            $request->validate(
                [
                    'chatName' => 'nullable|string',
                    'chatType' => 'required|integer',
                    'relatedUsername' => 'required|string'
                ]
            );
            $user = Auth::user();
            // Get tmp id
            $tmpTelegramId = (integer)(substr_replace(
                (string)$user->id,
                (string)now()->getTimestamp(),
                0));
            $telegramChat = new TelegramChats([
                'telegram_id' => $tmpTelegramId,
                'user_id' => $user->id,
                'related_username' => $request->relatedUsername,
                'chat_name' => $request->chatName ?? '',
                'chat_type' => $request->chatType,
                'chat_confirm' => false,
            ]);

            if (!$telegramChat->save()) {
                return [
                    'success' => false,
                    'message'=> 'Server error. Try again'
                ];
            }
            // Start confirm job
            TelegramConfirmJob::dispatch();
            $botLink = TelegramComponent::getBotLink();
            return [
                'success' => true,
                'message'=>!$request->chatType ? __('telegram.confirm_private_chat_message',['link'=>"<a  target=\"_blank\" href=\"$botLink\">@".array_reverse(explode('/',$botLink))[0].'</a>']) :
                    __('telegram.confirm_group_chat_message',['link'=>"<a  target=\"_blank\" href=\"$botLink\">@".array_reverse(explode('/',$botLink))[0].'</a>']),
                'tableRow'=>view('components.user-settings.telegram-chats-table-row',[
                    'chat'=>$telegramChat
                ])->render()
            ];
        }
        return redirect()->action([UserSettingsController::class, 'index']);
    }

    public function destroy(Request $request,int $telegram_id): bool|RedirectResponse|null
    {
        $chat = TelegramChats::find($telegram_id);
        if ($request->ajax()) {
            return $chat->delete();
        }
        return redirect()->action([UserSettingsController::class, 'index']);
    }
}
