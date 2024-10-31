<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function wpsap_upload_user_file( $file = array() ) {    
    require_once( ABSPATH . 'wp-admin/includes/admin.php' );
    $file_return = wp_handle_upload( $file, array('test_form' => false ) );
    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
        return false;
    } else {
        $filename = $file_return['file'];
        $attachment = array(
            'post_mime_type' => $file_return['type'],
            'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $file_return['url']
        );
        $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
        wp_update_attachment_metadata( $attachment_id, $attachment_data );
        if( 0 < intval( $attachment_id ) ) {
          return $attachment_id;
        }
    }
    return false;
}

add_action( 'wp_ajax_wpsap_upload_image', 'wpsap_upload_image' );
add_action( 'wp_ajax_nopriv_wpsap_upload_image', 'wpsap_upload_image' );
function wpsap_upload_image() {
    check_ajax_referer( 'wpsap_dddstring', 'security' );

    $data = array();
    $type= $_FILES[ 'image' ][ 'type' ];
    $extensions=array( 'image/jpeg', 'image/png', 'image/gif' );
    if( in_array( $type, $extensions )){
        $image = wpsap_upload_user_file($_FILES['image']);
        if($image > 0){
            $data['success'] = 1;
            $data['url'] = wp_get_attachment_url($image);
            $order_id = sanitize_text_field($_POST['order_id']);
             $site_url = sanitize_text_field($_POST['site_url']);
            update_post_meta($order_id,'order_image',$data['url']);
            update_post_meta($order_id,'order_url',$site_url);
        }
        else {
            $data['success'] = 0;
            $data['msg'] = "Invalid Image";   
            $data['code'] = 101;
        }
        
    }
    else {
        $data['success'] = 0;
        $data['msg'] = "Invalid Image";
        $data['code'] = 102;
    }
   
    wp_send_json($data);
}