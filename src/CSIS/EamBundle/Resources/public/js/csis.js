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
});
