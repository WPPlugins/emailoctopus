(function($){
    'use strict';

    var emailoctopus = {
        debug: window.location.href.indexOf('eoDebug=1') !== -1,
        isBotPost: function($form) {
            return $form.find('.emailoctopus-form-row-hp input').val();
        },
        basicValidateEmail: function(email) {
            var regex = /\S+@\S+\.\S+/;
            return regex.test(email);
        },
        ajaxSuccess: function($form) {
            $form.trigger('emailoctopus.success');

            var successRedirectUrl = $form.find('.emailoctopus-success-redirect-url').val();
            if (successRedirectUrl && successRedirectUrl.trim()) {
                // Redirect
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: redirecting to '+successRedirectUrl);
                }
                window.location.href = successRedirectUrl;
            } else {
                // Show confirmation
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: no redirect URL found, showing confirmation');
                }
                $form.siblings('.emailoctopus-success-message').text(
                    emailoctopus_message.success
                );
                $form.hide();
            }
        },
        ajaxError: function($form, textStatus) {
            var response = $.parseJSON(textStatus.responseText);
            var $errorMessage = $form.siblings('.emailoctopus-error-message');

            if (response && response.error && response.error.code) {
                switch(response.error.code) {
                    case 'INVALID_PARAMETERS':
                        $errorMessage.text(
                            emailoctopus_message.invalid_parameters_error
                        );
                        return;
                    case 'BOT_SUBMISSION':
                        $errorMessage.text(
                            emailoctopus_message.bot_submission_error
                        );
                        return;
                }
            }

            $errorMessage.text(
                emailoctopus_message.unknown_error
            );
            $form.find(':submit').removeAttr('disabled');
        },
        ajaxSubmit: function($form) {
            $form.find(':submit').attr('disabled', true);
            $.ajax({
                type: $form.attr('method'),
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function() {
                    if (emailoctopus.debug) {
                        console.log('EmailOctopus: posted');
                    }
                    emailoctopus.ajaxSuccess($form);
                },
                error: function(textStatus) {
                    if (emailoctopus.debug) {
                        console.log('EmailOctopus: error while posting');
                    }
                    emailoctopus.ajaxError($form, textStatus);
                },
            });
        }
    }

    $(function() {
        if (emailoctopus.debug) {
            if (typeof window.jQuery == 'undefined') {
                console.log('EmailOctopus: error, no jQuery');
            }
            var $form = $('.emailoctopus-form');
            if (!$form.length) {
                console.log('EmailOctopus: error, form missing');
            }
            if (!$form.siblings('.emailoctopus-error-message').length) {
                console.log('EmailOctopus: error, form missing error message section');
            }
            if (!$form.find('.emailoctopus-email-address').length) {
                console.log('EmailOctopus: error, form missing email address field');
            }
        }

        $('.email-octopus-form:not(.bound)').submit(function() {
            if (emailoctopus.debug) {
                console.log('EmailOctopus: form submitted');
            }
            var $form = $(this);
            var $errorMessage = $form.siblings('.emailoctopus-error-message');
            var emailAddress = $form.find('.emailoctopus-email-address').val();

            $errorMessage.empty();

            if (emailoctopus.isBotPost($form)) {
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: error, is bot post');
                }
                $errorMessage.text(
                    emailoctopus_message.bot_submission_error
                );
            } else if (!$.trim(emailAddress)) {
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: error, missing email address');
                }
                $errorMessage.text(
                    emailoctopus_message.missing_email_address_error
                );
            } else if (!emailoctopus.basicValidateEmail(emailAddress)) {
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: error, invalid email address');
                }
                $errorMessage.text(
                    emailoctopus_message.invalid_email_address_error
                );
            } else {
                if (emailoctopus.debug) {
                    console.log('EmailOctopus: posting');
                }
                emailoctopus.ajaxSubmit($form);
            }

            return false;
        })
        // Mitigate duplicate bindings, in case this script is included multiple
        // times. More reliable than running 'unbind' or 'off' first, which doesn't
        // work if jQuery is also included multiple times.
        .addClass('bound');
    });
})(jQuery);
