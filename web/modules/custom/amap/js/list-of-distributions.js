jQuery(document).ready(function ($) {

  $('#listofdistributions').DataTable({

    info: false,
    ordering: false,
    paging: false,
    searching: false,
    fixedHeader: true,
    responsive: {
      details: {
        display: $.fn.dataTable.Responsive.display.modal({
          header: function (row) {
            var data = row.data();
            return '<span style="font-size: 1.4rem;">Distribution du ' + data[0] + '</span>';
          }
        }),
        'renderer': function (api, rowIdx, columns) {
          var data = $.map(columns, function (col, i) {

            if (col.data) {
              if (col.title != 'Date') {
                return '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' + '<td><b>' + col.title + '</b></td></tr>';
              }
            }
            else {
              return '';
            }

          }).join('');

          if (data) {
            return $('<table/>').append(data);
          }
          else {
            return false;
          }
        }

      }
    }
  });

});
