<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function wpsap_plugin_php_function($content){

    $html = '<input type="text" readonly value="<?php echo wpsap_show('.esc_attr($content).'); ?>">';
    return $html;

}

function wpsap_shortcode_input($content){
    $html = '<input type="text" readonly value="[wpsap_ads id='.esc_attr($content).']">';
    return $html;
}

function wpsap_payemnt_server(){
    return 'https://sellads.wpflamingo.com/';
}

function wpsap_connect_account(){
    
}

function wpsap_get_active_ads($campaign){
      $args = array(
    'post_type'        => 'ads_order',
    'post_status' => array('pending','published','draft','active'),
    'posts_per_page'   => 10,
   );
   $args['meta_query'] = array(
    array(
        'key'     => 'wpsap_place_id',
        'value'   => $campaign,
        'compare' => '=',
    ),
    array(
        'key'     => 'order_status',
        'value'   => 'published',
        'compare' => '=',
    ),
  );

   $ads = get_posts($args);
   if(is_array($ads)){
       return $ads;
   }
   else {
       return array();
   }
}

function wpsap_reject_ads($order_id){
    update_post_meta($order_id,"wpsap_order_status",'rejected');
    $my_post = array(
            'ID'            => $order_id,
            'post_status'   => 'rejected',
        );
        wp_update_post( $my_post );
	$token = get_option('wpsap_generate_token');
    $trans_id = get_post_meta($order_id,'wpsap_confirm_payment',true);
    $api = wpsap_payemnt_server()."api/reject?id=".$trans_id."&token=".$token;
	wp_remote_get($api);
}

function wpsap_accept_ads($order_id){
    update_post_meta($order_id,"wpsap_order_status",'accepted');
    $my_post = array(
            'ID'            => $order_id,
            'post_status'   => 'active',
        );
        wp_update_post( $my_post );
	$token = get_option('wpsap_generate_token');
    $trans_id = get_post_meta($order_id,'wpsap_confirm_payment',true);
    $api = wpsap_payemnt_server()."api/accept?id=".$trans_id."&token=".$token;
	wp_remote_get($api);
}