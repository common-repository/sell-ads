jQuery('#wp_sap_form').on('submit', function(e) {
    e.preventDefault();
    jQuery('#upload_image_btn').attr('disabled', 'disabled');
    jQuery('#upload_image_btn').text('Uploading...');
    jQuery('#image_uploaded_area').text('Please Wait...');

    jQuery("#wpsap_security").val(wpsapAjax.security);
    jQuery.ajax({
        type: 'POST',
        url: wpsapAjax.ajaxurl,
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {},
        success: function(response) {
            if (response.success == 1) {
                var temp = `<img src="` + response.url + `" alt="">`;
                jQuery("#image_uploaded_area").html(temp);
                jQuery('#ads_image').val(response.url);
            } else {
                jQuery("#image_uploaded_area").html('<h5>Invalid Image!</h5>');
            }
            jQuery('#upload_image_btn').removeAttr('disabled');
            jQuery('#upload_image_btn').text('Upload Image');
        }
    });


});

jQuery('.next').on('click', function() {
    var uploaded_image = jQuery('#ads_image').val();
    if (uploaded_image.length > 5) {
        if (jQuery('#site_url').val().length > 1) {
            jQuery('.upload_ads_area').hide();
            jQuery('.purchased_area').show();
        } else {
            alert("Please add a link to your offer first");
        }

    } else {
        alert("Please upload your advertising image first");
    }

});

jQuery('#images').on('change', function() {
    jQuery("#image_uploaded_area").html('<h5>Image Selected</h5>');
    jQuery('#hidden_upload_image_btn').click();

});

jQuery('#upload_image_btn').on('click', function() {
    jQuery('#images').click();

});

jQuery('#site_url').on('change', function() {

    jQuery('#hidden_upload_image_btn').click();
});

new ClipboardJS('.wpsap_ads_copy_btn');


jQuery('.btn_finish').on("click", function() {
    location.href = jQuery(this).attr('data-link');
});

jQuery('.btn_back').on('click', function() {
    jQuery('.purchased_area').hide();
    jQuery('.upload_ads_area').show();
});