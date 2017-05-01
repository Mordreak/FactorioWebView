$( function() {
    $.widget("custom.startAndStop", {
        options: {
            startUrl: null,
            stopUrl: null,
            restartUrl: null,
            savesUrl: null
        },

        _create: function () {
            this._on(this.element.find('#start'), {
                click: "start"
            });
            this._on(this.element.find('#restart'), {
                click: "restart"
            });
            this._on(this.element.find('#stop'), {
                click: "stop"
            });
            this._refresh();
        },

        _refresh: function () {
            this._saves();
            this._trigger("change");
        },

        start: function (event) {
            var urlRequest = this.options.startUrl;
            var current = this;
            $('.inner.start').parent().find('.icon i').hide();
            $('.inner.start').css('background-image', 'url(\'/lib/local/img/reload-white.gif\')');
            $.ajax({
                url: urlRequest,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result['done'] == true) {
                        popupMe('Serveur démarré avec succès');
                    } else {
                        popupMe(result['answer']);
                    }
                },

                error: function (result) {
                    popupMe('An error as occured, please try again later');
                },

                complete: function (result) {
                    $('.inner.start').css('background-image', 'none');
                    $('.inner.start').parent().find('.icon i').show();
                    current._refresh();
                }
            });
        },

        stop: function (event) {
            var urlRequest = this.options.stopUrl;
            var current = this;
            $('.inner.stop').parent().find('.icon i').hide();
            $('.inner.stop').css('background-image', 'url(\'/lib/local/img/reload-white.gif\')');
            $.ajax({
                url: urlRequest,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result['done'] == true) {
                        popupMe('Serveur arrêté avec succès');
                    } else {
                        popupMe(result['answer']);
                    }
                },

                error: function (result) {
                    popupMe('An error as occured, please try again later');
                },

                complete: function (result) {
                    $('.inner.stop').css('background-image', 'none');
                    $('.inner.stop').parent().find('.icon i').show();
                    current._refresh();
                }
            });
        },

        restart: function (event) {
            var urlRequest = this.options.restartUrl;
            var current = this;
            $('.inner.restart').parent().find('.icon i').hide();
            $('.inner.restart').css('background-image', 'url(\'/lib/local/img/reload-white.gif\')');
            $.ajax({
                url: urlRequest,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result['done'] == true) {
                        popupMe('Serveur redémarré avec succès');
                    } else {
                        popupMe(result['answer']);
                    }
                },

                error: function (result) {
                    popupMe('An error as occured, please try again later');
                },

                complete: function (result) {
                    $('.inner.restart').css('background-image', 'none');
                    $('.inner.restart').parent().find('.icon i').show();
                    current._refresh();
                }
            });
        },

        _saves: function() {
            var urlRequest = this.options.savesUrl;
            $.ajax({
                url: urlRequest,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result['done'] == true) {
                        var content = '';
                        $.each(result['saves'], function(index, value) {
                            content += '<tr>\
                                <td>' + value['name'] + '</td>\
                                <td><span class="label label-success">' + value['time'] + '</span></td>\
                            </tr>'
                        });
                        $('#saves').html(content);
                    }
                },

                error: function (result) {
                },

                complete: function (result) {
                }
            });
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