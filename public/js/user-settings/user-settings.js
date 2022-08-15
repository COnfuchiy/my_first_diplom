function modalComponent() {
    return {
        modalOpen: false,
        messageShow: false,
        chat_type:0,
        submitForm(){
            this.messageShow = true;
            $('.telegram-form-message-area').html('Loading...');
            let chatName = $('#chat_name').val();
            let relatedUsername = $('#related_username').val();
            let chatType = this.chat_type;
            TelegramFunctions.addNewTelegramChat(chatName,relatedUsername,chatType);
        }
    }
}


$(() => {

    $('.remove-telegram-chat').on('click', function () {
        let tableRow = $(this).parent().parent();
        let telegramId = tableRow.data('id');
        if (telegramId && confirm('Удалить эту беседу?')) {
            TelegramFunctions.removeTelegramChat(tableRow, telegramId);
        }
    });

    $('.telegram-chat-confirm-info').on('click', function () {
        let telegramChatRow = $(this).parent().parent().parent();
        let chatType = telegramChatRow.find('.telegram-chat-type').html();
        $('.related-user').html(telegramChatRow.find('.telegram-chat-related-username').html());
        if (chatType==='Группа'){
            $('.telegram-confirm-message-group').show();
            $('.telegram-confirm-message-private').hide();
        }
        else{
            $('.telegram-confirm-message-group').hide();
            $('.telegram-confirm-message-private').show();
        }
        $('.telegram-confirm-modal-btn').click();
    });

});
