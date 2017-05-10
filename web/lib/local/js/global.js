function getLogs(urlRequest) {
    $.ajax({
        url: urlRequest,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            if (result['done'] == true) {
                var content = '';
                $.each(result['logs'], function (index, value) {
                    content += '<tr>\
                        <td>' + value['time'] + '</td>\
                        <td>' + value['info'] + '</td>\
                    </tr>'
                });
                $('#logs').append(content);
            }
        },

        error: function (result) {
        },

        complete: function (result) {
        }
    });
}