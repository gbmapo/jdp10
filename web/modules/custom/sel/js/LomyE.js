jQuery(document).ready(function ($) {

  $('#listofmyexchanges').DataTable({

    info: false,
    ordering: false,
    paging: false,
    searching: false,
    fixedHeader: true,
    columnDefs: [
      {targets: [0], className: 'dt-head-center'},
      {targets: [4], className: 'dt-head-right'},
    ],
  });

});
