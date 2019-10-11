
$(document).ready(function () {
    // console.log(_cinema_hall);
    var d = JSON.parse(_cinema_hall);
    $.each(d, function (index, rowData) {
        drawRow(index, rowData);
    });

});

function drawRow(index, rowData) {
    var ret = '<div class="kinoRow" id="kinoRow_' + index + '">' +
                '<div class="row">' +
                    '<div class="col-md-1">' + index +
                    '</div>' +
                    '<div class="col-md-11" align="center">';

    $.each(rowData, function (i, data) {
        ret += '<button id="id_' + index + '_' + data['number'] + '" class="seatBtn ' + data['status'] + '"' +
            'data-rownumber="' + index + '" ' +
            'data-seatnumber="' + data['number'] + '" ' +
            'data-status="' +  data['status'] + '" '+
            'data-persona="' +  data['persona'] + '" ' +
            ' disabled>' +
                data['number'] +
            '</button>';
    });
    ret += '</div></div></div>';
    $('#rows').append(ret);
}

