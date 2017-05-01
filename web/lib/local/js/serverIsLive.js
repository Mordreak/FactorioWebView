var passOn = function () {
    var elem = $('#panel').find('.pull-left.info a');
    if (!elem.find('i').hasClass('text-success')) {
        elem.html('<i class="fa fa-circle text-success"></i> Server Online');
    }
};

var passOf = function () {
    var elem = $('#panel').find('.pull-left.info a');
    if (elem.find('i').hasClass('text-success')) {
        elem.html('<i class="fa fa-circle"></i> Server Offline');
    }
};


var isServerLive = function (urlRequest) {
    if (checkRequest == null) {
        checkRequest = $.ajax({
            url: urlRequest,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result['done'] == true && result['answer'] == true) {
                    passOn();
                } else {
                    passOf();
                }
            },

            error: function (result) {
                passOf();
            },

            complete: function (result) {
                checkRequest = null;
            }
        });
    }
};