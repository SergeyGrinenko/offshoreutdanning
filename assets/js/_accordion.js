function Accordion() {

    let Accordion = function (el, multiple) {
        this.el = el || {};
        this.multiple = multiple || false;
        if (this.el) {

        }
        let links = this.el.querySelectorAll('.category-name');
        if (links) {
            links.forEach(function (link) {

                link.addEventListener('click', function (e) {

                    e.preventDefault();
                    let el = e.currentTarget.parentElement;

                    let isOpen = el.classList.contains('open');

                    el.classList.toggle('open', !isOpen);
                    console.log(el)
                    // el.classList.toggle('open');
                    el.querySelector('.accordion-content').classList.toggle('show');

                    el.querySelectorAll('.accordion-content > ul').forEach(function (item) {
                        let parentLi = item.querySelector('li');
                        if (parentLi.firstChild.nodeValue) {
                            if (parentLi.querySelector('ul')) {
                                let textContent = parentLi.firstChild.nodeValue.trim();
                                let spanElement = document.createElement("span");

                                spanElement.textContent = textContent;
                                spanElement.classList.add('inner_accordion', 'text-decoration');
                                parentLi.removeChild(parentLi.firstChild);
                                parentLi.insertBefore(spanElement, parentLi.firstChild);

                                parentLi.querySelectorAll('ul').forEach(function (liElement) {
                                    liElement.style.display = 'none';
                                    parentLi.addEventListener('click', function () {
                                        parentLi.querySelector('ul').style.display = (parentLi.querySelector('ul').style.display === 'none' || parentLi.querySelector('ul').style.display === '') ? 'block' : 'none';
                                    });
                                });
                            }
                        }
                    })
                });
            });
        }
    };

    if (document.getElementById('accordion'))
        new Accordion(document.getElementById('accordion'), false);
}

document.addEventListener("DOMContentLoaded", function () {
    Accordion();
});

