<?php
/**
 * Plugin Name: Affiliates Formula Custom Subscription Rates
 * Plugin URI: http://www.itthinx.com/shop/affiliates-pro/
 * Description: Example formula for subscription commissions, the formula used in rate should be 'c * s' or 'c * t'
 * Version: 1.0.0
 * Author: gtsiokos
 * Author URI: http://www.netpad.gr
 * License: GPLv3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Affiliates_Formula_Custom_Subscription_Rates {

	/**
	 * Init
	 */
	public static function init() {
		add_filter( 'affiliates_formula_computer_variables', array( __CLASS__, 'affiliates_formula_computer_variables' ), 10, 3 );
	}

	/**
	 * Sets a formula variable to be used in commission calculations for subscriptions by WCS
	 * For the rate the formula should be 'c * s' or 'c * t'
	 * @param array $variables
	 * @param object $rate
	 * @param array $context
	 * @return array
	 */
	public static function affiliates_formula_computer_variables( $variables, $rate, $context ) {
		$rates = self::formula_variable_default_rates();
		$variables['c'] = $rates['default'];

		if ( isset( $context['order_id'] ) && isset( $context['order_item_id'] ) ) {
			if ( get_option( 'woocommerce_subscriptions_active_version' ) ) {
				$order = self::get_order( $context['order_id'] );
				if ( $order ) {
					$order_item = $order->get_item( $context['order_item_id'] );
					if ( $order_item ) {
						$product = $order_item->get_product();
						$product_id = $product ? $product->get_id() : null;
					}
				}
			}
		}

		if ( $product_id ) {
			if (
				!method_exists( 'WC_Subscriptions_Product', 'is_subscription' ) ||
				!function_exists( 'wcs_order_contains_renewal' )
			) {
				require_once( ABSPATH . 'wp-content/plugins/woocommerce-subscriptions/woocommerce-subscriptions.php' );
			}

			if ( WC_Subscriptions_Product::is_subscription( $product_id ) ) {
				if ( wcs_order_contains_renewal( $context['order_id'] ) ) {
					$variables['c'] = $rates['low_rate'];
				} else {
					$variables['c'] = $rates['high_rate'];
				}
			}
		}
		return $variables;
	}

	/**
	 * Default variable values including low - high - and default rates.
	 * Can be modified with affiliates_formula_variable_crates filter hook
	 *
	 * @return array
	 */
	public static function formula_variable_default_rates() {
		$rates = array();
		$rates['default'] = 0.20;
		$rates['low_rate'] = 0.05;
		$rates['high_rate'] = 0.10;
		return apply_filters( 'affiliates_formula_variable_crates', $rates );
	}
	/**
	 * Retrieve an order.
	 *
	 * @param int $order_id
	 * @return WC_Order or null
	 */
	public static function get_order( $order_id = '' ) {
		$result = null;
		if ( function_exists( 'wc_get_order' ) ) {
			if ( $order = wc_get_order( $order_id ) ) {
				$result = $order;
			}
		} else if ( class_exists( 'WC_Order' ) ) {
			$order = new WC_Order( $order_id );
			if ( $order->get_order( $order_id ) ) {
				$result = $order;
			}
		} else {
			$order = new woocommerce_order();
			if ( method_exists( $order, 'get_order' ) ) {
				if ( $order->get_order( $order_id ) ) {
					$result = $order;
				}
			}
		}
		return $result;
	}
} Affiliates_Formula_Custom_Subscription_Rates::init();
