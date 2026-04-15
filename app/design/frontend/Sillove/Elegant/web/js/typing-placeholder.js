define(['jquery', 'domReady!'], function ($) {
    'use strict';

    const texts = [
        "Silver Ring",
        "Silver Jewellery Set ",
        "Silver Brecelet & Kada"
    ];

    $('.animate-input').each(function () {

        let index = 0;
        let charIndex = 0;
        let isDeleting = false;
        let cursorVisible = true;
        let $input = $(this);
        let typingTimeout;

        function getCursor() {
            return cursorVisible ? '|' : '';
        }

        setInterval(() => {
            cursorVisible = !cursorVisible;
        }, 500);

        function typeEffect() {

            // Prevent conflict with Magento typing
            if ($input.val().length > 0) return;

            let currentText = texts[index];

            if (isDeleting) {
                charIndex--;
            } else {
                charIndex++;
            }

            let displayText = currentText.substring(0, charIndex) + getCursor();
            $input.attr('placeholder', displayText);

            let speed = isDeleting ? 50 : 100;

            if (!isDeleting && charIndex === currentText.length) {
                speed = 1500;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                index = (index + 1) % texts.length;
                speed = 500;
            }

            typingTimeout = setTimeout(typeEffect, speed);
        }

        $input.on('focus', function () {
            clearTimeout(typingTimeout);
            $input.attr('placeholder', '');
        });

        $input.on('blur', function () {
            charIndex = 0;
            isDeleting = false;
            typeEffect();
        });

        typeEffect();
    });
});