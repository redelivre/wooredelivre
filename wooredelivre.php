<?php

/**
 * Plugin Name: WooCommerce Extension for Rede Livre
 * Plugin URI: https://github.com/redelivre/wooredelivre
 * Description: Plugin to make changes on woocommerce to redelivre
 * Version: 1.0.0
 * Author: Maurilio Atila
 * Author URI: https://github.com/cabelotaina
 * Developer: Maurilio Atila
 * Developer URI: https://github.com/cabelotaina
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 */
if (in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) )) {
	// Put your plugin code here
	
	// add_filter('gettext', 'change_checkout_btn');
	// add_filter('ngettext', 'change_checkout_btn');
	//
	// //function
	// function change_checkout_btn($checkout_btn){
	// $checkout_btn= str_ireplace('Company Name', 'CPF', $checkout_btn);
	// return $checkout_btn;
	// }
	
	add_action ( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	add_action ( 'woocommerce_before_cart', 'wc_minimum_order_amount' );
	function wc_minimum_order_amount() {
		// Set this variable to specify a minimum order value
		$minimum = get_option( 'woocommerce_redelivre_min_order', 1 );;
		
		if ($minimum > 0 && WC ()->cart->total < $minimum) {
			
			if (is_cart ()) {
				
				wc_print_notice ( sprintf ( 'You must have an order with a minimum of %s to place your order, your current order total is %s.', wc_price ( $minimum ), wc_price ( WC ()->cart->total ) ), 'error' );
			} else {
				
				wc_add_notice ( sprintf ( 'You must have an order with a minimum of %s to place your order, your current order total is %s.', wc_price ( $minimum ), wc_price ( WC ()->cart->total ) ), 'error' );
			}
		}
	}
	
	add_filter ( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );
	
	// Our hooked in function - $fields is passed via the filter!
	function custom_override_checkout_fields($fields) {
		$fields ['billing'] ['billing_company'] ['label'] = 'CPF';
		$fields ['billing'] ['billing_company'] ['required'] = true;
		return $fields;
	}
	
	/**
	 * Add a custom field in shipping checkout form
	 */
	// Hook in
	add_filter ( 'woocommerce_checkout_fields', 'custom_override_shipping_checkout_fields' );
	
	// Our hooked in function - $fields is passed via the filter!
	function custom_override_shipping_checkout_fields($fields) {
		$fields ['shipping'] ['shipping_location_type'] = array (
				'type' => 'select',
				'label' => __ ( 'Location Type', 'woocommerce' ),
				'clear' => false,
				'options' => array (
						'home' => __ ( 'Home', 'woocommerce' ),
						'apartment' => __ ( 'Apartment', 'woocommerce' ),
						'business' => __ ( 'Business', 'woocommerce' ),
						'hospital' => __ ( 'Hospital', 'woocommerce' ),
						'funeral-home' => __ ( 'Funeral Home', 'woocommerce' ),
						'church' => __ ( 'Church', 'woocommerce' ),
						'other' => __ ( 'Other', 'woocommerce' ) 
				),
				'placeholder' => _x ( 'Location Type', 'placeholder', 'woocommerce' ),
				'required' => true 
		);
		
		$fields ['shipping'] ['shipping_email'] = array (
				'type' => 'text',
				'label' => __ ( 'Email Address', 'woocommerce' ),
				'clear' => false,
				'placeholder' => _x ( 'Enter Email', 'placeholder', 'woocommerce' ),
				'required' => true 
		);
		
		$fields ['shipping'] ['shipping_phone'] = array (
				'type' => 'text',
				'label' => __ ( 'Phone', 'woocommerce' ),
				'clear' => false,
				'placeholder' => _x ( 'Enter Phone Number', 'placeholder', 'woocommerce' ),
				'required' => true 
		);
		
		return $fields;
	}
	
	/**
	 * Add a custom field in billing checkout form
	 */
	// Hook in
	add_filter ( 'woocommerce_checkout_fields', 'custom_override_billing_checkout_fields' );
	
	// Our hooked in function - $fields is passed via the filter!
	function custom_override_billing_checkout_fields($fields) {
		// $fields['billing']['billing_location_type'] = array(
		// 'type' => 'select',
		// 'label' => __('Location Type', 'woocommerce'),
		// 'clear' => false,
		// 'options' => array(
		// 'home' => __('Home', 'woocommerce'),
		// 'apartment' => __('Apartment', 'woocommerce'),
		// 'business' => __('Business', 'woocommerce'),
		// 'hospital' => __('Hospital', 'woocommerce'),
		// 'funeral-home' => __('Funeral Home', 'woocommerce'),
		// 'church' => __('Church', 'woocommerce'),
		// 'other' => __('Other', 'woocommerce')
		// ),
		// 'placeholder' => _x('Location Type', 'placeholder', 'woocommerce'),
		// 'required' => true
		// );
		return $fields;
	}
	
	/**
	 * Order all the fields as per requirement
	 */
	add_filter ( "woocommerce_checkout_fields", "order_shipping_fields" );
	function order_shipping_fields($fields) {
		$order = array (
				"shipping_country",
				"shipping_first_name",
				"shipping_last_name",
				"shipping_location_type",
				"shipping_address_1",
				"shipping_address_2",
				"shipping_city",
				"shipping_state",
				"shipping_postcode",
				"shipping_email",
				"shipping_phone" 
		);
		foreach ( $order as $field ) {
			$ordered_fields [$field] = $fields ["shipping"] [$field];
		}
		
		$fields ["shipping"] = $ordered_fields;
		unset ( $fields ['order'] ['order_comments'] );
		return $fields;
	}
	
	add_filter ( "woocommerce_checkout_fields", "order_billing_fields" );
	function order_billing_fields($fields) {
		$order = array (
				"billing_country",
				"billing_first_name",
				"billing_last_name",
				// "billing_location_type",
				"billing_address_1",
				"billing_address_2",
				"billing_city",
				"billing_state",
				"billing_postcode",
				"billing_email",
				"billing_phone" 
		);
		foreach ( $order as $field ) {
			$ordered_fields [$field] = $fields ["billing"] [$field];
		}
		
		$fields ["billing"] = $ordered_fields;
		return $fields;
	}
	
	/**
	 * Add custom field in custom area (other than shipping and billing forms)
	 */
	add_action ( 'woocommerce_after_order_notes', 'personal_message_checkout_field' );
	function personal_message_checkout_field($checkout) {
		echo '<div class="delivery-notice-container">*Encomendas feitas na terça ou quarta não entram na entrega desta semana, na sexta, sua encomenda sera entregue na proxima sexta.</div>';
		echo '<div class="entry-wrapper">';
		
		woocommerce_form_field ( 'birth_day', array (
				'type' => 'text',
				'class' => array (
						'form-row-wide' 
				),
				'label' => __ ( 'Insert your Birth Day' ),
				'clear' => false,
				'required' => true,
				'placeholder' => __ ( 'Birth Day' ) 
		), $checkout->get_value ( 'birth_day' ) );
		echo '</div>';
	}
	
	/**
	 * Process the checkout
	 */
	add_action ( 'woocommerce_checkout_process', 'wooredelivre_checkout_field_process' );
	function wooredelivre_checkout_field_process() {
		if ($_POST ['shipping'] ['shipping_location_type'] == "select") {
			wc_add_notice ( __ ( 'Please tell us the Location Type we are delivering to.' ), 'error' );
		}
		
		// if ($_POST['billing']['billing_location_type'] == "select") {
		// wc_add_notice(__('Please tell us the Location Type that we are billing to.'), 'error');
		// }
		
		if (! $_POST ['birth_day']) {
			wc_add_notice ( __ ( 'Please tell us the type of the Card that we have to deliver.' ), 'error' );
		}
	}
	
	/**
	 * Update the order meta with custom fields values
	 */
	add_action ( 'woocommerce_checkout_update_order_meta', 'wooredelivre_checkout_field_update_order_meta' );
	function wooredelivre_checkout_field_update_order_meta($order_id) {
		if (! empty ( $_POST ['shipping'] ['shipping_location_type'] )) {
			update_post_meta ( $order_id, 'Location Type', esc_attr ( $_POST ['shipping'] ['shipping_location_type'] ) );
		}
		// if (!empty($_POST['billing']['billing_location_type'])) {
		// update_post_meta($order_id, 'Location Type', esc_attr($_POST['billing']['billing_location_type']));
		// }
		
		if (! empty ( $_POST ['birth_day'] )) {
			update_post_meta ( $order_id, 'Card Type', sanitize_text_field ( $_POST ['birth_day'] ) );
		}
	}
	
	/**
	 * Display Custom Shipping fields and custom fields in custom area in the Order details area in Woocommerce->orders
	 */
	add_action ( 'woocommerce_admin_order_data_after_shipping_address', 'wooredelivre_fields_display_admin_order_meta', 10, 1 );
	function wooredelivre_fields_display_admin_order_meta($order) {
		echo '<p><strong>' . __ ( 'Email' ) . ':</strong><br> ' . get_post_meta ( $order->id, '_shipping_email', true ) . '</p>';
		echo '<p><strong>' . __ ( 'Phone' ) . ':</strong><br> ' . get_post_meta ( $order->id, '_shipping_phone', true ) . '</p>';
		// echo '<p><strong>' . __('Location Type') . ':</strong><br> ' . get_post_meta($order->id, '_shipping_location_type', true) . '</p>';
		// echo "<h4>Personal Message</h4>";
		echo '<p><strong>' . __ ( 'Birth Day' ) . ':</strong><br> ' . get_post_meta ( $order->id, 'Card Type', true ) . '</p>';
	}
	
	/**
	 * Display Custom Billing fields in the Order details area in Woocommerce->orders
	 */
	add_action ( 'woocommerce_admin_order_data_after_billing_address', 'wooredelivre_billing_fields_display_admin_order_meta', 10, 1 );
	function wooredelivre_billing_fields_display_admin_order_meta($order) {
		// echo '<p><strong>' . __('Location Type') . ':</strong><br> ' . get_post_meta($order->id, '_billing_location_type', true) . '</p>';
	}
	function add_redelivre_min_order_setting($settings) {
		$updated_settings = array ();
		foreach ( $settings as $section ) {
			// at the bottom of the General Options section
			if (isset ( $section ['id'] ) && 'general_options' == $section ['id'] && isset ( $section ['type'] ) && 'sectionend' == $section ['type']) {
				$updated_settings [] = array (
						'name' => __ ( 'Order minimal amount', 'wooredelivre' ),
						'desc_tip' => __ ( 'Set the minimal order amount value, default is 25, 0 to disable.', 'wooredelivre' ),
						'id' => 'woocommerce_redelivre_min_order',
						'type' => 'text',
						'css' => 'min-width:300px;',
						'std' => '25', // WC < 2.0
						'default' => '25', // WC >= 2.0
						'desc' => __ ( 'Set the minimal order amount value like at least $25.00', 'wooredelivre' ) 
				);
			}
			
			$updated_settings [] = $section;
		}
		
		return $updated_settings;
	}
	add_filter ( 'woocommerce_general_settings', 'add_redelivre_min_order_setting' );
}

?>
