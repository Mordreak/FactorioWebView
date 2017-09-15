$( function() {
    $.widget("custom.activateMod", {
        options: {
            activateUrl: null
        },

        _create: function () {
            this._refresh();
        },

        _refresh: function () {
            this._trigger("change");
        },

        toggle: function (modName) {
            if (typeof modName == 'string') {
                var datas = {modname: modName};
            }
            if (modName != '') {
                if (request == null) {
                    var urlRequest = this.options.activateUrl;
                    var current = this;
                    request = $.ajax({
                        url: urlRequest,
                        data: datas,
                        type: 'GET',
                        dataType: 'json',
                        success: function (result) {
                            if (result['done'] == true) {
                                popupMe('Please restart the server for the changes to take effect');
                                if (result['action'] == 'installed') {
                                    console.log($('#' + modName).parents('#mods tr').find('.bool').text());
                                    document.getElementById(modName + '-bool').innerText = 'Yes';
                                    document.getElementById(modName).innerHTML = 'Disable';
                                    document.getElementById(modName).className += "label label-danger";

                                } else {
                                    console.log($('#' + modName).parents('#mods tr').find('.bool').text());
                                    document.getElementById(modName + '-bool').innerText = 'No';
                                    document.getElementById(modName).innerHTML = 'Activate';
                                    document.getElementById(modName).className += "label label-info";
                                }
                            } else {
                                popupMe(result['answer']);
                            }
                        },

                        error: function (result) {
                            popupMe('An error as occured, please try again later');
                        },

                        complete: function (result) {
                            request = null;
                        }
                    });
                }
            } else {
                popupMe('Your mod is missing a name');
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