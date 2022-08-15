<?php


namespace App\Monitoring;

require_once '/var/www/html/monitoring/vendor/eleirbag89/telegrambotphp/Telegram.php';

use App\Models\TelegramChats;
use Carbon\Carbon;
use Telegram;

class TelegramComponent
{

    public static function confirmProcess()
    {
        $telegram = new Telegram(self::getBotToken());
        $text = $telegram->Text();
        $chatId = $telegram->ChatID();
        if ($text == '/start') {
            $username = $telegram->Username();
            if (TelegramChats::where(
                [
                    ['telegram_id', '=', $chatId],
                    ['related_username', '=', $username],
                ]
            )->get()->count()) {
                self::sendMessage(
                    $telegram,
                    $chatId,
                    __('telegram.message_already_exist')
                );
                return;
            }
            $unconfirmedChats = TelegramChats::getAllUnconfirmedChatsByRelatedUsername($username);
            if (count($unconfirmedChats)) {
                $chatInfo = $telegram->getChat(['chat_id' => $chatId])['result'];
                $chatType = $chatInfo['type'] === 'private' ? TelegramChats::PRIVATE_CHAT : TelegramChats::GROUP;
                foreach ($unconfirmedChats as $unconfirmedChat) {
                    if($chatType===$unconfirmedChat->chat_type){
                        $unconfirmedChat->telegram_id = $chatId;
                        $unconfirmedChat->chat_confirm = true;
                        if (!$unconfirmedChat->chat_name) {
                            if ($chatType === TelegramChats::PRIVATE_CHAT) {
                                $unconfirmedChat->chat_name = $username;
                            } else {
                                $unconfirmedChat->chat_name = $chatInfo['title'] ?? $username;
                            }
                        }
                        if (!$unconfirmedChat->save()) {
                            self::sendMessage(
                                $telegram,
                                $chatId,
                                __('telegram.message_error')
                            );
                        }
                        self::sendMessage(
                            $telegram,
                            $chatId,
                            __('telegram.message_success_confirm',['chatName'=>($unconfirmedChat->chat_name !== $username) ? $unconfirmedChat->chat_name : ''])
                        );
                        return;
                    }
                }
            } else {
                self::sendMessage(
                    $telegram,
                    $chatId,
                    __('telegram.message_not_related_user')//TODO
                );
            }
        }
        else{
            self::sendMessage(
                $telegram,
                $chatId,
                __('telegram.message_already_exist')
            );
            return;
        }
    }

    private static function getBotToken()
    {
        return '5138398392:AAGK50V7XZ5mpC411lL3Rvx7ms2SzpHj92I'; //TODO
    }

    public static function sendMessage(Telegram $telegramHandle, int $chatId, string $message)
    {
        $content = ['chat_id' => $chatId, 'text' => $message];
        return $telegramHandle->sendMessage($content);
    }

    private static function getChatByRelatedUsername($username): TelegramChats|false
    {
        $chats = TelegramChats::getAllActiveUnconfirmedChats();
        $outputChat = false;
        foreach ($chats as $chat) {
            if ($chat->related_username === $username) {
                $outputChat = $chat;
            }
        }
        return $outputChat;
    }

    public static function getBotLink()
    {
        return config('telegram.botsLinks')[0]; //TODO
    }

    private static function checkUnconfirmed(): bool
    {
        $chats = TelegramChats::getAllActiveUnconfirmedChats();
        $totalCount = 0;
        $currentDatetime = now();
        foreach ($chats as $chat) {
            $chatConfirmDateTime = Carbon::createFromTimeString($chat->chat_confirm_counter_start);
            if ($chatConfirmDateTime->addMinutes(TelegramChats::CONFIRM_TIME_IN_MINUTES) <= $currentDatetime) {
                $chat->chat_confirm_counter_start = null;
                if (!$chat->save()) {
                    // error log
                }
            } else {
                $totalCount++;
            }
        }
        return $totalCount;
    }

    public static function monitoringNotify(int $chatId,string $message){
        $telegram = new Telegram(self::getBotToken());
        self::sendMessage(
            $telegram,
            $chatId,
            $message
        );
    }
}
