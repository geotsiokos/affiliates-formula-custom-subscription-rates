# affiliates-formula-custom-subscription-rates
An example of setting different rates to affiliates commissions on subscriptions, depending on whether it is a subscription's original order or a renewal.
The formula used in rate should be 'c * s' or 'c * t' and the values for the c variable can be set with the following hook:

add_filter( 'affiliates_formula_variable_crates', 'example_affiliates_formula_variable_crates' );
function example_affiliates_formula_variable_crates( $rates ) {
	$rates['default'] = 0.3;
	$rates['low_rate'] = 0.4;
	$rates['high_rate'] = 0.5;
	return $rates;
}
