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
                    current._refresh();
                }
            });
        },

        stop: function (event) {
            var urlRequest = this.options.stopUrl;
            var current = this;
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
                    current._refresh();
                }
            });
        },

        restart: function (event) {
            var urlRequest = this.options.restartUrl;
            var current = this;
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