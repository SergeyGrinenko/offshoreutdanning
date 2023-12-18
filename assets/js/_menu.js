function toggleMegaMenu(element) {
    const isActive = element.classList.toggle('active');
    const megaMenu = element.querySelector('.custom-mega-menu');

    closeOtherMenus(element);

    if (isActive) {
        showMenu(element, megaMenu);
    } else {
        megaMenu.style.height = '0';
    }
}

function closeOtherMenus(currentElement) {
    document.querySelectorAll('#menu-header li').forEach(item => {
        if (item !== currentElement) {
            item.classList.remove('active');
            const otherMenu = item.querySelector('.custom-mega-menu');
            if (otherMenu) {
                otherMenu.classList.remove('show');
                otherMenu.style.height = '0';
            }
        }
    });
}

function showMenu(element, megaMenu) {
    megaMenu.classList.add('show');
    const isSingleProduct = document.querySelector('.single-product #menu-header li') !== null;
    if (isSingleProduct && element.classList.contains('active'))
        setStyle(document.querySelector('.single-product .page-header'), 'background', '#eae8e3');

    element.classList.contains('posts-grid') ? buildGridArea(element) : buildMenuGrid(element);
    megaMenu.style.height = calcMenuHeight(element);
}

function calcMenuHeight(element) {
    const padding = 0;
    const mainIndent = 45 + padding;
    return element.querySelector('.dropdown-menu').offsetHeight + mainIndent + 'px';
}

function buildGridArea(element) {
    const liElements = element.querySelectorAll('.custom-mega-menu .dropdown-menu > li');
    const length = liElements.length;

    if (length > 4) {
        const num = (length - 1).toString();
        liElements[liElements.length - 2].style.gridArea = '1/2/' + num + '/4';
        liElements[liElements.length - 1].style.gridArea = '1/3/' + num + '/4';
    }
}

function buildMenuGrid(element) {
    const megaMenu = element.querySelector('.custom-mega-menu');
    const liElements = megaMenu.querySelectorAll('.dropdown-menu li');
    const length = liElements.length;
    const dropdownMenu = megaMenu.querySelector('.dropdown-menu');

    if (length > 2 && length < 4) {
        setGridProperties(dropdownMenu, 'repeat(2, 1fr)', '20px');
        liElements.forEach(item => setGridProperties(item.querySelector('.menu__sub-item'), '160px 1fr'));
    } else if (length > 3) {
        liElements.forEach(item => {
            const subItem = item.querySelector('.menu__sub-item');
            if (subItem) {
                setDisplay(subItem.querySelector('p'), 'none');
                setStyle(subItem.querySelector('h3'), 'text-align', 'center', ['my-3'], ['my-0']);
                setGridProperties(dropdownMenu, 'repeat(4, 1fr)', '3px');
                setGridProperties(subItem, '1fr');
                setStyle(subItem.querySelector('img'), 'max-height', '180px');
            }
        });
    }
}

function setGridProperties(element, columns, gap) {
    if (element) {
        Object.assign(element.style, { gridTemplateColumns: columns, gap: gap });
    }
}

function setDisplay(element, value) {
    if (element) element.style.display = value;
}

function setStyle(element, property, value, removeClasses = [], addClasses = []) {
    if (element) {
        Object.assign(element.style, { [property]: value });
        element.classList.remove(...removeClasses);
        element.classList.add(...addClasses);
    }
}

function resizeMenu() {
    const body = document.body;
    const headerRight = document.querySelector('.header-right');

    if (body.classList.contains('single-product') && !body.classList.contains('cart_item')) {
        if (window.innerWidth >= 1059) {
            const ww = window.innerWidth;
            const container = document.querySelector('.container').offsetWidth;
            const thumbnail = document.querySelector('.single-course .course-title-wrapper img').offsetWidth;

            headerRight.style.width = ((ww - container) / 2 + thumbnail) + 'px';
        } else {
            headerRight.style.width = 'auto';
        }
    } else {
        headerRight.style.width = 'auto';
    }
}

resizeMenu();
document.querySelectorAll('#menu-header .dropdown').forEach(dropdown => dropdown.addEventListener('click', () => toggleMegaMenu(dropdown)));
const t=document.querySelector('.toggle-menu');
t.addEventListener('click',()=>{document.querySelectorAll('.page-header, .toggle-menu').forEach(e=>e.classList.toggle('open'));document.body.classList.toggle('open-menu')});
window.addEventListener("resize", function () {resizeMenu();});