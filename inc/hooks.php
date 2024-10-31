<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init','wpsap_generate_token');
function wpsap_generate_token(){
    if(!get_option('wpsap_generate_token')){
        $bytes = random_bytes(20);  
        $token = bin2hex($bytes);
        update_option('wpsap_generate_token',$token);
    }
    if(isset($_GET['wpsap_ads_stripe_account'])){
        $token = sanitize_text_field($_GET['token']);
        $current_token = get_option('wpsap_generate_token');
        if($token == $current_token){
            update_option('wpsap_stripe_account',sanitize_text_field($_GET['wpsap_ads_stripe_account']));
            update_option('wpsap_site_id',sanitize_text_field($_GET['wpsap_ads_site_id']));
            echo 100;
            die();
        }
        
    }

    if(isset($_GET['wpsap_ads_get_ad'])){
        $data = array();
        $current_token = get_option('wpsap_generate_token');
        $token = sanitize_text_field($_GET['token']);
        if($token == $current_token){
            $data['success'] = 1;
            $adid = sanitize_text_field($_GET['wpsap_ads_get_ad']);
            $price =  get_post_meta($adid, "wpsap_campaign_price",true); 
            $campaign_title = get_post_meta($adid, "wpsap_campaign_title",true);
            if(empty($campaign_title)){
                $campaign_title = 'This Ads space is for sale';
            }
            $settings_data = get_option( 'wpsap_option_name');
			$note_on_checkout = $settings_data['notification_2'];
            $data['ad'] = array('title' => $campaign_title,'price' => $price,'note' => $note_on_checkout);
        }
        else {
            $data['success'] = 0;  
        }
        wp_send_json($data);
    }

     if(isset($_GET['wpsap_ads_cencel_subscription'])){
        $token = sanitize_text_field($_GET['token']);
        $current_token = get_option('wpsap_generate_token');
        if($token == $current_token){
            $order_id = sanitize_text_field($_GET['order']);
        update_post_meta($order_id,'order_status','cencelled');
        }
     }

    if(isset($_GET['wpsap_ads_confirm_payment'])){
        $transID = sanitize_text_field($_GET['wpsap_ads_confirm_payment']);
        $placeID = sanitize_text_field($_GET['wpsap_ads_place_id']);
        $current_token = get_option('wpsap_generate_token');
        $token = sanitize_text_field($_GET['token']);
        $email = sanitize_text_field($_GET['email']);
        $total = sanitize_text_field($_GET['total']);
        if($token == $current_token){
            $id = wp_insert_post(array(
                'post_title'=>'Order #'.$transID, 
                'post_type'=>'ads_order', 
                'post_status' => 'pending',
              ));
            if($id > 0){
                update_post_meta($id,'wpsap_place_id',$placeID);
                update_post_meta($id,'wpsap_confirm_payment',$transID);
                update_post_meta($id,'order_status','pending');
                update_post_meta($id,'order_email',$email);
                update_post_meta($id,'order_total',$total);
                
            }
        }
        die();
    }
}
add_action( 'init', 'wpsap_add_posttype' );
function wpsap_add_posttype(){
    $labels = array(
        'name' => __( 'Campaigns'),
        'singular_name' => __( 'Campaign' ),
        'add_new' => _x('Add New', 'wpsap_ads_plugin'),
        'add_new_item' => __('Add New'),
        'edit_item' => __('Edit Campaign'),
        'new_item' => __('New Campaign'),
        'view_item' => __('View Campaign'),
        'search_items' => __('Search Campaign'),
        'not_found' =>  __('No Campaign found'),
        'not_found_in_trash' => __('No Campaign found in Trash'), 
        );

    register_post_type( 'ads_campaign',
    array(
    'labels' =>  $labels,
    'public' => false,
    'show_ui' => true,
    'supports' => array('title'),
    'has_archive' => true,
    'show_in_menu' => 'sell-my-adspace'
    )
);

register_post_type( 'ads_order',
array(
        'labels' => array(
                'name' => __( 'Orders' ),
                'singular_name' => __( 'Order' )
        ),
'public' => true,
'has_archive' => true,
'supports' => array('title'),
'show_in_menu' => 'sell-my-adspace'
)
);

}

add_action( 'admin_head', 'wpsap_custom_status_count');
function wpsap_custom_status_count(){
    global $submenu;
    
    $order_count = 1;
    $args = array(
  'post_type'       => 'ads_order',
  'post_status'     => array('publish','draft','pending'),
  'posts_per_page'  => -1,
  'meta_query'      => array(
    array(
      'key'         => 'order_status',
      'value'       => 'pending',
      'compare'     => '=',
    ),
  )
);

    $second_loop = get_posts($args);
    if(is_array($second_loop)){
    $order_count = count($second_loop);
    }
    
    foreach ( $submenu['sell-my-adspace'] as $key => $menu_item ) {
            if($key == 1){
            if($order_count > 0){
            $submenu['sell-my-adspace'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // WPCS: override ok.
            break;  
                }
            }
        
    }
}

function wpsap_change_title_text( $title ){
    $screen = get_current_screen();
  
    if  ( 'ads_campaign' == $screen->post_type ) {
         $title = 'Campaign Name (Invisible)';
    }
  
    return $title;
}
add_filter( 'enter_title_here', 'wpsap_change_title_text' );

function wpsap_disable_new_posts() {
    // Hide sidebar link
    global $submenu;
    unset($submenu['edit.php?post_type=ads_order'][10]);

 
}
add_action('admin_menu', 'wpsap_disable_new_posts');

add_action('admin_footer','wpsap_page_css');
function wpsap_page_css(){
       // Hide link on listing page
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'ads_order') {
        echo '<style type="text/css">
        .page-title-action { display:none !important; }
        </style>';
    }
}
add_action('save_post','wpsap_redirect_page');
function wpsap_redirect_page(){
    $type=  get_post_type();

    switch ($type){
    case "ads_campaign":
        $url=  admin_url().'edit.php?post_type=ads_campaign';
        wp_redirect($url);
        exit;
    break;
    }
}


add_action('init','wpsap_confirm');
function wpsap_confirm(){
    if(isset($_GET['confirm']) && isset($_GET['session'])){
        $confirm = sanitize_text_field($_GET['confirm']);
        $session = sanitize_text_field($_GET['session']);
        $args = array(
            'post_type' => 'ads_order', 
            'numberposts'   => 1,
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'wpsap_confirm_payment',
                    'value' => $session,
                    'compare' => '=',
                )
            )
         );     

         $orders = get_posts($args);
         $order = false;
         foreach($orders as $order){
            $order = $order;
         }
         if($order){
            update_post_meta($order->ID,'wpsap_order_status',1);  
             $order_id = $order->ID;
              $order_date = get_the_date( 'l F j, Y',$order_id);
             $expected_date = date('l F j, Y',strtotime($order_date."+ 7days"));
             
             require WPSAP_Plugin_Path.'inc/page/confirm.php';
        }
        else {
            echo "Invalid Order";
        }
       
         die();
    }
}


// Registering custom post status
function wpsap_rejected_status(){
    register_post_status('rejected', array(
        'label'                     => _x( 'Rejected', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'wpsap_rejected_status' );
 
// Using jQuery to add it to post status dropdown
add_action('admin_footer-post.php', 'wpsap_rejected_status_list');
function wpsap_rejected_status_list(){
global $post;
$complete = '';
$label = '';
if($post->post_type == 'ads_order'){
if($post->post_status == 'rejected'){
$complete = ' selected="selected"';
$label = '<span id="post-status-display"> Rejected</span>';
}
echo '
<script>
jQuery(document).ready(function($){
$("select#post_status").append("<option value=\"rejected\" '.esc_attr($complete).'>Rejected</option>");
$(".misc-pub-section label").append("'.esc_attr($label).'");
});
</script>
';
}
}



// Registering custom post status
function wpsap_active_status(){
    register_post_status('active', array(
        'label'                     => _x( 'Aktiv', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Aktiv <span class="count">(%s)</span>', 'Aktiv <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'wpsap_active_status' );
 
// Using jQuery to add it to post status dropdown
add_action('admin_footer-post.php', 'wpsap_active_status_list');
function wpsap_active_status_list(){
global $post;
$complete = '';
$label = '';
if($post->post_type == 'ads_order'){
if($post->post_status == 'active'){
$complete = ' selected="selected"';
$label = '<span id="post-status-display"> Aktiv</span>';
}
echo '
<script>
jQuery(document).ready(function($){
$("select#post_status").append("<option value=\"active\" '.esc_attr($complete).'>Aktiv</option>");
$(".misc-pub-section label").append("'.esc_attr($label).'");
});
</script>
';
}
}


// Registering custom post status
function wpsap_pending_status(){
    register_post_status('pending', array(
        'label'                     => _x( 'Pending', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'wpsap_pending_status' );
 
// Using jQuery to add it to post status dropdown
add_action('admin_footer-post.php', 'wpsap_pending_status_list');
function wpsap_pending_status_list(){
global $post;
$complete = '';
$label = '';
if($post->post_type == 'ads_order'){
if($post->post_status == 'pending'){
$complete = ' selected="selected"';
$label = '<span id="post-status-display"> Pending</span>';
}
echo '
<script>
jQuery(document).ready(function($){
$("select#post_status").append("<option value=\"pending\" '.esc_attr($complete).'>Pending</option>");
$(".misc-pub-section label").append("'.esc_attr($label).'");
});
</script>
';
}
}