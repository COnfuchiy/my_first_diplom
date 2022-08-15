<tr data-id="{{$chat->telegram_id}}">
    <td>
        {{$chat->chat_confirm?$chat->telegram_id:'-'}}
    </td>
    <td>
        <div class="telegram-chat-name">
            {{$chat->chat_name}}
        </div>
    </td>
    <td>
        <div class="telegram-chat-related-username">
            {{$chat->related_username}}
        </div>
    </td>
    <td>
        <div class="telegram-chat-type">
        {{$chat->chat_type?__('telegram.type_group'):__('telegram.type_private')}}
        </div>

    </td>
    <td>
        <div class="telegram-chat-confirm">
            @if(!$chat->chat_confirm)
                <a href="#" class="telegram-chat-confirm-info">
                    {{__('telegram.confirm')}}
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </a>
            @else
            {{__('telegram.confirmed')}}
            @endif
        </div>
    </td>
    <td>
        <a class="remove-telegram-chat" href="#">
            {{__('form.remove')}}
        </a>
    </td>
</tr>
