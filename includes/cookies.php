<?php
/**
 * Our cookie-related functions.
 *
 * @package WooCartExpiration
 */

// Declare our namespace.
namespace LiquidWeb\WooCartExpiration\Cookies;

// Set our aliases.
use LiquidWeb\WooCartExpiration as Core;
use LiquidWeb\WooCartExpiration\Utilities as Utilities;


/**
 * Set the cookie to begin the countdown.
 *
 * @param  string $cart_item_key  The key from the cart.
 *
 * @return void
 */
function set_cookie( $cart_item_key = '' ) {

	// Get our amount of time to expire.
	$stamps = Utilities\get_initial_expiration_times();

	// Now create a JSON encoded array so we can check stuff later.
	$setup  = array( 'expire' => absint( $stamps['expire'] ), 'cart' => $cart_item_key );

	// And set the cookie.
	setcookie( Core\COOKIE_NAME, base64_encode( maybe_serialize( $setup ) ), absint( $stamps['cookie'] ), '/' );
}

/**
 * Delete the cookie if it's present.
 *
 * @return void
 */
function clear_cookie() {

	// If we don't have the cookie, then no one cares.
	if ( ! isset( $_COOKIE[ Core\COOKIE_NAME ] ) ) {
		return;
	}

	// Set my reset time (in the past).
	$reset  = current_time( 'timestamp', true ) - 3600;

	// Unset the existing cookie.
	unset( $_COOKIE[ Core\COOKIE_NAME ] );

	// Now set a cookie in the past so it expires.
	setcookie( Core\COOKIE_NAME, '', absint( $reset ), '/' );
}

/**
 * Check the status of the cookie if it's present.
 *
 * @param  boolean $data  Whether to return the cookie data.
 *
 * @return mixed
 */
function check_cookie( $data = false ) {

	// If we don't have the cookie, then no one cares.
	if ( ! isset( $_COOKIE[ Core\COOKIE_NAME ] ) ) {
		return;
	}

	// Decode and unserialize the cookie.
	$cookie = maybe_unserialize( base64_decode( $_COOKIE[ Core\COOKIE_NAME ] ) );

	// Bail without a set of data or an expire time.
	if ( ! $cookie || empty( $cookie['expire'] ) ) {
		return false;
	}

	// If we requested the data, send it.
	if ( $data ) {
		return $cookie;
	}

	// Set my current time.
	$current_time   = current_time( 'timestamp', true );

	// Return true / false for expired.
	return absint( $cookie['expire'] ) >= absint( $current_time ) ? true : false;
}
