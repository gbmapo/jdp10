jQuery(document).ready(function ($) {

  $('#listofmembersandpersons').DataTable({

    info: false,
    ordering: false,
    paging: false,
    searching: false,
    fixedHeader: true,
    columnDefs: [
      {targets: [3,6], className: 'dt-head-center'},
    ],
  });

});
