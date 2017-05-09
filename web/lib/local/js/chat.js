$(function () {
    $.widget("custom.chat", {
        options: {
            chatUrl: null,
        },

        _create: function () {
            this._on(this.element, {
                click: "chat"
            });

            this._refresh();
        },

        _refresh: function () {
            this._trigger("change");
        },

        chat: function (event) {
            if (chatRequest == null) {
                var quoteContent = $('#chat-input').val();
                var urlRequest = this.options.chatUrl;
                var current = this;
                chatRequest = $.ajax({
                    url: urlRequest,
                    data: {quote: quoteContent},
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        if (result['done'] == true) {
                            popupMe('Partie créée avec succès');
                        } else {
                            popupMe(result['answer']);
                        }
                    },

                    error: function (result) {
                        popupMe('An error as occured, please try again later');
                    },

                    complete: function (result) {
                        chatRequest = null;
                        current._refresh();
                    }
                });
            }
        },


        _destroy: function () {
        },

        _setOptions: function () {
            this._superApply(arguments);
            this._refresh();
        },

        _setOption: function (key, value) {
            this._super(key, value);
        }
    });
});