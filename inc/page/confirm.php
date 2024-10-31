<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html>
<head>
<?php wp_head(); ?>
</head>
<body>
<?php 
$wpsap_ads_stripe_account = get_option('wpsap_option_name');
$notification = "";
$terms = "";
$cencel = "";
$cencel_policy = "";
if(isset($wpsap_ads_stripe_account['notification_2'])){
	$notification = $wpsap_ads_stripe_account['notification_2'];
}

if(isset($wpsap_ads_stripe_account['terms_condtions_0'])){
	$terms = $wpsap_ads_stripe_account['terms_condtions_0'];
}

if(isset($wpsap_ads_stripe_account['template_cencel_subscription_1'])){
	$cencel = $wpsap_ads_stripe_account['template_cencel_subscription_1'];
}
if(isset($wpsap_ads_stripe_account['template_cencel_policy_1'])){
    $cencel_policy = $wpsap_ads_stripe_account['template_cencel_policy_1'];
}

$note_on_thanks_page = "";
if(isset($wpsap_ads_stripe_account['note_on_thanks_page'])){
	$note_on_thanks_page = $wpsap_ads_stripe_account['note_on_thanks_page'];
}
$image_uploaded = false;
$link_added = false;
$session_id = sanitize_text_field($_GET['session']);
if(get_post_meta($order_id,'order_image',true)){
    $image_uploaded = get_post_meta($order_id,'order_image',true);
}

if(get_post_meta($order_id,'order_url',true)){
    $link_added = get_post_meta($order_id,'order_url',true);
}

?>
<?php if(!$link_added || !$image_uploaded){ 
    $upload_ads_area_css = "display:block";
 } 
 else {
    $upload_ads_area_css = "display:none"; 
 }
 ?> 
<div class="upload_ads_area" style="<?php echo esc_attr($upload_ads_area_css); ?>">

<div class="container">
   
    <div class="text">
        <h2>Upload your Ad</h2>
		<p><?php echo esc_html($notification); ?></p>
        <div class="notes_text_desc">Attention: Make sure that you upload your desired advertising image (JPG, PNG, GIF) in the preferred resolution (up to max. 10 MB) and add the correct link to your offer. 
</div>
    </div>

    <form action="" id="wp_sap_form" method="post" enctype="multipart/form-data">
        <div class="file-area">
            <input type="hidden" name="action" value="wpsap_upload_image">
            <input type="hidden" name="security" id="wpsap_security" value="">
            <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
            <input type="file" name="image" id="images"  required="required">
            <div class="file-dummy">
                <?php if($image_uploaded){ ?> 
                 <div class="default">
                    <img src="<?php echo esc_url($image_uploaded); ?>" alt="">
                    <h6>Click on the image to change it</h6>
                </div>
                <?php } else { ?> 
                <div class="default">
                    <img src="<?php echo esc_attr(WPSAP_Plugin_Dir); ?>inc/page/img/icon.png" alt="">
                    <h6>Drop image here or click to upload</h6>
                </div>
                <?php } ?> 
                <div class="success" id="image_uploaded_area">
                    <h5>Image Uploading....</h5>
                </div>
            </div>
        </div>
        <div class="file">
       
        <input type="submit" value="Upload image" id="hidden_upload_image_btn" style="display:none;">
		<button type="button" id="upload_image_btn">Upload image</button>
         <input type="text" id="site_url" name="site_url" value="<?php echo esc_url($link_added); ?>" placeholder="Paste your product link here...">
        </div>
    </form>
    <ul>
      
        <li>
            <a href="<?php echo esc_url(get_the_permalink($cencel)); ?>"><?php echo esc_html(get_the_title($cencel)); ?></a>
        </li>
    </ul>
    <div class="btn_area">
        <input type="hidden" id="ads_image" value="<?php echo esc_url($image_uploaded); ?>">
     
        <button class="btn next">Next</button>
    </div>
</div>
</div>
<?php if(!$link_added || !$image_uploaded){ ?> 
<div class="purchased_area" style="display: none;">
<?php } else {  ?> 
<div class="purchased_area">
<?php } ?> 

    <div class="container">
        <div class="row">

            <div class="left_column">
                <a href="#" class="logo" style="display:none;">
                    <img src="<?php echo esc_attr(WPSAP_Plugin_Dir); ?>inc/page/img/logo.png" alt="">
                </a>
                <h2><?php echo esc_html(get_bloginfo( 'name' )); ?></h2>
                <div><?php echo esc_html($note_on_thanks_page); ?></div>
                <p>Copyright <?php echo esc_html(get_bloginfo( 'name' )); ?></p>
            </div>

            <div class="right_column">
              
                <h3>Your order has been placed</h3>
                <p>The advertisement has been successfully sent to the website owner. <br/> 
                It may take up to 14 days for the ad to be reviewed and accepted. <br/> 
                You will automatically get your money back if your request is not answered or rejected. <br/>
                 If your ad has been accepted, your advertising partnership will remain active until you or the website owner cancel it.</p>
              
                <table>
                    <tbody>
                        <tr>
                            <td>Start date</td>
                            <td>After the website owner accept your ads.</td>
                        </tr>
                        <tr>
                            <td>End date</td>
                            <td>Until someone quits</td>
                        </tr>
                        
                    </tbody>
                </table>
                <div class="link_input_desc_area">
                <div class="link_input_area">
               <div class="input-group">
<input id="foo" type="text" class="link_copied_button" value="https://sellads.wpflamingo.com/cencelsubscription/<?php echo esc_attr($order_id); ?>/<?php echo esc_attr($session_id); ?>">
<span class="input-group-button">
<button class="wpsap_ads_copy_btn" type="button"  data-clipboard-target="#foo">
<img class="clippy" src="<?php echo esc_attr(WPSAP_Plugin_Dir); ?>inc/page/img/clippy.png" width="13" alt="Copy to clipboard">
</button>
</span>
     </div>           
     
    </div>
                <div class="link_area">
                  
                    <a href="#">
                        <img src="<?php echo esc_attr(WPSAP_Plugin_Dir); ?>inc/page/img/info-icon.png" alt="">
                    </a>
                  
                    <p>Save this link. With this link you can cancel your monthly advertising space rental in the future. If you want to cancel, you can use this link or simply ask the website owner directly.  </p>
                </div>
                </div>
                 <ul class="service_fee">
  <li>
         <a href="<?php echo esc_attr(get_the_permalink($cencel_policy)); ?>"><?php echo esc_attr(get_the_title($cencel_policy)); ?></a>  &nbsp;  <a href="<?php echo esc_attr(get_the_permalink($terms)); ?>"><?php echo esc_html(get_the_title($terms)); ?></a>
        </li>
</ul>
                <div class="back_finish_area">
                <button class="btn_back">Back</button>
                <button class="btn_finish" data-link="<?php echo esc_attr(get_site_url()); ?>">Finish</button>
                </div>
            </div>
        </div>
    </div>
   
</div>

<style>
body {
    background-color: #F0F1F2;
}

.notes_text_desc {
    max-width: 484px;
    margin: 0 auto;
    margin-bottom: 20px;
    background-color: #fef3cf;
    padding: 10px;
    font-weight: 300;
    text-align: left;
    font-family: sans-serif;
}

ul.service_fee a {
    color: #000;
    font-family: sans-serif;
}

.purchased_area .row .right_column table tbody tr {
    border-bottom: 1px solid #e3e3e3;
}

ul.service_fee li {
    list-style: none;
}
</style>

<?php wp_footer(); ?>
</body>
</html>