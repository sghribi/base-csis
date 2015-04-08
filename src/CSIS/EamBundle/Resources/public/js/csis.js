/**
 * CSIS JS utils
 */

$(document).ready(function() {
    // Enable tooltips
    $("[data-toggle='tooltip']").tooltip();


    // List helper
    Handlebars.registerHelper('list', function(items, options) {
        var out = "<ul class='inline'>";

        for(var i=0, l=items.length; i<l; i++) {
            out = out + "<li>" + options.fn(items[i]) + "</li>";
        }

        return out + "</ul>";
    });

    // Enable search bar
    var $searchBar = $('#search-bar-input');
    $searchBar.CSISSearch();

    // Enable datatable
    $('.csis-datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.6/i18n/French.json"
            },
            "iDisplayLength": 25
        }
    );

    $('.csis-datatable-small').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.6/i18n/French.json"
            },
            "iDisplayLength": 10,
            "bFilter" : false,
            "bLengthChange": false
        }
    );
});
