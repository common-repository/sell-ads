jQuery('.wpsap_ads_plugin_ads_box_btn').on('click',function(){
    var link = jQuery(this).attr('data-href');
    location.href= link;

});