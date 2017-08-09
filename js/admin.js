(function ($) {
    'use strict';

    function toggleSuccessRedirectUrlWrapper(show) {
        $('.emailoctopus-widget-options .success-redirect-url-wrapper').toggle(
            show
        );
    }

    $(function() {
        toggleSuccessRedirectUrlWrapper(
            $('.emailoctopus-widget-options .redirect-on-success').is(':checked')
        )

        $(document).on('widget-updated', function(e, widget) {
            toggleSuccessRedirectUrlWrapper(
                $('.emailoctopus-widget-options .redirect-on-success').is(':checked')
            )
        });

        $('body').on('change', '.emailoctopus-widget-options .redirect-on-success', function() {
            if ($(this).is(':checked')) {
                toggleSuccessRedirectUrlWrapper(true);
                $('.emailoctopus-widget-options input.success-redirect-url').focus();
            } else {
                toggleSuccessRedirectUrlWrapper(false);
                $('.emailoctopus-widget-options input.success-redirect-url').val('');
            }
        });
    });
}(jQuery));
