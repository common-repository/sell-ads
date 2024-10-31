<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function wpsap_show($id){
    ob_start();
    $title = get_post_meta($id,"wpsap_campaign_title",true);
    $price = get_post_meta($id,"wpsap_campaign_price",true);
    $button_text = get_post_meta($id, "wpsap_button_text",true);
    $site_id = get_option('wpsap_site_id');
    $activer_orders = wpsap_get_active_ads($id);
    if(count($activer_orders) == 0){
    ?> 
    <div class="wpsap_ads_plugin_ads_box">
        <div class="wpsap_ads_plugin_ads_box_container">
        <div class="wpsap_ads_plugin_ads_box_title"><?php echo esc_html($title); ?></div>
        <button class="wpsap_ads_plugin_ads_box_btn" data-href="<?php echo esc_attr(wpsap_payemnt_server()); ?>payment/<?php echo esc_attr($site_id); ?>/<?php echo esc_attr($id); ?>?tok=<?php echo esc_attr(time()); ?>"><?php echo esc_html($button_text); ?></button>
        </div>
    </div>
    
    <?php 
    }
    else {
        foreach($activer_orders as $ad){
            $url = get_post_meta($ad->ID, "order_url",true);
         ?> 
    <div class="wpsap_ads_plugin_ads_box">
        <div class="wpsap_ads_plugin_ads_box_container">
        <a href="<?php echo esc_attr($url); ?>" target="_blank" rel="noopener noreferrer nofollow"><img style="max-width:100%;" src="<?php echo esc_attr(get_post_meta($ad->ID, "order_image",true)); ?>"></a>
        </div>
    </div>
    
    <?php } }
    return ob_get_clean();
}


function wpsap_shortcode_func( $atts ) {
	$attributes = shortcode_atts( array(
		'id' => false,
	), $atts );
	
	
    if($attributes['id']){
        return wpsap_show($attributes['id']);
    }
    else {
        return 'Invalid ID';
    }

}
add_shortcode( 'wpsap_ads', 'wpsap_shortcode_func' );