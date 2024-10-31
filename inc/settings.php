<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class WPSAP_Options {
	private $wpsap_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wpsap_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'wpsap_page_init' ) );
	}

	public function wpsap_add_plugin_page() {
		add_menu_page(
			'Sell Ad Space', // page_title
			'Sell Ad Space', // menu_title
			'manage_options', // capability
			'sell-my-adspace', // menu_slug
			array( $this, 'wpsap_create_admin_page' ), // function
			'dashicons-cart', // icon_url
			2 // position
		);

        add_submenu_page(
            'sell-my-adspace',
            'Settings',
            'Settings', 
            'manage_options',
            'sell-my-adspace',
            array( $this, 'wpsap_create_admin_page' ), 
            2 ); 

	}

	public function wpsap_create_admin_page() {
		$this->wpsap_options = get_option( 'wpsap_option_name' );
		$token = get_option('wpsap_generate_token');
		$email = get_option('admin_email');
		$site = get_site_url();
		$final_string = base64_encode(json_encode(array('token' => $token, 'email' => $email, 'site' => $site)));
		$wpsap_ads_stripe_account = get_option('wpsap_stripe_account');
		 ?>

		<div class="wrap">
		<h2>Settings</h2>
		<?php settings_errors(); ?>

		<h3>Payment</h3>
       
			<?php if($wpsap_ads_stripe_account){ ?> 
				<p><strong>Connected Stripe Account: <?php echo esc_html($wpsap_ads_stripe_account); ?></strong></p>
				<button class="wpsap_connect_stripe" data-href="<?php echo esc_attr(wpsap_payemnt_server()); ?>/connect/?init=<?php echo esc_attr($final_string); ?>&v=<?php echo esc_attr(time()); ?>">Go Stripe Dashboard</button>
			<?php } else { ?> 
			<button class="wpsap_connect_stripe" data-href="<?php echo esc_attr(wpsap_payemnt_server()); ?>/connect/?init=<?php echo esc_attr($final_string); ?>&v=<?php echo esc_attr(time()); ?>">Connect Your Stripe Account</button>

			<?php } ?> 

			     <p>We charge a service fee with a fixed transaction fee of $0.30 + 3.9% per <br/>
successful card charge. We want to be completely transparent with our fees.</p>
			<p></p>
			

			<form method="post" action="options.php">
				<?php
					settings_fields( 'wpsap_option_group' );
					do_settings_sections( 'sell-my-adspace-admin' );
					submit_button();
				?>
			</form>
			
           
		</div>

	


        <style>
            button.wpsap_connect_stripe {
    background-color: #6772e5;
    color: #ffff;
    border: none;
    padding: 10px 10px 10px 10px;
    border-radius: 5px;
    cursor: pointer;
}
form h2 {
    display: none;
}

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
			jQuery('.wpsap_connect_stripe').on('click', function(){
				location.href = jQuery(this).attr('data-href');

			});
		</script>
	<?php }

	public function wpsap_page_init() {
		register_setting(
			'wpsap_option_group', // option_group
			'wpsap_option_name', // option_name
			array( $this, 'wpsap_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'wpsap_setting_section', // id
			'Settings', // title
			array( $this, 'wpsap_section_info' ), // callback
			'sell-my-adspace-admin' // page
		);

		add_settings_field(
			'wpsap_terms_condtions_0', // id
			'Terms of Service', // title
			array( $this, 'wpsap_terms_condtions_0_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);

		add_settings_field(
			'wpsap_template_cencel_subscription_1', // id
			'Uploading Policy', // title
			array( $this, 'wpsap_template_cencel_subscription_1_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);

		add_settings_field(
			'wpsap_template_cencel_policy_1', // id
			'Cancel Policy', // title
			array( $this, 'wpsap_template_cencel_policy_1_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);


		add_settings_field(
			'wpsap_notification_2', // id
			'Note on Payment Page <div class="tooltip"><span class="dashicons dashicons-info"></span>
			<span class="tooltiptext">This text will be displayed on the stripe payment page</span>
		  </div>', // title
			array( $this, 'wpsap_notification_2_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);

		add_settings_field(
			'wpsap_note_on_upload_page', // id
			'Note on Upload-page  <div class="tooltip"><span class="dashicons dashicons-info"></span>
			<span class="tooltiptext">Please upload your promotional image and the link to your offer. </span>
		  </div>', // title
			array( $this, 'wpsap_note_on_upload_page_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);


		add_settings_field(
			'wpsap_note_on_thanks_page', // id
			'Note on Thank-You Page  <div class="tooltip"><span class="dashicons dashicons-info"></span>
			<span class="tooltiptext">This text will be displayed on the Thank-You page.</span>
		  </div>', // title
			array( $this, 'wpsap_note_on_thanks_page_callback' ), // callback
			'sell-my-adspace-admin', // page
			'wpsap_setting_section' // section
		);
	}

	public function wpsap_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['terms_condtions_0'] ) ) {
			$sanitary_values['terms_condtions_0'] = $input['terms_condtions_0'];
		}

		if ( isset( $input['template_cencel_subscription_1'] ) ) {
			$sanitary_values['template_cencel_subscription_1'] = $input['template_cencel_subscription_1'];
		}

        if ( isset( $input['template_cencel_policy_1'] ) ) {
			$sanitary_values['template_cencel_policy_1'] = $input['template_cencel_policy_1'];
		}


		if ( isset( $input['notification_2'] ) ) {
			$sanitary_values['notification_2'] = esc_textarea( $input['notification_2'] );
		}

		if ( isset( $input['note_on_upload_page'] ) ) {
			$sanitary_values['note_on_upload_page'] = esc_textarea( $input['note_on_upload_page'] );
		}

		if ( isset( $input['note_on_thanks_page'] ) ) {
			$sanitary_values['note_on_thanks_page'] = esc_textarea( $input['note_on_thanks_page'] );
		}



		return $sanitary_values;
	}

	public function wpsap_section_info() {
		
	}

	public function wpsap_get_pages(){
		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$pages = get_pages($args);

		return $pages;
	}

	

	public function wpsap_terms_condtions_0_callback() {
		?> <select name="wpsap_option_name[terms_condtions_0]" id="terms_condtions_0">
			<?php foreach($this->wpsap_get_pages() as $page){ ?> 
				<?php $selected = (isset( $this->wpsap_options['terms_condtions_0'] ) && $this->wpsap_options['terms_condtions_0'] == $page->ID) ? 'selected' : '' ; ?>
			<option value="<?php echo esc_attr($page->ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html(get_the_title($page->ID)); ?></option>
			<?php } ?> 

		</select> <?php
	}

	public function wpsap_template_cencel_subscription_1_callback() {
		?> <select name="wpsap_option_name[template_cencel_subscription_1]" id="template_cencel_subscription_1">
			
			<?php foreach($this->wpsap_get_pages() as $page){ ?> 
				<?php $selected = (isset( $this->wpsap_options['template_cencel_subscription_1'] ) && $this->wpsap_options['template_cencel_subscription_1'] == $page->ID) ? 'selected' : '' ; ?>
			<option value="<?php echo esc_attr($page->ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html(get_the_title($page->ID)); ?></option>
			<?php } ?> 
		</select> <?php
	}

		public function wpsap_template_cencel_policy_1_callback() {
		?> <select name="wpsap_option_name[template_cencel_policy_1]" id="template_cencel_policy_1">
			
			<?php foreach($this->wpsap_get_pages() as $page){ ?> 
				<?php $selected = (isset( $this->wpsap_options['template_cencel_policy_1'] ) && $this->wpsap_options['template_cencel_policy_1'] == $page->ID) ? 'selected' : '' ; ?>
			<option value="<?php echo esc_attr($page->ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html(get_the_title($page->ID)); ?></option>
			<?php } ?> 
		</select> <?php
	}

	public function wpsap_notification_2_callback() {
		printf(
			'<textarea class="large-text" rows="5" name="wpsap_option_name[notification_2]" id="notification_2" placeholder="Exact description of the advertising space, contact information, phone number...">%s</textarea>',
			isset( $this->wpsap_options['notification_2'] ) ? esc_attr( $this->wpsap_options['notification_2']) : ''
		);
	}

	public function wpsap_note_on_upload_page_callback() {
		printf(
			'<textarea class="large-text" rows="5" placeholder="Preferred image size, file format or other tips …" name="wpsap_option_name[note_on_upload_page]" id="note_on_upload_page">%s</textarea>',
			isset( $this->wpsap_options['note_on_upload_page'] ) ? esc_attr( $this->wpsap_options['note_on_upload_page']) : ''
		);
	}

	public function wpsap_note_on_thanks_page_callback() {
		printf(
			'<textarea class="large-text" rows="5" placeholder="Contact info, email address or phone number…" name="wpsap_option_name[note_on_thanks_page]" id="note_on_thanks_page">%s</textarea>',
			isset( $this->wpsap_options['note_on_thanks_page'] ) ? esc_attr( $this->wpsap_options['note_on_thanks_page']) : ''
		);
	}

	

}
if ( is_admin() )
	$wpsap = new WPSAP_Options();