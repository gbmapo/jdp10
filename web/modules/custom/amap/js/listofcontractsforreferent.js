jQuery(document).ready(function ($) {

  $('#listofcontractsforreferent').DataTable({

    language: {
        url: 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json',
    },
    layout: {
      topStart: null,
      topEnd: null,
      bottomStart: null,
      bottomEnd: null,
    },
    info: false,
    ordering: false,
    paging: false,
    fixedHeader: true,
    columnDefs: [
      {targets: [1,2,3,5,6], className: 'dt-head-center'},
      {type: 'string', targets: 6 }
    ],
    initComplete: function () {
      this.api().columns([3, 4, 5]).every(function (i) {
        var column = this;
        var select = $('<select><option value=""></option></select>')
          .appendTo($(column.header()))
          .on('change', function () {
            var val = $.fn.dataTable.util.escapeRegex(
              $(this).val()
            );
            column
              .search(val ? '^' + val + '$' : '', true, false)
              .draw();
          });

        switch (i) {
          case 3:
          case 5:
            column.data().unique().sort().each(function (d, j) {
              select.append('<option value="' + d + '">' + d + '</option>');
            });
            break;
          case 4:
            var temp = {};
            column.data().unique().each(function (d, j) {
              var sText = $('<a/>').html(d).text();
              temp[sText] = '<option value="' + sText + '">' + sText + '</option>';
            });
            Object.keys(temp).sort().forEach(function (key) {
              select.append(temp[key]);
            });
            break;
          default:
        }
      });
    }
  });

});
