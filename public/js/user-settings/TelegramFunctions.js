class TelegramFunctions {

    static path = '/user-control-panel/telegram/';

    static telegramTableClassName = '.telegram-chats tbody';

    static urlsSet = {
        remove: 'destroy',
        add: 'store',
    };

    static constructUrl(type, telegramId) {
        if (telegramId) {
            return this.path + telegramId.toString() + '/' + this.urlsSet[type];
        } else {
            return this.path + this.urlsSet[type];
        }
    }

    static removeTelegramChat(elem, telegramId) {
        let url = this.constructUrl('remove', telegramId);
        $.ajax(
            {
                url: url,
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response) {
                        elem.parent().parent().remove();
                    }
                },
                error: (e) => {
                    console.log(e);
                    // throw new Error(''); TODO
                }
            }
        );
    }

    static addNewTelegramChat(chatName, relatedUsername, chatType) {
        let url = this.constructUrl('add');
        let data = {chatName: chatName, relatedUsername: relatedUsername.replace('@',''), chatType: chatType};
        $.ajax( {
            url:url,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: (response) => {
                if (response.success) {
                    if (response.tableRow) {
                        $(this.telegramTableClassName).append(response.tableRow);
                    }
                    $('.telegram-form-message-area').html(response.message);
                }
            },

            error: (e) => {
                // TODO loader ends
                console.log(e);
                // throw new Error(''); TODO
            }
        });
    }
}
