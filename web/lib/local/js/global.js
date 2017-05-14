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

function getMods(urlRequest) {
    $.ajax({
        url: urlRequest,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            if (result['done'] == true) {
                var content = '';
                $.each(result['mods'], function (index, value) {
                    content += '<tr><td>' + value['name'] + '</td>';
                    if (value['installed'] == true) {
                        content += '<td id="'+ value['name'] +'-bool">Yes</td>';
                        content += '<td><span class="label label-danger" id="'+ value['name'] +'">Disable</span></td></tr>';
                    } else {
                        content += '<td id="'+ value['name'] +'-bool">No</td>';
                        content += '<td><span class="label label-info" id="'+ value['name'] +'">Activate</span></td></tr>';
                    }
                });
                $('#mods').append(content);
            }
        },

        error: function (result) {
        },

        complete: function (result) {
        }
    });
}