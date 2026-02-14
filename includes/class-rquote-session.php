<?php
/**
 * Handles the quote cart session
 */
class Woo_RQuote_Session {

	private static $session_key = 'woo_rquote_cart';

	/**
	 * Get the quote cart from session
	 */
	public static function get_cart() {
		if ( ! WC()->session ) {
			return array();
		}
		return WC()->session->get( self::$session_key, array() );
	}

	/**
	 * Add item to quote cart
	 */
	public static function add_to_cart( $product_id, $quantity = 1 ) {
		$cart = self::get_cart();
		
		if ( isset( $cart[ $product_id ] ) ) {
			$cart[ $product_id ]['quantity'] += $quantity;
		} else {
			$cart[ $product_id ] = array(
				'product_id' => $product_id,
				'quantity'   => $quantity,
			);
		}

		self::set_cart( $cart );
		return $cart;
	}

	/**
	 * Remove item from quote cart
	 */
	public static function remove_from_cart( $product_id ) {
		$cart = self::get_cart();
		if ( isset( $cart[ $product_id ] ) ) {
			unset( $cart[ $product_id ] );
		}
		self::set_cart( $cart );
		return $cart;
	}

	/**
	 * Update quantity in quote cart
	 */
	public static function update_quantity( $product_id, $quantity ) {
		$cart = self::get_cart();
		if ( isset( $cart[ $product_id ] ) ) {
			if ( $quantity <= 0 ) {
				unset( $cart[ $product_id ] );
			} else {
				$cart[ $product_id ]['quantity'] = $quantity;
			}
		}
		self::set_cart( $cart );
		return $cart;
	}

	/**
	 * Clear the quote cart
	 */
	public static function clear_cart() {
		self::set_cart( array() );
	}

	/**
	 * Save cart to session
	 */
	private static function set_cart( $cart ) {
		if ( WC()->session ) {
			WC()->session->set( self::$session_key, $cart );
		}
	}
}
