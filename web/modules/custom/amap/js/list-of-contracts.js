jQuery(document).ready(function ($) {

  $('#listofcontracts').DataTable({

    info: false,
    ordering: false,
    paging: false,
    searching: false,
    fixedHeader: true,
    columnDefs: [
      {targets: [1,2,4,5], className: 'dt-head-center'},
    ],
  });

});
