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

    // Enable internationalization support in the sorting
    customStringPre = function(data) {
    data = data[0];
    normalized = ! data ?
        '' :
        typeof data === 'string' ?
            data
                .replace( /έ/g, 'ε')
                .replace( /ύ/g, 'υ')
                .replace( /ό/g, 'ο')
                .replace( /ώ/g, 'ω')
                .replace( /ά/g, 'α')
                .replace( /ί/g, 'ι')
                .replace( /ή/g, 'η')
                .replace( /\n/g, ' ' )
                .replace( /á/g, 'a' )
                .replace( /é/g, 'e' )
                .replace( /è/g, 'e' )
                .replace( /ê/g, 'e' )
                .replace( /É/g, 'E' )
                .replace( /È/g, 'E' )
                .replace( /Ê/g, 'E' )
                .replace( /í/g, 'i' )
                .replace( /ó/g, 'o' )
                .replace( /ú/g, 'u' )
                .replace( /ê/g, 'e' )
                .replace( /î/g, 'i' )
                .replace( /ô/g, 'o' )
                .replace( /è/g, 'e' )
                .replace( /ï/g, 'i' )
                .replace( /ü/g, 'u' )
                .replace( /ã/g, 'a' )
                .replace( /õ/g, 'o' )
                .replace( /ç/g, 'c' )
                .replace( /ì/g, 'i' ) :
            data;

        var _empty = function ( d ) {
            return !d || d === true || d === '-' ? true : false;
        };

        return _empty(normalized) ?
            '' :
            typeof normalized === 'string' ?
                normalized.toLowerCase() :
                ! normalized.toString ?
                    '' :
                    normalized.toString();
    };
    $.fn.dataTableExt.oSort[ "string-pre" ] = customStringPre;

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
