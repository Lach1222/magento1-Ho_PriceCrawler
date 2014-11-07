jQuery(document).ready(function() {
    // Show tooltip when hovering URL of message in logs grid
    jQuery('.log-url, .log-message').hover(function() {
        var el = jQuery(this);
        var isMessage = el.hasClass('log-message');
        var $url = el.attr(isMessage ? 'data-content' : 'href');

        if (el.parent().find('.tooltip').length <= 0) {
            el.parent().append('<span class="tooltip ' + (isMessage ? '' : 'no-wrap') + '" style="display:none;">' + $url + '</span>');
        }

        var tooltip = el.next();

        tooltip.toggle();
    });
});

/**
 * Search for product matches on edit form
 * @param url
 */
searchMatches = function(url) {
    var searchQuery = $('search_string').getValue();
    url = url + 'query/' + searchQuery;

    new Ajax.Request(url, {
        method: 'get',
        onSuccess: function(transport) {
            var response = transport.responseText || 'no response text';

            var errorEl = $('search-error');
            errorEl.update('');

            try {
                var jsonResponse = JSON.parse(response);
            }
            catch (e) {
                errorEl.update('<span>' + 'An error occured, please change your search query: <br />'  + response + '</span>');
            }

            jsonResponse.each(function(s) {
                var matches = s['matches'];
                var siteId = s['site_id'];

                $('result-query').update(searchQuery);
                var dropdown = $('product_match_' + siteId);
                dropdown.update('');

                matches.each(function(optgroup) {
                    if (optgroup instanceof Object) {
                        var optgroupEl = document.createElement('optgroup');
                        optgroupEl.label = optgroup['label'] != undefined ? optgroup['label'] : '';

                        var options = optgroup['value'];
                        options.each(function(option) {
                            var opt = document.createElement('option');
                            opt.text = option['label'];
                            opt.value = option['value'];
                            optgroupEl.insert(opt);
                        });
                        dropdown.insert(optgroupEl);
                    }
                    else {
                        var option = document.createElement('option');
                        option.value = 0;
                        dropdown.insert(option);
                    }

                    // Hide current selected product info
                    $$('.selected_info').each(function(el) {
                        el.addClassName('hidden');
                    });
                });
            });
        },
        onFailure: function() { alert('Something went wrong'); }
    });
};

/**
 * Show selected product info
 * @param url
 * @param siteId
 */
selectedProductInfo = function(url, siteId) {
    var infoEl = $('selected_info_' + siteId);
    var selectedProductId = $('product_match_' + siteId).getValue();
    if (selectedProductId === 'manual' || selectedProductId === '0') {
        infoEl.addClassName('hidden');
    }
    else {
        url = url + 'id/' + selectedProductId;
        new Ajax.Request(url, {
            method: 'get',
            onSuccess: function(transport) {
                var response = transport.responseText || 'no response text';
                response = JSON.parse(response);

                infoEl.down('.product_identifier').update(response['product_identifier']);
                infoEl.down('.product_name').update(response['name']);
                infoEl.down('.product_price').update(response['price']);
                infoEl.down('.product-image').setAttribute('src', response['image']);
                infoEl.down('.product-url').setAttribute('href', response['url']);

                infoEl.removeClassName('hidden');
            },
            onFailure: function() { alert('Something went wrong'); }
        });
    }
};