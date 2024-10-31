<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function wpsap_campaign_metaboxes( ) {
    add_meta_box(
        'campaign_metaboxes', // $id
        'Campaign Information', // $title
        'wpsap_campaign_metaboxes_html', // $callback
        'ads_campaign', // $screen
        'normal', // $context
        'high' // $priority
    );
 }
 add_action( 'add_meta_boxes', 'wpsap_campaign_metaboxes' );

 function wpsap_campaign_metaboxes_html(){
    global $post;
    $campaign_title = get_post_meta($post->ID, "wpsap_campaign_title",true);
    if(empty($campaign_title)){
        $campaign_title = 'This Advertising space is for sale';
    }

    $campaign_name = get_the_title($post->ID);
    if(empty($campaign_name)){
        $campaign_name = '';
    }

    $button_text = get_post_meta($post->ID, "wpsap_button_text",true);
    if(empty($button_text)){
        $button_text = 'Buy';
    }

?>
    <table class="campaign_information_table">
    <tr>
            <td><h4>Campagin Name (Inivisible) </h4></td>
            <td>
            <div class="tooltip"><span class="dashicons dashicons-info"></span>
  <span class="tooltiptext">The campaign name is not displayed in the frontend but is only there to help you keep
track.</span>
</div>
            <input type="text" name="wpsap_campaign_name" id="wpsap_campaign_name" class="regular-text" value="<?php echo esc_attr($campaign_name); ?>" required>
            </td>
         </tr>

        <tr>
            <td><h4>Title </h4></td>
            <td>
            <div class="tooltip"><span class="dashicons dashicons-info"></span>
  <span class="tooltiptext">This title is used to advertise your ad space.</span>
</div>
            <input type="text" name="wpsap_campaign_title" class="regular-text" value="<?php echo esc_attr($campaign_title); ?>" required>
            </td>
         </tr>

         <tr>
            <td><h4>Button Text</h4></td>
            <td>
            <div class="tooltip"><span class="dashicons dashicons-info"></span>
  <span class="tooltiptext">This text is displayed on the button</span>
</div>

            <input type="text" name="wpsap_button_text" class="regular-text" value="<?php echo esc_attr($button_text); ?>" required>
            </td>
         </tr>

         <tr>
            <td><h4>Monthly Price ($)</h4></td>
            <td>
            <div class="tooltip"><span class="dashicons dashicons-info"></span>
  <span class="tooltiptext">This is the monthly price in US dollars for which you want to rent out your ad space.</span>
</div>
            <input type="number" name="wpsap_campaign_price" class="regular-text" value="<?php echo esc_attr(get_post_meta($post->ID, "wpsap_campaign_price",true)); ?>" required>
            </td>
         </tr>


    </table>
    <div class="save_area">
    <button type="button" id="second_save_button" class="button button-primary">Update</button>
</div>
    <style>
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted #ffff;
}

.save_area {
    text-align: right;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 300px;
    background-color: black;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 11px;
    position: absolute;
    z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}

.postbox-header {
    display: none;
}

 #titlediv {
    display: none;
 }


div#postbox-container-1 {
    display: none;
}

.dashicons-info:before {
    content: "\f348";
    color: #626262;
}
</style>
<script>
    jQuery('#second_save_button').on('click', function(){
        jQuery('#publish').click();
    });

    jQuery('#campaign_name').on('change',function(){

        jQuery('#title').val(jQuery(this).val());
    });

    jQuery('.toplevel_page_sell-my-adspace').find('.current').removeClass('current');
    jQuery('.toplevel_page_sell-my-adspace').find('.wp-first-item').addClass('current');
</script>
<?php
}


function wpsap_campaign_save_post($post_id)
{
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return $post_id;

    // Verify nonce
    if (!isset($_POST['wpsap_campaign_nonce']) || !wp_verify_nonce($_POST['wpsap_campaign_nonce'], 'wpsap_campaign_nonce')) {
        return $post_id;
    }

    // Check if the current user has permission to edit the post
    if (!current_user_can('edit_post', $post_id))
        return $post_id;

    // Sanitize and update post meta fields
    if (isset($_POST["wpsap_campaign_title"])) {
        update_post_meta($post_id, "wpsap_campaign_title", sanitize_text_field($_POST["wpsap_campaign_title"]));
    }
    if (isset($_POST["wpsap_campaign_name"])) {
        update_post_meta($post_id, "wpsap_campaign_name", sanitize_text_field($_POST["wpsap_campaign_name"]));
    }
    if (isset($_POST["wpsap_button_text"])) {
        update_post_meta($post_id, "wpsap_button_text", sanitize_text_field($_POST["wpsap_button_text"]));
    }
    if (isset($_POST["wpsap_campaign_price"])) {
        update_post_meta($post_id, "wpsap_campaign_price", sanitize_text_field($_POST["wpsap_campaign_price"]));
    }
}   
add_action('save_post_ads_campaign', 'wpsap_campaign_save_post');


// Add the custom columns to the book post type:
add_filter( 'manage_ads_campaign_posts_columns', 'wpsap_edit_campaign_columns' );
function wpsap_edit_campaign_columns($columns) {
    unset($columns['date']);
    $columns['title'] = __( 'Campaign', "wpsap");
    $columns['price'] = __( 'Price', "wpsap");
   
    $columns['pdate'] = __( 'Date', "wpsap");
    $columns['php'] = __( 'PHP', "wpsap");
    $columns['shortcode'] = __( 'Shortcode', "wpsap");
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_ads_campaign_posts_custom_column' , 'wpsap_campaign_column', 10, 2 );
function wpsap_campaign_column( $column, $post_id ) {
    switch ( $column ) {
        case 'pdate' :
            echo  esc_html(get_the_date('d. F Y',$post_id));
            break;
        case 'price' :
            echo  '$'.esc_html(get_post_meta($post_id, "wpsap_campaign_price",true))." per month";
            break;
        case 'php' :
            echo  esc_html(wpsap_plugin_php_function($post_id));
            break;
        case 'shortcode' :
            echo  esc_html(wpsap_shortcode_input($post_id));
            break;

    }
}



// Add the custom columns to the ads_order post type:
add_filter( 'manage_ads_order_posts_columns', 'wpsap_order_edit_campaign_columns' );
function wpsap_order_edit_campaign_columns($columns) {
    unset($columns['date']);
    unset($columns['title']);
    $columns['order'] = __( 'Order', "wpsap");
    $columns['campaign'] = __( 'Campaign', "wpsap");
    $columns['pdate'] = __( 'Date', "wpsap");
    $columns['status'] = __( 'Status', "wpsap");
    $columns['billing'] = __( 'Billing', "wpsap");
    $columns['resources'] = __( 'Resources', "wpsap");
    $columns['link'] = __( 'Link', "wpsap");
    $columns['total'] = __( 'Total', "wpsap");
    $columns['action'] = __( 'Actions', "wpsap");
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_ads_order_posts_custom_column' , 'wpsap_ads_order_column', 10, 2 );
function wpsap_ads_order_column( $column, $post_id ) {
    switch ( $column ) {

        case 'order' :
            echo  'Order #'.esc_html($post_id);
            break;
        case 'campaign' :
            echo  esc_html(get_the_title(get_post_meta($post_id, "wpsap_place_id",true)));
            break;
        case 'pdate' :
            echo  esc_html(get_the_date('d. F Y',$post_id));
            break;
        case 'status' :
            $status = get_post_meta($post_id, "wpsap_order_status",true);
            $current_status =   get_post_meta($post_id, "wpsap_order_status",true);
            if($status == 'pending'){
                $current_date = time();
                $placing_date = strtotime(get_the_date('d. F Y',$post_id));
                $diff_in_seconds = $current_date-$placing_date;
                $diff = round ($diff_in_seconds / (60 * 60 * 24));
                $final_dates = (14-$diff);
                if($final_dates > 0){
                    echo '<strong>Pending</strong>';
                     echo '<br/><small><span>('.esc_html($final_dates).' days until expiration)</span></small>';
                }
                else {
                    wpsap_reject_ads($post_id);
                    echo '<script> location.reload(); </script>';
                    echo "<span style='color:#ff0000;'>Rejected</span>";
                }
               
               
            }
            else {
               if($current_status == 'accepted'){
                   echo "<span style='color:green;'>Running</span> <br/> <small>(Please Publish the ads)</small>";
               }
               else if($current_status == 'published'){
                    echo "<strong style='color:green;'>Running</strong>";
               }
               else if($current_status == 'pending'){
                   echo "Pending";
               }
               else if($current_status == 'cencelled'){
                   echo "<span style='color:#ff0000;'>Cancelled</span>";
               }
               else if($current_status == 'rejected'){
                   echo "<span style='color:#ff0000;'>Rejected</span>";
               }
               else {
                    echo esc_html($current_status); 
               }
               
            }
            break;
        case 'billing' :
            echo  esc_html(get_post_meta($post_id, "order_email",true));
             break;
        case 'resources' :
            $url = get_post_meta($post_id, "order_url",true);
            echo  '<a target="_blank" href="'.esc_url(get_post_meta($post_id, "order_image",true)).'"><img style="max-width:120px;" src="'.esc_url(get_post_meta($post_id, "order_image",true)).'"></a>';
            break;
        case 'link' :
            $url = get_post_meta($post_id, "order_url",true);
            echo  '<a href="'.esc_url($url).'">'.esc_url($url).'</a>';
            break;
        case 'total' :
            $status = get_post_meta($post_id, "wpsap_order_status",true);
            $payment = get_post_meta($post_id, "order_total",true)/100;
            if($status == "accepted"){
                echo "<small>You have confirmed the advertising partnership. Now you need to publish the advertisement. Make sure that the ad is displayed correctly and that the product link works. If there are any errors, you should contact the buyer immediately. If the buyer does not cancel within the next 14 days, the first transaction will be executed. The monthly payment will remain until someone cancels.</small>";
            }
            else {
                $total = get_post_meta($post_id, "order_total",true)/100;
                echo "$".esc_html($total);
            }
            break;
        case 'action' :
        	$status = get_post_meta($post_id, "wpsap_order_status",true);
             if($status == 'rejected'){
                echo 'This Order is rejected & refunded!';
            }
            elseif($status != "accepted" && $status != "published"){
            
            echo  '<a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&reject='.esc_attr($post_id).'" class="button reject_ads">Reject</a> | <a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&accept='.esc_attr($post_id).'" class="button accept_ads">Accept</a>';
            }
            
            else {
            if($status != "published"){
            echo '<a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&publish='.esc_attr($post_id).'" class="button button-primary accept_ads">Publish</a> | <a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&reject='.esc_attr($post_id).'" class="button reject_ads">Cancel</a>';
             }
             else {
               echo '<a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&accept='.esc_attr($post_id).'" class="button button-primary accept_ads">Unpublish</a> | <a href="'.esc_url(get_admin_url()).'edit.php?post_type=ads_order&reject='.esc_attr($post_id).'" class="button reject_ads">Cancel</a>';
             }
            }
            break;

    }
}


add_action('admin_footer','wpsap_hide_space');
function wpsap_hide_space(){
    if(isset($_GET['post_type'])){
        if(sanitize_text_field($_GET['post_type']) == 'ads_order'){
    ?> 
    <style>
.tablenav.top {
    display: none;
}
</style>

<?php 
        }
    }
}



add_action('admin_init','wpsap_job_status');
function wpsap_job_status(){
  if(isset($_GET['post_type'])){
        if($_GET['post_type'] == 'ads_order'){
            $main_url = get_admin_url().'edit.php?post_type=ads_order';
            if(isset($_GET['accept'])){
            $accept = sanitize_text_field($_GET['accept']);
            update_post_meta($accept, "wpsap_order_status",'accepted');
            $my_post = array(
                'ID'            => $accept,
                'post_status'   => 'active',
            );
            wp_update_post( $my_post );
            wpsap_accept_ads($accept);
            $main_url = get_admin_url().'edit.php?post_type=ads_order';
            wp_safe_redirect($main_url);
            exit();
            }

            if(isset($_GET['reject'])){
            $reject = sanitize_text_field($_GET['reject']);
            wpsap_reject_ads($reject);
            update_post_meta($reject, "wpsap_order_status",'rejected');
            $my_post = array(
                'ID'            => $reject,
                'post_status'   => 'rejected',
            );
            wp_update_post( $my_post );
             $main_url = get_admin_url().'edit.php?post_type=ads_order';
             wp_safe_redirect($main_url);
             exit();
            }

              if(isset($_GET['publish'])){
            $publish = sanitize_text_field($_GET['publish']);
            update_post_meta($publish, "wpsap_order_status",'published');
           
             wp_safe_redirect($main_url);
             exit();
            }
           
        }
  }

}