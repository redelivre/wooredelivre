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


/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
}
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

// add_filter('gettext', 'change_checkout_btn');
// add_filter('ngettext', 'change_checkout_btn');
// 
// //function
// function change_checkout_btn($checkout_btn){
//   $checkout_btn= str_ireplace('Company Name', 'CPF', $checkout_btn);
//   return $checkout_btn;
// }

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
     $fields['billing']['billing_company']['label'] = 'CPF';
     $fields['billing']['billing_company']['required'] = true;
     return $fields;
}



