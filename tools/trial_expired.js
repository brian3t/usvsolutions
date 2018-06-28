//2018-02-28 v4 Override promo code in trial-expired page - Product Backlog Item 96 
var DISCOUNT_PERCENT = 0.25;

$(document).ready(function() {
    // Split each array item into [key, value]
    var url_params = _.chain(location.search.slice(1).split('&'))
        .map(function(item) {
            if (item) return item.split('=');
        }) // ignore empty string if search is empty
        .compact() // Remove undefined in the case the search is empty
        .object() // Turn [key, value] arrays into object parameters
        .value(); // Return the value of the chain operation

    if (!url_params.hasOwnProperty('pc') || url_params.pc !== 'EXP32') {
        return false;
    }
    var promo_code = url_params.pc;
    var offer_text_el = $(':contains("SPECIAL OFFER"):last');
    if (offer_text_el.length === 1) {
        offer_text_el.text(offer_text_el.text().replace(new RegExp(/\$\d+/), 100 * DISCOUNT_PERCENT + '%'));
    }
    //now set all <script class=ppc-data> tags to store the new promocode. This is not really necessary
    var all_ppc = $('div[data-processed="true"][data-module="ui/portable-product-configurator"] script.ppc-data');//we have 3 such divs in the trial_expired page
    $.each(all_ppc, function(i, ppc){
        try {
            var data_string = JSON.parse($(ppc).text());
            $.each(data_string.devices, function(i, device) {
                $.each(device.boxes, function(i, box) {
                    box[i].promocode = promo_code;
                });
            });        
            $(ppc).text(JSON.stringify(data_string));
        } catch (e) {            console.error("Error parsing ppc data"); console.error(e);        }
    });

    //This function sets new discounted price within a box data-initialized="ui/portable-product-configurator"
    function update_price_override_promo_code(portable_product_configurator) {
        portable_product_configurator = $(portable_product_configurator);
        $.each(portable_product_configurator.find('div.ppc-box.duration'), function(i, ppc_box_el) {
            ppc_box_el = $(ppc_box_el);
            var flat_price = $(ppc_box_el.find('span.price.flat.obsolete'));
            if (flat_price.length === 0) {
                return;
            }
            flat_price = parseFloat(flat_price.data('price').replace('$', ''));
            if (isNaN(flat_price)) {
                return;
            } //e.g. flat_price = 69.99
            var discounted_price = Number(flat_price * (1 - DISCOUNT_PERCENT)).toFixed(2);
            var discounted_price_span = ppc_box_el.find('span.price').not('.flat');
            discounted_price_span.data('price', '$' + discounted_price).attr('data-price', '$' + discounted_price);
            discounted_price_span.html('<span class="currency">$</span>' + Math.floor(discounted_price) + '.<sup class="cents">' + Math.round((discounted_price - Math.floor(discounted_price)) * 100) + '</sup>');
        });
        $('a[data-event-category="Add to cart"]').data('promocode', promo_code).attr('data-promocode', promo_code); //make sure promocode is passed onto cart page
    }
    window.check_ppc_ready = setInterval(function() {
        if ($('div[data-module="ui/portable-product-configurator"] span.knob').length >= 1) {//wait for ppc to be initialized, i.e. when +- `Devices` buttons knobs show up
            $('span.knob').on('click', function(event) { //these are the +- `Devices` buttons
                $('div.ppc-price').hide();
                setTimeout(function() {
                    update_price_override_promo_code($(event.target).closest('div.ppc[data-initialized="ui/portable-product-configurator"]'));
                    $('div.ppc-price').show();
                }, 100); //make sure this happens after the internal price calculator is done
            });
            //trigger update price for the first time, when ppc loads
            update_price_override_promo_code('body');
            try {
                clearInterval(window.check_ppc_ready);
            } catch (e){}
        }
    }, 500);
});
