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

// ADD Custom Fields to Checkout Page
/**
 * Add the field to the checkout
 **/

add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {

    date_default_timezone_set('America/Sao_Paulo');
    $mydateoptions = array('' => __('Select BirthDay', 'woocommerce' ));

    echo '<div id="my_custom_checkout_field"><h3>'.__('Informacionais Adicionais').'</h3>';

   woocommerce_form_field( 'birth_date', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'id'            => 'datepicker',
        'required'      => true,
        'label'         => __('Data de Nascimento'),
        'placeholder'       => __('Select Date'),
        'options'     =>   $mydateoptions
      ),$checkout->get_value( 'birth_date' ));

    echo '</div>';
}

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
    global $woocommerce;

    // Check if set, if its not set add an error.
    if (!$_POST['birth_date'])
         wc_add_notice( '<strong>Data de Nascimento</strong> ' . __( 'is a required field.', 'woocommerce' ), 'error' );
}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ($_POST['birth_date']) update_post_meta( $order_id, 'BirthDay', esc_attr($_POST['birth_date']));
}
?>
