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

class Affiliates_Formula_Custom_Subscription_Rates {

	const LOW_RATE = 0.05;
	const HIGH_RATE = 0.10;

	/**
	 * Init
	 */
	public static function init() {
		add_filter( 'affiliates_formula_computer_variables', array( __CLASS__, 'affiliates_formula_computer_variables', 10, 3 ) );
	}

	/**
	 * Sets a formula variable to be used in commission calculations for subscriptions by WCS
	 * For the rate the formula should be 'c * s' or 'c * t'
	 * @param array $variables
	 * @param object $rate
	 * @param array $context
	 * @return array
	 */
	public static function affiliates_formula_computer_variables( $variables , $rate, $context ) {
		if ( isset( $context['order_id'] ) && isset( $context['order_item_id'] ) ) {
			if ( get_option( 'woocommerce_subscriptions_active_version' ) ) {
				if (
					method_exists( 'WC_Subscriptions_Order', 'is_item_subscription' ) &&
					method_exists( 'WC_Subscriptions_Renewal_Order', 'is_renewal' )
				) {
					if ( WC_Subscriptions_Order::is_item_subscription( $context['order_id'], $context['order_item_id'] ) ) {
						if ( WC_Subscriptions_Renewal_Order::is_renewal( $context['order_id'] ) ) {
							$variables['c'] = self::LOW_RATE;
						} else {
							$variables['c'] = self::HIGH_RATE;
						}
					}
				}
			}
		}
		return $variables;
	}
} Affiliates_Formula_Custom_Subscription_Rates::init();
