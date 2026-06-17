define([
    'jquery',
    'mage/validation'
], function ($) {
    'use strict';

    return function (config) {
        var $form = $(config.formSelector);
        var $mobileInput = $(config.mobileInputSelector);
        var $otpField = $(config.otpFieldSelector);
        var $sendBtn = $(config.sendBtnSelector);
        var $verifyBtn = $(config.verifyBtnSelector);
        var $messages = $(config.messagesSelector);
        var $subtitle = $(config.subtitleSelector);
        var $mobileStep = $(config.mobileStepSelector);
        var $registerModal = $(config.registerModalSelector);
        var $registerForm = $(config.registerFormSelector);
        var $registerBtn = $(config.registerBtnSelector);
        var $registerMessages = $(config.registerMessagesSelector);
        var $registerModalMobile = $(config.registerModalMobileSelector);
        var otpSent = false;

        if ($registerModal.length && !$registerModal.parent().is('body')) {
            $registerModal.appendTo('body');
        }

        var showMessage = function ($container, msg, isError) {
            $container
                .html('<div class="bv-otp-message ' + (isError ? 'bv-otp-message--error' : 'bv-otp-message--success') + '">' + msg + '</div>')
                .show();
        };

        var setButtonLoading = function ($btn, loadingText, isLoading) {
            var $label = $btn.find('span').first();

            if (isLoading) {
                if (!$btn.data('original-text')) {
                    $btn.data('original-text', $label.text());
                }
                $btn.prop('disabled', true);
                $label.text(loadingText);
            } else {
                $btn.prop('disabled', false);
                $label.text($btn.data('original-text') || $label.text());
            }
        };

        var switchToOtpStep = function () {
            otpSent = true;
            $subtitle.text(config.otpSubtitle || 'Enter the OTP sent to your mobile number');
            $mobileStep.hide();
            $otpField.show();
            $sendBtn.hide();
            $verifyBtn.show();
            $otpField.find('input').focus();
        };

        var resetForm = function () {
            otpSent = false;
            $subtitle.text(config.mobileSubtitle || 'We will send you an OTP to verify your number');
            $mobileStep.show();
            $otpField.hide().find('input').val('');
            $sendBtn.show();
            $verifyBtn.hide();
            $messages.empty().hide();
            setButtonLoading($sendBtn, '', false);
            setButtonLoading($verifyBtn, '', false);
            closeRegisterModal();
        };

        var openRegisterModal = function (mobileNumber) {
            if (!$registerModal.length) {
                return;
            }

            $registerModalMobile.text(
                (config.registerMobileText || 'Verified mobile: +91 %1').replace('%1', mobileNumber)
            );
            $registerMessages.empty().hide();

            if ($registerForm.length && $registerForm[0]) {
                $registerForm[0].reset();
            }

            $registerModal
                .removeAttr('style')
                .addClass('bv-register-modal--open')
                .attr('aria-hidden', 'false');
            $('body').addClass('bv-register-modal-open');
            $registerForm.find('input:visible').first().focus();
        };

        var closeRegisterModal = function () {
            $registerModal
                .removeClass('bv-register-modal--open')
                .attr('aria-hidden', 'true');
            $('body').removeClass('bv-register-modal-open');
            $registerMessages.empty().hide();
            setButtonLoading($registerBtn, '', false);
        };

        $mobileInput.on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });

        $sendBtn.on('click', function () {
            if (!$form.validation('isValid')) {
                return false;
            }

            $messages.empty().hide();
            setButtonLoading($sendBtn, config.sendingText || 'Sending...', true);

            $.ajax({
                url: config.sendUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    mobile_number: $mobileInput.val()
                },
                success: function (res) {
                    if (res.success) {
                        showMessage($messages, res.message, false);
                        switchToOtpStep();
                    } else {
                        showMessage($messages, res.message, true);
                    }
                    setButtonLoading($sendBtn, '', false);
                },
                error: function () {
                    showMessage($messages, config.errorText || 'Something went wrong. Please try again.', true);
                    setButtonLoading($sendBtn, '', false);
                }
            });
        });

        $verifyBtn.on('click', function () {
            if (!$form.validation('isValid')) {
                return false;
            }

            $messages.empty().hide();
            setButtonLoading($verifyBtn, config.verifyingText || 'Verifying...', true);

            $.ajax({
                url: config.verifyUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    mobile_number: $mobileInput.val(),
                    otp: $otpField.find('input').val()
                },
                success: function (res) {
                    if (res.success) {
                        if (res.needs_registration === true || res.needs_registration === 'true' || res.needs_registration === 1) {
                            showMessage($messages, res.message, false);
                            openRegisterModal(res.mobile_number || $mobileInput.val());
                            setButtonLoading($verifyBtn, '', false);
                        } else if (res.redirect) {
                            showMessage($messages, res.message, false);
                            window.location.href = res.redirect;
                        } else {
                            showMessage($messages, res.message, false);
                            setButtonLoading($verifyBtn, '', false);
                        }
                    } else {
                        showMessage($messages, res.message, true);
                        setButtonLoading($verifyBtn, '', false);
                    }
                },
                error: function () {
                    showMessage($messages, config.errorText || 'Something went wrong. Please try again.', true);
                    setButtonLoading($verifyBtn, '', false);
                }
            });
        });

        $registerBtn.on('click', function () {
            if (!$registerForm.validation('isValid')) {
                return false;
            }

            $registerMessages.empty().hide();
            setButtonLoading($registerBtn, config.creatingText || 'Creating...', true);

            $.ajax({
                url: config.registerUrl,
                type: 'POST',
                dataType: 'json',
                data: $registerForm.serialize(),
                success: function (res) {
                    if (res.success && res.redirect) {
                        showMessage($registerMessages, res.message, false);
                        window.location.href = res.redirect;
                    } else if (res.success) {
                        showMessage($registerMessages, res.message, false);
                        setButtonLoading($registerBtn, '', false);
                    } else {
                        showMessage($registerMessages, res.message, true);
                        setButtonLoading($registerBtn, '', false);
                    }
                },
                error: function () {
                    showMessage($registerMessages, config.errorText || 'Something went wrong. Please try again.', true);
                    setButtonLoading($registerBtn, '', false);
                }
            });
        });

        if (config.changeNumberSelector) {
            $(config.changeNumberSelector).on('click', function (e) {
                e.preventDefault();
                resetForm();
            });
        }

        if (config.registerModalCloseSelector) {
            $(config.registerModalCloseSelector).on('click', function () {
                closeRegisterModal();
            });
        }

        if (config.registerModalOverlaySelector) {
            $(config.registerModalOverlaySelector).on('click', function () {
                closeRegisterModal();
            });
        }
    };
});
