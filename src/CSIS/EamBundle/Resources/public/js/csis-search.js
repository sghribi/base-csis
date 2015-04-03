/**
 * CSIS "search" plugin
 * Handles like/dislike button groups on posts
 *
 * =============================================================================
 *
 * Usage :
 *   $('#idInputSearch').CSISSearch();
 */

(function ($)
{
    $.fn.extend({
        CSISSearch: function (opt)
        {
            var typeahead;
            var input = $(this);
            var equipmentByNameSearchLimit = input.data('search-equipement-name-limit');
            var equipmentByTagsSearchLimit = input.data('search-equipement-tags-limit');
            var defer = 0;
            var options = {
                highlight: true,
                hint: true,
                minLength: 2
            };

            function generateBloodhound (url, limit) {
                return new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    limit: limit,
                    remote: {
                        url: url,
                        replace: function(url, query) {
                            return url + "#" + query; // Hack to prevent data from being cached. `cache: false` doesn't work.
                        },
                        ajax: {
                            type: 'GET',
                            data: {
                                query: function(){
                                    return input.val();
                                }
                            },
                            context: this,
                            beforeSend: function() {
                                $('.search-spinner').show();
                            },
                            complete: function() {
                                $('.search-spinner').hide();
                            }
                        }
                    }
                });
            }

            /**
             * In order to defer the action in order to select one suggestion after opening the menu
             */
            function deferSearchResults() {
                if (!defer) {
                    defer = window.setTimeout(function () {
                        var cursor = $('.tt-suggestion.tt-cursor');
                        if (!cursor.size()) {
                            $('.tt-suggestion').first().addClass('tt-cursor');
                        }
                        defer = 0;
                    }, 0);
                }
            }

            //- Search for equipments by name
            var equipmentsByName = generateBloodhound(Routing.generate('equipment_by_name_quick_search'), equipmentByNameSearchLimit);
            equipmentsByName.initialize();

            //- Search for equipments by tags
            var equipmentsByTags = generateBloodhound(Routing.generate('equipment_by_tags_quick_search'), equipmentByTagsSearchLimit);
            equipmentsByTags.initialize();

            var header = {
                name: 'header',
                source: function(header, cb) { return cb([]); },
                displayKey: function(value) { return value; },
                templates: {
                    empty: function (query) {
                        var url = Routing.generate('search_results', { query: query.query });
                        return  '<a class="tt-suggestion no-result" href="' + url + '">Rechercher "' + query.query + '" dans la base<i class="search-spinner fa fa-fw fa-spinner fa-pulse"></i></a>';
                    }
                }
            };
            var equipmentByNameSources = {
                name: 'equipment-by-name',
                displayKey: function(data) {
                    return data.designation;
                },
                source: equipmentsByName.ttAdapter(),
                templates: {
                    suggestion: function (data) {
                        deferSearchResults();
                        url = Routing.generate('equipment_show', { id: data.id });
                        template =  Handlebars.compile($('#equipment-by-name-suggestion').html());
                        return template({data: data, url: url});
                    }
                }
            };
            var equipmentByTagsSources = {
                name: 'equipment-by-tags',
                displayKey: function(data) {
                    return data.designation;
                },
                source: equipmentsByTags.ttAdapter(),
                templates: {
                    suggestion: function (data) {
                        deferSearchResults();
                        url = Routing.generate('equipment_show', { id: data.id });
                        template =  Handlebars.compile($('#equipment-by-tags-suggestion').html());
                        return template({data: data, url: url});
                    }
                }
            };

            //- Init autocomplete input
            typeahead = input.typeahead (
                options,
                header,
                equipmentByNameSources,
                equipmentByTagsSources
            );

            typeahead.on('focus select', function() {
                    $(this).keyup();
                }
            ).on('typeahead:selected', function(e) {
                    selected = $('.tt-suggestion.tt-cursor').children();
                    if (selected.size() > 0) {
                        window.location = selected.attr('href');
                    } else {
                        window.location = $('.no-result').attr('href');
                    }
                }
            );
        }
    });
}
)(jQuery);
