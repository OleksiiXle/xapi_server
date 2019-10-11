var rowCount = 1;
const ROW_LENGTH = 15;
$(document).ready(function () {
    console.log(_mode);
   // console.log(_cinema_hall);
    switch (_mode) {
        case 'create':
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            drawNewRow(ROW_LENGTH);
            break;
        case 'update':
            var d = JSON.parse(_cinema_hall);
            var buf;
            $.each(d, function (index, value) {
                console.log(index);
                console.log(Object.keys(value).length);
                buf = parseInt(Object.keys(value).length);
                drawNewRow(buf);

                /*
                                $.each(value, function (index, value) {

                                    console.log(value);
                                });
                                */
            });

            break;

    }

});


function saveHall() {
    var rowsData = [];
    var seatsData = [];
    var i;
    $('.rowSeats').each(function (index, value) {
       //  console.log(this.dataset);
        seatsData = [];
        for (i=1; i <= parseInt(this.dataset.rowlength); i++){
            seatsData.push(JSON.stringify({
                'number' : i,
                'status' : 'free',
                'price' : '150',
                'persona' : 'None',
            }));
        }
        rowsData.push(seatsData);
    });
   // console.log(rowsData);
    $('#kino-cinema_hall').val(JSON.stringify(rowsData));



    switch (_mode) {
        case 'create':
            $('#form-create').submit();
            break;
        case 'update':
            $('#form-update').submit();
            break;

    }
}

function drawRowSeats(rowLength, rowNumber) {
    var row = '';
    for (var i=1; i <= rowLength; i++){
        row += '<span class="seatFree" ' +
            'data-rowNumber="' + rowNumber + '" ' +
            'data-seatNumber="' + i + '" ' +
            'data-status="free" '+
            'data-status="free" '+
            'data-user_id="0"' +
            '> ' + i + ' </span>';
    }
    return '<span id="rowSeats_' + rowNumber +
        '" class="rowSeats"' +
        ' data-rowLength="' + rowLength +'">' +
        row +
        '</span>';
}

function drawNewRow(rowLength) {
    var ret ;
    ret = '<div class="kinoRow" id="kinoRow_' + rowCount + '" data-rowNumber="' + rowCount + '" data-rowLength="' + rowLength + '"><div class="row">' +
            '<div class="col-md-1">' + rowCount +
            '</div>' +
            '<div class="col-md-9" align="center">' +
                drawRowSeats(rowLength, rowCount ) +
            '</div>' +
            '<div class="col-md-2">' +
                '<button onclick="addSeat(' + rowCount +');">+</button>' +
                '<button onclick="delSeat(' + rowCount +');">-</button>' +
                '<button onclick="delRow(' + rowCount +');">0</button>' +
            '</div>' +
        '</div></div>';
 //   console.log(ret);

    $('#rows').append(ret);
    rowCount++;

}

function addSeat(n) {
    var oldLength = $('#rowSeats_' + n)[0].dataset.rowlength;
    $('#rowSeats_' + n).replaceWith(drawRowSeats(parseInt(oldLength)+1, n));
}

function delSeat(n) {
    var oldLength = $('#rowSeats_' + n)[0].dataset.rowlength;
    $('#rowSeats_' + n).replaceWith(drawRowSeats(parseInt(oldLength)-1, n));

}

function delRow(n) {
    $('#kinoRow_' + n).remove();

}