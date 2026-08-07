(function ($) {
    'use strict';

    function getErrorMessage(response) {
        if (
            response &&
            response.responseJSON &&
            response.responseJSON.data &&
            response.responseJSON.data.message
        ) {
            return response.responseJSON.data.message;
        }

        return ipgeoAjax.errorText;
    }

    $(document).on('submit', '.ipgeo-form', function (event) {
        event.preventDefault();

        var $form = $(this);
        var $container = $form.closest('.ipgeo-container');
        var $results = $container.find('.ipgeo-results').first();
        var $submit = $form.find('[type="submit"]').first();
        var request = $form.data('ipgeoRequest');

        if (request && request.readyState !== 4) {
            request.abort();
        }

        $form.addClass('is-loading').attr('aria-busy', 'true');
        $submit.prop('disabled', true);
        $results.empty().append(
            $('<p>', {
                'class': 'ipgeo-loading',
                'role': 'status',
                'text': ipgeoAjax.loadingText
            })
        );

        request = $.ajax({
            url: ipgeoAjax.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: $form.serialize() + '&action=ipgeo_lookup'
        })
            .done(function (response) {
                if (response.success && response.data && response.data.html) {
                    $results.empty().append(response.data.html);
                    return;
                }

                var message = response.data && response.data.message
                    ? response.data.message
                    : ipgeoAjax.errorText;

                $results.html(
                    $('<p>', {
                        'class': 'alert alert-danger',
                        'text': message
                    })
                );
            })
            .fail(function (response, status) {
                if ('abort' === status) {
                    return;
                }

                $results.html(
                    $('<p>', {
                        'class': 'alert alert-danger',
                        'text': getErrorMessage(response)
                    })
                );
            })
            .always(function () {
                $form.removeClass('is-loading').removeAttr('aria-busy');
                $submit.prop('disabled', false);
                $form.removeData('ipgeoRequest');
            });

        $form.data('ipgeoRequest', request);
    });
}(jQuery));