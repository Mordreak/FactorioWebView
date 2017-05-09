$( function() {
    $.widget("custom.createGame", {
        options: {
            createUrl: null,
            savesUrl: null
        },

        _create: function () {
            this._on(this.element, {
                click: "create"
            });

            this._refresh();
        },

        _refresh: function () {
            this.saves();
            this._trigger("change");
        },

        create: function (event) {
            var saveName = $('#savename-input').val();
            if (saveName != '') {
            if (request == null) {
                var confirmation = confirm("Be carefull, this will shutdown the server !\nWould you like to continue?");
                if (confirmation == true) {
                        var urlRequest = this.options.createUrl;
                        var current = this;
                        $('#saves-title i').hide();
                        $('#saves-title').css('background-image', 'url(\'/lib/local/img/load-tiny.gif\')');
                        request = $.ajax({
                            url: urlRequest,
                            data: {savename: saveName},
                            type: 'GET',
                            dataType: 'json',
                            success: function (result) {
                                if (result['done'] == true) {
                                    popupMe('Game successfully created');
                                } else {
                                    popupMe(result['answer']);
                                }
                            },

                            error: function (result) {
                                popupMe('An error as occured, please try again later');
                            },

                            complete: function (result) {
                                $('#saves-title').css('background-image', 'none');
                                $('#saves-title i').show();
                                request = null;
                                current._refresh();
                            }
                        });
                    }
                }
            } else {
                popupMe('Please, give a name to your game');
            }
        },

        saves: function(urlRequest) {
            if (urlRequest === undefined)
                urlRequest = this.options.savesUrl;
            $('#saves-title i').hide();
            $('#saves-title').css('background-image', 'url(\'/lib/local/img/load-tiny.gif\')');
            $.ajax({
                url: urlRequest,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result['done'] == true) {
                        var content = '';
                        $.each(result['saves'], function (index, value) {
                            content += '<tr>\
                            <td>' + value['name'] + '</td>\
                            <td><span class="label label-success">' + value['time'] + '</span></td>\
                            <td><span class="label label-info" id="' + value['name'] + '">Load</span></td>\
                        </tr>'
                        });
                        $('#saves').html(content);
                    }
                },

                error: function (result) {
                },

                complete: function (result) {
                    $('#saves-title').css('background-image', 'none');
                    $('#saves-title i').show();
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