function change_location_ajax($element, $product_id, $location, $from, $to, $next5) {
    jQuery.ajax({
        url: woocommerce_params.ajax_url,
        type: 'post',
        data: {
            'action': 'change_course_location',
            'product_id': $product_id,
            'location': $location,
            'from': $from,
            'to': $to,
            'next5': $next5
        },
        success: function (response) {
            if (response) {
                jQuery($element.closest('.grouped_courses').find('.body-dates.main')).html(jQuery.parseJSON(response).main);
                jQuery($element.closest('.grouped_courses').find('.body-dates.next5')).html(jQuery.parseJSON(response).next5);
            }
        }
    });
}

function groupedAddToCart($product_id, $courses) {

    var data = [],
        variation_id = {};

    if (document.getElementsByClassName("practice-variation")) {
        jQuery('.practice-variation').find('input:checked').each(function (i) {
            variation_id[jQuery(this).data('variation_id')] = jQuery(this).data('variation_id');
        });
    }

    jQuery('.mini-cart-item[data-id="' + $product_id + '"]').css({'filter': 'brightness(0.5)', 'transition': '.3s'})

    jQuery.each($courses, function (index, value) {
        data.push({
            'course_id': value.id,
            'course_year': value.year,
            'course_month': value.month,
            'course_type': value.type,
            'course_index': value.index,
            'group_type': value.group_type,
            'variation_id': variation_id
        })
    });

    jQuery.ajax({
        url: woocommerce_params.ajax_url,
        type: 'post',
        data: {
            'action': 'course_add_to_cart',
            'product_id': $product_id,
            'data': JSON.stringify(data)
        },
        success: function (response) {
            if (response) {
                if (JSON.parse(response).qty > 0) {
                    jQuery('.mini-cart-wrapper').parent().removeClass('hidden');
                }

                jQuery('.mini-cart-container .cart-content').html(JSON.parse(response).cart_items);
                jQuery('.mini-cart-wrapper .mini-items-data').html(JSON.parse(response).header_info);
            }
        }
    })
}

jQuery(document).ready(function ($) {
    $('.grouped_courses .select-course-locations select').change(function () {
        var product_id = $(this).closest('.grouped_courses').data('product-id'),
            location = $(this).val(),
            from = $(this).closest('.grouped_courses').data('from'),
            to = $(this).closest('.grouped_courses').data('to'),
            next5 = $(this).closest('.grouped_courses').data('next5');
        $(this).closest('.grouped_courses').find('.body-dates > div').css({
            'filter': 'opacity(0.3)',
            'transition': '.2s'
        });
        change_location_ajax($(this), product_id, location, from, to, next5);
    });

    $(document).on('click', ".grouped_courses .pamelding", function () {
        var product_id = $(this).closest('.grouped_courses').data('product-id'),
            courses = [];
        $(this).closest('.body-dates').find('.grouped-course-item').each(function () {
            courses.push($(this).data());
            $(this).addClass('active');
        });
        groupedAddToCart(product_id, courses);
        if (!jQuery('.mini-cart-wrapper .mini-cart-container').hasClass('cart-active')) {
            ToggleCart();
        }
    });
});