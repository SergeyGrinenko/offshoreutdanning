/**
 *  @typedef {function} videojs
 */

(() => {
    "use strict";

    const backdrop = document.querySelector('#modal-backdrop');
    document.addEventListener('click', modalHandler);

    function modalHandler(evt) {
        const modalBtnOpen = evt.target.closest('.js-modal');
        if (modalBtnOpen) {
            const modalSelector = modalBtnOpen.dataset.modal;
            showModal(document.querySelector(modalSelector));
        }

        const modalBtnClose = evt.target.closest('.modal-close');
        if (modalBtnClose) {
            evt.preventDefault();
            const videoElement = modalBtnClose.closest('.modal-window').querySelector('.video-js');
            handleVideoPause(videoElement);
            hideModal(modalBtnClose.closest('.modal-window'));
        }

        if (evt.target.matches('#modal-backdrop')) {
            const videoElement = evt.target.closest('.embed-course-video').querySelector('.video-js');
            handleVideoPause(videoElement);
            hideModal(document.querySelector('.modal-window.show'));
        }
    }

    function showModal(modalElem) {
        modalElem.classList.add('show');
        backdrop.classList.remove('hidden');
        const videoElement = modalElem.querySelector('.video-js');
        videoElement && videojs(videoElement.id).play();
    }

    function hideModal(modalElem) {
        modalElem.classList.remove('show');
        backdrop.classList.add('hidden');
        document.querySelector('header').style.zIndex = '10';
    }

    function handleVideoPause(videoElement) {
        if (videoElement) {
            const player = videojs(videoElement.id);
            player.pause();
            player.currentTime(0);
        }
    }
})();