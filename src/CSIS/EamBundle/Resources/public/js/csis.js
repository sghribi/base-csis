/**
 * CSIS JS utils
 */

$(document).ready(function() {
    // Enable search bar
    var $searchBar = $('#search-bar-input');
    $searchBar.CSISSearch();

    // Enable tooltips
    $("[data-toggle='tooltip']").tooltip();
});