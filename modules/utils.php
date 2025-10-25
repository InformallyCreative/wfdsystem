<?php
/**
 * Utility helpers for the Waterfilter Direct System plugin.
 *
 * @package WFD\System
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve a plugin setting with optional default fallback.
 *
 * @param string $option_name Option key.
 * @param mixed  $default     Default value when option is not set.
 *
 * @return mixed
 */
function wfd_get_setting( $option_name, $default = false ) {
	$value = get_option( 'wfd_' . $option_name, $default );

	if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

	if ( is_string( $value ) ) {
		return sanitize_text_field( $value );
	}

return $value;
}

/**
 * Render a PHP template from the plugin templates directory.
 *
 * @param string $template_name Template filename.
 * @param array  $data          Data to extract to template scope.
 *
 * @return string
 */
function wfd_render_template( $template_name, $data = array() ) {
		$template_path = WFD_SYSTEM_PATH . 'templates/' . $template_name;

		if ( ! file_exists( $template_path ) ) {
			return '';
		}

	ob_start();
	extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	require $template_path;

	return (string) ob_get_clean();
}

/**
 * Generate a secure, single-use token for invoice access.
 *
 * @param int $invoice_id Invoice post ID.
 *
 * @return string
 */
function wfd_generate_invoice_token( $invoice_id ) {
	$token = wp_generate_password( 32, false, false );
	update_post_meta( $invoice_id, '_wfd_invoice_token', $token );
	update_post_meta( $invoice_id, '_wfd_invoice_token_created', time() );

	return $token;
}

/**
 * Validate the provided invoice token.
 *
 * @param int    $invoice_id Invoice ID.
 * @param string $token      Token string.
 *
 * @return bool
 */
function wfd_validate_invoice_token( $invoice_id, $token ) {
	$stored_token = get_post_meta( $invoice_id, '_wfd_invoice_token', true );
	$created      = (int) get_post_meta( $invoice_id, '_wfd_invoice_token_created', true );
	$expires      = (int) wfd_get_setting( 'invoice_token_expiration', DAY_IN_SECONDS * 7 );

	if ( empty( $stored_token ) || empty( $token ) ) {
		return false;
	}

if ( ! hash_equals( $stored_token, $token ) ) {
	return false;
}

if ( $created && ( time() - $created ) > $expires ) {
	return false;
}

return true;
}

/**
 * Format an amount as a currency value.
 *
 * @param float  $amount   Amount to format.
 * @param string $currency Currency code.
 *
 * @return string
 */
function wfd_format_currency( $amount, $currency = 'AUD' ) {
	$symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol( $currency ) : '';

	if ( empty( $symbol ) ) {
		$symbol = '$';
	}

return sprintf( '%s%s', $symbol, number_format_i18n( (float) $amount, 2 ) );
}

/**
 * Helper to fetch invoice meta with fallback.
 *
 * @param int    $invoice_id Invoice ID.
 * @param string $meta_key   Meta key (without prefix).
 * @param mixed  $default    Default value.
 *
 * @return mixed
 */
function wfd_get_invoice_meta( $invoice_id, $meta_key, $default = '' ) {
	$value = get_post_meta( $invoice_id, '_wfd_' . $meta_key, true );

	if ( '' === $value || null === $value ) {
		return $default;
	}

return $value;
}

/**
 * Store invoice meta value.
 *
 * @param int    $invoice_id Invoice ID.
 * @param string $meta_key   Meta key (without prefix).
 * @param mixed  $value      Value to store.
 *
 * @return void
 */
function wfd_update_invoice_meta( $invoice_id, $meta_key, $value ) {
	update_post_meta( $invoice_id, '_wfd_' . $meta_key, $value );
}
