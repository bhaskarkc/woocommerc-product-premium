<?php

/**
 * Plugin Name: Woocommerce Product Premium
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Adds premium price for choosen product.
 * Version: 1.0.0
 * Author: Bhaskar K C
 * Author URI: http://bhaskarkc.net/
 * Developer: Bhaskar K C
 * Developer URI: http://bhaskarkc.net/
 * Text Domain: woocommerce-product-premium
 *
 * Copyright: © 2017 Bhaskar K C.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if acccessed directly
defined( 'ABSPATH' ) || exit;

// Bail early if woocommerce plugin is not active/exists.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

// include premium product price admin class
require_once( __DIR__ . '/classes/class-wc-premium-price-admin.php' );
// include premium product price class
require_once( __DIR__ . '/classes/class-wc-premium-price.php' );

// creates WC_Premium_Price_Admin instance.
WC_Premium_Price_Admin::instance();

// Create instance of WC_Premium_Price
WC_Premium_Price::instance();

