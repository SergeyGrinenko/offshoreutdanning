const PayerCompany = 'bussiness'
const PayerStudent = 'deltager';

function SetupLocalStorage() {
    const liElements = document.querySelectorAll('.item-id');
    const preSelectedPayer = document.querySelector(`input[name="betaler_type"]:checked`).value;
    liElements.forEach((liElement, index) => {
        const inputs = liElement.querySelectorAll('.members_wrap input, .members_wrap select');
        const storedData = localStorage.getItem(`participant_${index + 1}`);
        const userData = storedData ? JSON.parse(storedData) : {};

        inputs.forEach(input => {
            const inputId = input.id || input.name;

            input.addEventListener('input', () => {
                userData[inputId] = input.value;

                localStorage.setItem(`participant_${index + 1}`, JSON.stringify(userData));
                if (inputId === 'firstname' || inputId === 'lastname') {
                    const fullnameElement = liElement.querySelector('.col_form_header .fullname');
                    fullnameElement.textContent = `${userData['firstname'] || ''} ${userData['lastname'] || ''}`;
                    updateSelectOptions(liElements);

                    if (userData['firstname'] && userData['lastname']) {
                        if (!userData['location']) {
                            const selectLocation = liElement.querySelector('#location');
                            if (selectLocation) {
                                userData['location'] = selectLocation.options[0].value;
                                updateDataInLocalStorage(index + 1, userData);
                            }
                        } else {
                            updateDataInLocalStorage(index + 1, userData);
                        }
                    }
                }

                setupCheckoutData();
                if (index + 1 > 1 && preSelectedPayer === PayerStudent) {
                    if (userData['firstname'] && userData['lastname']) {
                        document.querySelector('.deltager_payer').classList.remove('hide');
                    }
                } else {
                    document.querySelector('.deltager_payer').classList.add('hide');
                }
            });

            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    userData[inputId] = input.value;
                    localStorage.setItem(`participant_${index + 1}`, JSON.stringify(userData));
                    setupCheckoutData();
                });
            }

            if (index + 1 > 1 && preSelectedPayer === PayerStudent) {
                if (userData['firstname'] && userData['lastname']) {
                    document.querySelector('.deltager_payer').classList.remove('hide');
                }
            } else {
                document.querySelector('.deltager_payer').classList.add('hide');
            }
        });

        const selectLocation = liElement.querySelector('#location');
        if (selectLocation) {
            selectLocation.addEventListener('change', () => {
                userData['location'] = selectLocation.value;
                // Update data in local storage only if both firstname and lastname are present
                if (userData['firstname'] && userData['lastname']) {
                    updateDataInLocalStorage(index + 1, userData);
                }
            });
        }
    });

    const selectDeltager = document.getElementById('select-deltager');
    if (selectDeltager) {
        const selectedOption = localStorage.getItem('participantPayer');
        if (selectedOption) selectDeltager.value = selectedOption;

        selectDeltager.addEventListener('change', function () {
            const newSelectedOption = this.value;
            localStorage.setItem('participantPayer', newSelectedOption);
            setupCheckoutData();
        });
    }
    fillFormFromLocalStorage();
}

async function updateParticipantCount() {
    document.getElementById('place_order').disabled = true;
    let members = document.querySelectorAll('.member li').length;
    if (members) await changeQuantity(members);
}

async function changeQuantity($qty = 1) {
    try {
        const response = await fetch(woocommerce_params.ajax_url, {
            method: 'POST',
            headers: {'Accept': 'application/json'},
            body: new URLSearchParams({
                'action': 'change_quantity',
                'quantity': $qty,
            }),
        });

        if (response.ok) {
            const responseData = await response.json();
            const checkoutElement = document.querySelector('.woocommerce-checkout');
            const tableElement = checkoutElement.querySelector('table.shop_table');
            if (tableElement) tableElement.outerHTML = responseData.cart_items;
            document.getElementById('place_order').removeAttribute('disabled');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function updateDataInLocalStorage(participantIndex, userData) {
    localStorage.setItem(`participant_${participantIndex}`, JSON.stringify(userData));
    setupCheckoutData();
}

function updateSelectOptions(liElements) {
    const selectElement = document.getElementById('select-deltager');

    if (selectElement) {
        const selectedOption = selectElement.value;
        if (!localStorage.getItem('participantPayer')) localStorage.setItem('participantPayer', 'participant_1');

        liElements.forEach((liElement, index) => {
            const storedData = localStorage.getItem(`participant_${index + 1}`);
            if (storedData) {
                const parsedData = JSON.parse(storedData);
                const optionValue = `${parsedData['firstname']} ${parsedData['lastname']}`;
                const optionId = `participant_${index + 1}`;
                const isSelected = optionValue === selectedOption;
                const existingOption = selectElement.options[index];

                if (existingOption) {
                    existingOption.value = optionId;
                    existingOption.text = optionValue;
                } else {
                    addOption(selectElement, optionValue, optionId, isSelected);
                }
            }
        });

        while (selectElement.options.length > liElements.length) {
            selectElement.remove(selectElement.options.length - 1);
        }
    }
}

function fillFormFromLocalStorage() {
    const selectElement = document.getElementById('select-deltager');
    const participantPayer = localStorage.getItem('participantPayer');
    const list = document.getElementById('ToggleCard');
    if (list) {
        const listItems = list.getElementsByClassName('item-id');
        selectElement.innerHTML = '';

        for (let i = 0; i < listItems.length; i++) {
            let listItem = listItems[i];
            let localStorageKey = 'participant_' + (i + 1);
            let localStorageData = JSON.parse(localStorage.getItem(localStorageKey));

            // Check if localStorageData exists
            if (localStorageData) {
                // Loop through each input element within the list item
                let inputs = listItem.querySelectorAll('.required');
                inputs.forEach(function (input) {
                    let fieldId = input.id;
                    // Populate the HTML elements with data from local storage
                    input.value = localStorageData[fieldId] || '';

                    if (fieldId === 'firstname' || fieldId === 'lastname') {
                        const fullnameElement = listItem.querySelector('.col_form_header .fullname');
                        fullnameElement.textContent = `${localStorageData['firstname'] || ''} ${localStorageData['lastname'] || ''}`;
                    }
                });

                if (selectElement) {
                    const optionValue = `${localStorageData['firstname']} ${localStorageData['lastname']}`;
                    const optionId = `participant_${i + 1}`;
                    addOption(selectElement, optionValue, optionId, participantPayer === optionId);
                }
            }
        }
    }
}

function addOption(selectElement, text, value, selected) {
    const option = document.createElement('option');
    option.text = text;
    option.value = value;
    if (selected) option.selected = true;
    selectElement.add(option);
}

async function removeDelegate(delegateElement) {
    const memberItemIds = document.querySelectorAll('.member .item-id');
    const selectDeltager = document.getElementById('select-deltager');
    const selectedOption = selectDeltager.value;
    if (memberItemIds.length > 1) {
        const closestItemId = closest(delegateElement, '.item-id');
        closestItemId.parentNode.removeChild(closestItemId);

        if (selectDeltager) {
            if (closestItemId) {
                const storedData = localStorage.getItem(`participant_${closestItemId.id}`);
                const parsedData = storedData ? JSON.parse(storedData) : {};
                const firstName = parsedData['firstname'];
                const lastName = parsedData['lastname'];

                if (firstName !== undefined && lastName !== undefined) {
                    reindexLocalStorage(closestItemId, selectedOption, selectDeltager);
                    updateSelectOptions(document.querySelectorAll('.member .item-id'), closestItemId);
                    initMembers(document.querySelector('.members_wrap'));
                }
            }
        }
    }
    await updateParticipantCount();
    setupCheckoutData();
}

function reindexLocalStorage(closestItemId, selectedOption, selectDeltager) {
    const liElements = document.querySelectorAll('.member .item-id');

    liElements.forEach((liElement, index) => {
        const userId = liElement.id;
        const oldUserData = localStorage.getItem(`participant_${userId}`);
        if (parseInt(closestItemId.id) > 1) localStorage.removeItem(`participant_${closestItemId.id}`);
        if (oldUserData) {
            localStorage.removeItem(`participant_${userId}`);
            localStorage.setItem(`participant_${index + 1}`, oldUserData);

            if (`participant_${closestItemId.id}` === selectedOption) {
                if (parseInt(closestItemId.id) === 1 && selectedOption === 'participant_1') {
                    if (parseInt(closestItemId.id) === 1) {
                        localStorage.setItem('participantPayer', `participant_${closestItemId.id}`);
                    } else {
                        localStorage.setItem('participantPayer', `participant_${index + 1}`);
                    }

                } else {
                    const firstOption = selectDeltager.options[0];
                    selectDeltager.value = firstOption ? firstOption.value : selectedOption;
                    localStorage.setItem('participantPayer', selectDeltager.value);
                }

            } else {
                if (selectedOption) {
                    let SelectedDataID = parseInt(selectedOption.replace(/\D/g, ''));
                    if (SelectedDataID > parseInt(closestItemId.id)) {
                        if (selectedOption !== `participant_${index + 1}`) {
                            localStorage.setItem('participantPayer', `participant_${index + 1}`);
                        } else {
                            localStorage.setItem('participantPayer', `participant_${index + 1}`)
                        }
                    }
                }
            }
        }
    });
}

function closest(el, selector) {
    while (el && !el.matches(selector)) {
        el = el.parentElement;
    }
    return el;
}


async function addDelegate(delegate) {
    const lastItemId = delegate.querySelector('.item-id:last-child');
    const newItem = lastItemId.cloneNode(true);
    const fullnameElement = newItem.querySelector(".fullname");
    const inputElement = newItem.querySelector("input");
    delegate.querySelector('.member').appendChild(newItem);
    inputElement.value = '';
    fullnameElement.textContent = '';
    initMembers(delegate);
    await updateParticipantCount();
    ToggleDelegate(newItem.querySelector('.col_form_header'))
}

function updateDelegate() {
    const keys = Object.keys(localStorage);
    const userData = {};

    keys.forEach(key => {
        const match = key.match(/^participant_([0-9])$/);

        if (match) {
            const userId = match[1];
            const attributeName = match[2];
            userData[userId] = userData[userId] || {};
            userData[userId][attributeName] = key;
        }
    });
    const delegate = document.querySelector('.members_wrap');
    if (delegate) {
        const lastItem = document.querySelector('.members_wrap .item-id:last-child');
        Object.entries(userData).forEach(([userId]) => {
            if (lastItem.id !== userId) {
                const newItem = lastItem.cloneNode(true);
                delegate.querySelector('.member').appendChild(newItem);
                ToggleDelegate(newItem.querySelector('.col_form_header'))
            }
        });

        initMembers(delegate);
    }

}

function initMembers(members) {
    let row_id = 1,
        index = 0;

    members.querySelectorAll('.member .item-id').forEach((item) => {
        item.id = row_id;
        item.querySelectorAll('input, select').forEach((element) => {
            if (element.tagName !== 'SELECT') element.value = '';
            element.id = element.id.replace(/([0-9]+)/g, row_id);
            element.name = element.name.replace(/([0-9]+)/g, row_id);
        });
        row_id++;
        index++;
    });

    SetupLocalStorage();
}

function setupPayerHandler() {
    function getSelectedValue() {
        let preSelectedValue = document.querySelector('input[name="betaler_type"]:checked').value;
        localStorage.setItem('payerType', preSelectedValue);
    }

    function setInitialValue() {
        let storedPayerType = localStorage.getItem('payerType');
        if (storedPayerType) {
            let selectedRadioButton = document.querySelector('input[name="betaler_type"][value="' + storedPayerType + '"]');
            if (selectedRadioButton) {
                selectedRadioButton.checked = true;
            }
        }
    }

    let radioButtons = document.querySelectorAll('input[name="betaler_type"]');
    radioButtons.forEach(radioButton => radioButton.addEventListener('change', getSelectedValue));

    setInitialValue();
    getSelectedValue();
}

function setupRadioButtonsHandler(groupName, storageKey) {
    function getSelectedValue() {
        const partCount = countOfParticipants();
        const preSelectedValue = document.querySelector(`input[name="${groupName}"]:checked`).value;
        localStorage.setItem(storageKey, preSelectedValue);

        if (preSelectedValue === PayerCompany) {
            document.querySelector('.deltager_payer').classList.add('hide');
            document.querySelector('.company_payer').classList.remove('hide');
            const requiredInputs = document.querySelectorAll('.required');
            requiredInputs.forEach(input => input.setAttribute('required', true));
        } else if (preSelectedValue === PayerStudent) {
            document.querySelector('.company_payer').classList.add('hide');
            if (partCount > 1) document.querySelector('.deltager_payer').classList.remove('hide');
            const requiredInputs = document.querySelectorAll('.company_payer .required');
            requiredInputs.forEach(input => input.removeAttribute('required'));
        }

        setupCheckoutData();
    }

    function setInitialValue() {
        const storedValue = localStorage.getItem(storageKey);
        if (storedValue) {
            const selectedRadioButton = document.querySelector(`input[name="${groupName}"][value="${storedValue}"]`);
            if (selectedRadioButton) {
                selectedRadioButton.checked = true;
                getSelectedValue(); // Trigger the logic on initial load
            }
        }
    }

    const radioButtons = document.querySelectorAll(`input[name="${groupName}"]`);
    radioButtons.forEach(button => button.addEventListener('change', getSelectedValue));

    setInitialValue();
    setupCheckoutData();
}

function countOfParticipants() {
    const keys = Object.keys(localStorage);
    const dataKeys = keys.filter(key => key.startsWith('participant_'));
    return dataKeys.length;
}

function handleFormData() {
    const storedData = localStorage.getItem('company_fields');
    const billingFields = storedData ? JSON.parse(storedData) : {};

    // Get all input fields with class 'required'
    const inputFields = document.querySelectorAll('.company_form input, .company_form select');

    // Loop through each input field
    inputFields.forEach((field) => {
        // Save data on input
        field.addEventListener('input', () => {
            billingFields[field.name] = field.value;
            localStorage.setItem('company_fields', JSON.stringify(billingFields));
            setupCheckoutData();
        });

        if (field.tagName === 'SELECT') {
            if (!billingFields['billing_country']) {
                billingFields[field.name] = field.options[field.selectedIndex].value;
                localStorage.setItem('company_fields', JSON.stringify(billingFields));
            }
            field.addEventListener('change', () => {
                billingFields[field.name] = field.value;
                localStorage.setItem('company_fields', JSON.stringify(billingFields));
                setupCheckoutData();
            });
        }

        // Fill the form on page load
        const savedData = localStorage.getItem('company_fields');
        if (savedData) {
            const savedFields = JSON.parse(savedData);
            if (savedFields[field.name]) {
                field.value = savedFields[field.name];
            }
        }
    });
}

function setupCheckoutData() {
    const checkoutDataInput = document.getElementById('checkout_data');
    let allDataObject = {};

    for (let i = 0; i < localStorage.length; i++) {
        let key = localStorage.key(i);
        allDataObject[key] = localStorage.getItem(key);
    }

    let jsonString = JSON.stringify(allDataObject);
    if (checkoutDataInput) {
        checkoutDataInput.value = jsonString;
    }
    if (document.getElementById('place_order'))
        document.getElementById('place_order').removeAttribute('disabled');
}

function toggleCouponForm() {
    document.querySelectorAll('.toggle-coupon').forEach(function (toggleCoupon) {
        toggleCoupon.addEventListener('click', function () {
            const buttonIcon = this.querySelector('.button-icon');
            const redeemCoupon = document.querySelector('.redeem-coupon');
            buttonIcon.classList.toggle('open');
            if (redeemCoupon.style.display === 'block') {
                redeemCoupon.style.display = 'none';
            } else {
                redeemCoupon.style.display = 'block';
            }
        });
    });
}

async function applyCoupon(code) {
    try {
        const response = await fetch(woocommerce_params.ajax_url, {
            method: 'POST',
            headers: {'Accept': 'application/json'},
            body: new URLSearchParams({
                'action': 'apply_coupon_code',
                'coupon': code,
            }),
        });

        if (response.ok) {
            const responseData = await response.json();
            if (responseData.success === true) document.querySelector('.woocommerce-checkout table.shop_table').outerHTML = responseData.response;
            document.querySelector('.coupon-message').innerHTML = responseData.message;
            document.querySelector('.coupon-message').style.display = 'block';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// function clearLocalStorage() {
//     if (window.location.pathname.indexOf("order-received") >= 0) {
//         localStorage.clear();
//     }
// }

function ToggleDelegate($header) {
    let header = document.querySelector('.member li .col_form_header');
    if ($header) header = $header

    if (header) {
        let isClickInProgress = false;
        header.addEventListener('click', function (e) {
            var isNotColButton = !e.target.classList.contains('col_button');
            if (isNotColButton) {
                if (isClickInProgress) return;
                let item = this.closest('.item-id');
                let body = item.querySelector('.col_form_body');
                let isVisible = body.classList.contains('show');
                isClickInProgress = true;
                item.classList.toggle('open');

                if (!isVisible) {
                    body.classList.add('show');
                    body.classList.remove('hide');
                } else {
                    body.classList.remove('show');
                    body.classList.add('hide');
                }
            }

            isClickInProgress = false;
        });
    }

}

window.onload = SetupLocalStorage;

document.addEventListener('click', async (event) => {
    const target = event.target;

    switch (true) {
        case target.classList.contains('add_member'):
            await addDelegate(target.parentElement);
            break;

        case target.classList.contains('col_button'):
            await removeDelegate(target);
            break;

        case target.matches('#apply-coupon'):
            let parentElement = target.parentElement;
            let codeInput = parentElement.querySelector('input#coupon');
            let code = codeInput.value;
            if (code) await applyCoupon(code);
            break;

        default:
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    if (window.location.pathname.indexOf("order-received") >= 0) {
        localStorage.clear();
    } else {
        SetupLocalStorage();
        updateDelegate();
        setupRadioButtonsHandler('betaler_type', 'payerType');
        setupRadioButtonsHandler('payment_method', 'paymentMethod');
        handleFormData();
        setupCheckoutData();
        await updateParticipantCount();
        toggleCouponForm();
        setupPayerHandler();
        ToggleDelegate()
    }


});