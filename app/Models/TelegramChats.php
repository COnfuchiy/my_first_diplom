<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;

class TelegramChats extends Model
{
    use HasFactory;

    protected $primaryKey = 'telegram_id';

    public $incrementing = false;

    public const PRIVATE_CHAT = 0;

    public const GROUP = 0;

    public const CONFIRM_TIME_IN_MINUTES = 15;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'telegram_id',
        'user_id',
        'chat_name',
        'related_username',
        'chat_type',
        'chat_confirm',
    ];

    public static function getAllUnconfirmedChatsByRelatedUsername($username){
        return self::where(
            [
                ['chat_confirm','=',false],
                ['related_username','=',$username],
            ]
        )->get();
    }
}
