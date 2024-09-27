jQuery(function ($) {
    $('#showBoxChangePolicy').on('click', function () {
        $('#FormPolicy').toggle();
    });

    $('#FormPolicy').submit(function (e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append('policy', $('#policyData').val());
		formData.append('inquiry', $('#inquiryData').val());
		formData.append('thanks_content', $('#thanksData').val());
		formData.append('email_content', $('#emailData').val());
        formData.append('action', 'update_policy');
        formData.append('_ajax_nonce', jsforwp_event_globals.nonce);

        $.ajax({
            type: 'POST',
            url: jsforwp_event_globals.ajax_url,
            async: false,
            data: formData,
            dataType: 'json',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (response) {
                $('#FormPolicy').hide();
                alert('Update success');
            },
            error: function (err) {
                console.log(err);
            },
        });
    });

    $('#FormImageBg').submit(function (e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append('image_bg', $('#imageBgData').val());
        formData.append('action', 'update_image_background');
        formData.append('_ajax_nonce', jsforwp_event_globals.nonce);

        $.ajax({
            type: 'POST',
            url: jsforwp_event_globals.ajax_url,
            async: false,
            data: formData,
            dataType: 'json',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Updated');
            },
            error: function (err) {
                console.log(err);
            },
        });
    });

    $('#FormImageBanner').submit(function (e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append('image_banner', $('#imageBannerData').val());
        formData.append('action', 'update_image_banner');
        formData.append('_ajax_nonce', jsforwp_event_globals.nonce);

        $.ajax({
            type: 'POST',
            url: jsforwp_event_globals.ajax_url,
            async: false,
            data: formData,
            dataType: 'json',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Updated');
            },
            error: function (err) {
                console.log(err);
            },
        });
    });
});
