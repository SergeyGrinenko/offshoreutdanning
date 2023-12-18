jQuery(document).ready(function ($) {
    function live_search_box() {

        $('.post_item').each(function () {
            $(this).attr('data-search-term', $(this).text().replace(/(kr)|([,-/\s]+)|(&nbsp;)/g, '').toLowerCase().trim());
        });

        $('.search-course-bar input').on('keyup', function () {
            var searchTerm = $(this).val().replace(/(kr)|([,-/\s]+)|(&nbsp;)/g, '').toLowerCase().trim();

            $('.filter_inputs').each(function (i) {
                $(this).find('.radio_btn').removeClass('active');
                $(this).find('input[value="alls"]').parent().parent().find('.radio_btn').addClass('active')
            });

            $('.post_item').each(function () {
                if ($(this).filter('[data-search-term *="' + searchTerm + '"]').length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('.search_item').each(function () {
                if (!$(this).find('.post_item:visible').length) {
                    $(this).find('.letter').hide();
                } else {
                    $(this).find('.letter').show();
                }
            });

            if ($('.search_item').children(':visible').length == 0 && $('.post_item:visible').length == 0) {
                $('.no-items').show();
            } else {
                $('.no-items').hide();
            }
            filterGuide()
        })
    }

    function filterGuide() {
        const el_filters = document.querySelectorAll('[name="fagomrade"], [name="krever"], [name="kurs"], [name="alls"]'),
            el_filterable = document.querySelectorAll('[data-filterable]');

        const applyFilter = () => {

            // Filter checked inputs
            const el_checked = [...el_filters].filter(el => el.checked && el.value);

            // Collect checked inputs values to array
            const filters = [...el_checked].map(el => el.value);

            // Get elements to filter
            const el_filtered = [...el_filterable].filter(el => {
                const props = el.getAttribute('data-filterable').trim().split(/\s+/);
                return filters.every(fi => props.includes(fi))
            });

            // Hide all
            el_filterable.forEach(el => el.classList.add('hide'));

            // Show filtered
            el_filtered.forEach(el => el.classList.remove('hide'));
        };

// Assign event listener
        el_filters.forEach(el => el.addEventListener('change', applyFilter));
// Init
        applyFilter();

        $('.filter_inputs').each(function () {

            $(this).find('label').click(function () {
                $(this).parent().parent().find('.radio_btn').removeClass('active');
                if ($(this).find('input').is(':checked')) {
                    $(this).parent().find('.radio_btn').addClass('active');
                }
            })
        });

        $('.categories-filter label').click(function () { // click on category

            // if ($(this).find('input').val() !== 'alls') {
            //     $('.filter_inputs').find('.count_posts').show(); // show counters if category !== All
            // }

            var parrent_cat = $(this).find('input').attr('name'); // get parrent category of element
            var count = $(this).parent().find('.count').data('count'); // get count posts in this category

            setTimeout(function () {  //set time out in 10ms for adding class 'active' to elements and start processing

                var inputs = [];
                var filterable = [];

                $('.search_item > div').each(function () { // get filtered posts
                    if (!$(this).hasClass('hide')) {
                        filterable.push($(this).data('filterable')); // push categories filtered posts to array
                    }
                });

                $('.categories-filter input').each(function () { // get all categories (filter inputs)
                    inputs.push(new Array($(this).val())); // push all of them to array
                });

                $(inputs).each(function () { // sorted all all categories (filter inputs)
                    var k = $(this); // get each of them
                    if (filterable.toString().indexOf(this[0]) === -1) { // if filtered posts don't have selected category
                        var key1 = $(this);
                        console.log('ok')

                        $('.filter_inputs > .filter_inputs__item').each(function () {
                            var filtred_parent_cat = $(this).find('input').attr('name');
                            var filtred_child_cat = $(this).find('input').val();

                            if (filtred_child_cat === key1[0] && filtred_parent_cat !== parrent_cat) {
                                $(this).addClass('disabled');
                                $(this).find('.count').text('0')
                            } else if (filtred_child_cat === 'alls' && filtred_parent_cat !== parrent_cat) {
                                $(this).find('.count').text(count)
                            }
                        })

                    } else {
                        var key2 = $(this);

                        var q = 0;
                        $(filterable).each(function () {
                            if (this.toString().indexOf(k[0]) > 1) {
                                q++
                            }
                        });

                        $('.filter_inputs > .filter_inputs__item').each(function () {
                            var filtred_parent_cat = $(this).find('input').attr('name');
                            var filtred_child_cat = $(this).find('input').val();

                            if (filtred_child_cat === key2[0] && filtred_parent_cat !== parrent_cat) {
                                $(this).removeClass('disabled');
                                var default_counter = $(this).find('.count').data('count');
                                $(this).find('.count').text(q);
                            } else if (filtred_child_cat === 'alls' && filtred_parent_cat !== parrent_cat) {
                                $(this).find('.count').text(count)
                            }
                        })
                    }
                })
            }, 10);
        });
    }

    function responsiveTable() {
        const tableEl = document.querySelector('table');
        if(tableEl){
            const thEls = tableEl.querySelectorAll('thead th');
            const tdLabels = Array.from(thEls).map(el => el.innerText);
            tableEl.querySelectorAll('tbody tr').forEach(tr => {
                Array.from(tr.children).forEach(
                    (td, ndx) => td.setAttribute('label', tdLabels[ndx])
                );
            });
        }
    }

    responsiveTable();
    filterGuide();
    live_search_box();
});