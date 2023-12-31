/**
 *  @typedef {Object} woocommerce_params
 *  @typedef {Object} responseData
 *  @typedef {Object} response
 *  @property {string} ajax_url
 *  @property {string} qty
 *  @property {string} cart_items
 *  @property {string} header_info
 */

const miniCartContainer = document.querySelector('.mini-cart-container .cart-content');
const miniItemsData = document.querySelector('.mini-cart-wrapper .mini-items-data');
const miniCartWrapper = document.querySelector('.mini-cart-wrapper');
const courseCalendarDates = document.querySelector('.course-calendar .calendar-dates');
const product_id = document.querySelector('.single-course').id;
const modal = document.querySelector(".modal-cart");
const trigger = document.querySelector(".trigger-cart");
const closeButton = document.querySelector(".close-cart");

document.addEventListener("DOMContentLoaded", function () {
    const calendarDates = document.querySelector('.course-calendar .calendar-dates');
    let lastClickTime = 0;

    if (calendarDates) {
        calendarDates.addEventListener("click", async function (event) {
            const currentTime = new Date().getTime();
            if (currentTime - lastClickTime > 300) {
                const calendarItem = event.target.closest('.calendar-item');
                if (calendarItem && !event.target.closest('.calendar-item__link')) {
                    try {
                        await addToCart(calendarItem);
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                    }
                }
                lastClickTime = currentTime;
            }
        });
    }

    const practiceVariationInputs = document.querySelectorAll('.practice-variation-wrapper input[type="checkbox"]');
    practiceVariationInputs.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            let practices = localStorage.getItem('practices') ? JSON.parse(localStorage.getItem('practices')) : {};
            calculatePricePractise(this, practices, product_id);
        });
    });

    const singleCourse = document.querySelector('.single-course');
    document.addEventListener('click', async function (e) {
        const loadMoreButton = e.target.closest('.load-more-dates');
        if (loadMoreButton) {
            try {
                await load_more_courses(singleCourse.id, singleCourse.dataset.location);
            } catch (error) {
                console.error(error);
            }
        }
    });

    if (document.querySelector('.practise-wrapper')) checkPractises();
});

async function addToCart(courseParent) {
    const course_id = courseParent.id;
    const course_year = courseParent.dataset.year;
    const course_month = courseParent.dataset.month;
    const course_type = courseParent.dataset.type;
    const course_index = courseParent.dataset.index;
    const variation_id = getVariationId(product_id);

    document.querySelectorAll('.single-product').forEach(p => {
        p.classList.add('cart_item');

        const header = p.querySelector('header');
        if (header) {
            header.classList.remove('dynamic');
        }
    });
    resizeMenu();
    document.querySelector('.single-product').classList.add('cart_item');

    const miniCartItem = document.querySelector(`.mini-cart-item[data-id="${product_id}"]`);
    if (miniCartItem) miniCartItem.classList.add('filter-brightness-transition');

    try {
        const [response] = await Promise.all([
            fetch(woocommerce_params.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'course_add_to_cart',
                    'product_id': product_id,
                    'course_id': course_id,
                    'course_year': course_year,
                    'course_month': course_month,
                    'course_type': course_type,
                    'course_index': course_index,
                    'variation_id': JSON.stringify(variation_id)
                }),
            }),
        ]);

        if (response.ok) {
            const responseData = await response.json();
            showCartModal();
            updateMiniCart(responseData);
            if (responseData.qty > 0) miniCartWrapper.parentElement.classList.remove('hidden');
            chooseCourse(courseParent);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function getVariationId($product_id) {
    let variation_id = localStorage.getItem('practices') ? JSON.parse(localStorage.getItem('practices')) : new Object({});
    if (Object.keys(variation_id).length !== 0) {
        const checkedInputs = document.querySelectorAll('.practice-variation input:checked');
        checkedInputs.forEach(input => {
            variation_id[$product_id][input.dataset.variation_id] = input.dataset.variation_id;
        });
    }
    return variation_id;
}


function updateMiniCart(response) {
    miniCartContainer.innerHTML = response.cart_items;
    miniItemsData.innerHTML = response.header_info;
}

function showCartModal() {
    document.querySelector('.modal-cart').classList.add("show-modal");
}

function chooseCourse(courseParent) {
    const activeCalendarItem = courseCalendarDates.querySelector('.calendar-item.active');
    if (activeCalendarItem) activeCalendarItem.classList.remove('active');
    courseParent.classList.add('active');
}



function windowOnClick(event) {
    const isCloseButtonClick = event.target.classList.contains("close-cart");
    if (event.target === modal || event.target === closeButton || isCloseButtonClick) modal.classList.remove("show-modal");
}

// trigger.addEventListener("click", toggleModal);
window.addEventListener("click", windowOnClick);

document.addEventListener('click', async function (e) {
    const loadMoreButton = e.target.closest('.load-more-dates');
    if (loadMoreButton) {
        const singleCourse = document.querySelector('.single-course');
        try {
            await load_more_courses(singleCourse.id, singleCourse.dataset.location);
        } catch (error) {
            console.error(error);
        }
    }
});

async function load_more_courses(product_id, location = null, summ_variations = null, grouped = null, filter = null) {
    const elements = document.getElementsByClassName("single-course");
    if (elements.length > 0) {
        let element = elements[0];
        let dataLocation = element.getAttribute("data-location");
        if (dataLocation) filter = dataLocation;
    }

    const formData = new URLSearchParams();
    formData.append('action', 'load_more_courses');
    formData.append('product_id', product_id);
    formData.append('location', location);
    formData.append('summ_variations', summ_variations);
    formData.append('grouped', grouped);
    formData.append('filter', filter);

    document.querySelector(".load-more-dates").classList.remove('text-decoration');
    document.querySelector(".load-more-dates").innerHTML = '<div class="default-preloader"></div>';

    if (formData) {
        try {
            const response = await fetch(woocommerce_params.ajax_url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData,
            });

            if (response.ok) {
                const responseData = await response.text();
                const singleCourseTable = document.querySelector('.singlecourse__table');
                const singleCourseResponsibility = document.querySelector('.singlecourse__responsibility');
                const calendarDates = document.querySelector('.calendar-dates');

                if (singleCourseTable) singleCourseTable.classList.remove('expand');
                if (singleCourseResponsibility) singleCourseResponsibility.classList.remove('expand');

                calendarDates.innerHTML = responseData;

                const loadMoreDatesButton = document.querySelector(".load-more-dates");
                if (loadMoreDatesButton) loadMoreDatesButton.remove();

                const practices = localStorage.getItem('practices');
                if (practices) {
                    const parsedData = JSON.parse(practices)[product_id];
                    let additional_price = Object.values(parsedData).reduce((sum, item) => sum + item.price, 0);
                    if (parsedData && Object.keys(parsedData).length !== 0) {
                        updateCoursesPrices(additional_price, true)
                    } else {
                        updateCoursesPrices(additional_price, false)
                    }

                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}

function calculatePricePractise(variation, practices, product_id) {
    const additional_price = Number(variation.value);
    const status = variation.checked;
    const variation_id = variation.getAttribute('data-variation_id');

    if (!practices[product_id]) practices[product_id] = {};

    if (status) {
        practices[product_id][variation_id] = {
            'id': variation.id,
            'price': additional_price
        };
    } else {
        const keyToRemove = variation.getAttribute('data-variation_id');
        let storedData = JSON.parse(localStorage.getItem("practices")) || {};
        storedData = storedData[product_id] || {};

        if (keyToRemove in storedData) {
            delete storedData[keyToRemove];
            practices[product_id] = storedData;

            if (Object.keys(practices[product_id]).length === 0) {
                delete practices[product_id];
            }
        }
    }

    updateCoursesPrices(additional_price, status);
    localStorage.setItem('practices', JSON.stringify(practices));
    if (Object.keys(practices).length === 0) localStorage.removeItem('practices');
}

function updateCoursesPrices(additional_price, status) {
    if (additional_price) {
        const calendarItems = document.querySelectorAll('.course-calendar .calendar-item__price');
        calendarItems.forEach(function (item) {
            let match = item.textContent.match(/([0-9]+[\s+][0-9]+)|([0-9]+)/g);
            if (match) {
                let price = parseInt(match[0].replace(/(\s+)/g, '')) + additional_price;
                if (status !== true)
                    price = parseInt(match[0].replace(/(\s+)/g, '')) - additional_price;
                item.textContent = item.textContent.replace(/([0-9]+[\s+][0-9]+)|([0-9]+)/g, price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
            }
        });
    }

}

function checkPractises() {
    const practices = localStorage.getItem('practices');
    if (practices) {
        const parsedData = JSON.parse(practices)[product_id];
        let additional_price = 0;
        if (parsedData && Object.keys(parsedData).length !== 0) {
            for (const variationId in parsedData) {
                if (parsedData.hasOwnProperty(variationId)) {
                    const id = parsedData[variationId].id;
                    const radio = document.querySelector(`input[type="radio"][value="${id}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                    additional_price = Object.values(parsedData).reduce((sum, item) => sum + item.price, 0);

                    const checkbox = document.querySelector(`input[type="checkbox"][data-variation_id="${variationId}"][id="${id}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }

                    const hideElements = document.querySelectorAll(`.option[id="${id}"]`);
                    hideElements.forEach(element => {
                        element.classList.remove('hide');
                    });

                    document.querySelector('.practice-type-message div').classList.add('hide');
                    if (document.querySelector('.practice-type-message div[id="' + id + '"]'))
                        document.querySelector('.practice-type-message div[id="' + id + '"]').classList.remove('hide');
                }
            }
            updateCoursesPrices(additional_price, true)
        } else {
            updateCoursesPrices(additional_price, false)
        }
    }
    if (!localStorage.getItem('practices'))
        document.querySelector('.practise-type input[type="radio"][value="no-practise"]').checked = true;
}

document.querySelectorAll('.practise-type input[type="radio"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        const practices = localStorage.getItem('practices');

        document.querySelector('.practice-type-message div').classList.add('hide');
        if (document.querySelector('.practice-type-message div[id="' + this.value + '"]'))
            document.querySelector('.practice-type-message div[id="' + this.value + '"]').classList.remove('hide');

        if (practices) {
            const parsedData = JSON.parse(practices)[product_id];
            if (parsedData) {
                let additional_price = Object.values(parsedData).reduce((sum, item) => sum + item.price, 0);
                updateCoursesPrices(additional_price, false)
            }

            const localStorageData = JSON.parse(practices);
            if (localStorageData) {
                const productIdToRemove = product_id;
                if (localStorageData[productIdToRemove]) {
                    delete localStorageData[productIdToRemove];

                    if (Object.keys(localStorageData).length === 0) {
                        localStorage.removeItem('practices');
                    } else {
                        localStorage.setItem('practices', JSON.stringify(localStorageData));
                    }
                }
            }
        }

        const checkboxes = document.querySelectorAll('.practice-variation input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        const practiceId = this.value;
        document.querySelectorAll('.practice-variation .option').forEach(function (option) {
            option.classList.add('hide');
            if (practiceId === option.id) {
                option.classList.remove('hide');
            }
        });
    });
});