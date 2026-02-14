<?php
/**
 * Handles frontend modifications based on settings
 */
class Woo_RQuote_Frontend {

	public function __construct() {
		add_action( 'wp', array( $this, 'apply_visibility_settings' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_quote_button' ) );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_quote_button_loop' ), 15 );
		add_action( 'wp_footer', array( $this, 'load_sidecart_template' ) );
	}

	public function apply_visibility_settings() {
		if ( get_option( 'woo_rquote_hide_price' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		}

		if ( get_option( 'woo_rquote_hide_add_to_cart' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}

	public function add_quote_button() {
		global $product;
		$text = get_option( 'woo_rquote_button_text', 'Add to Quote' );
		echo '<div class="rquote-add-btn-wrapper">';
		echo '<button type="button" class="rquote-add-btn button" data-product-id="' . esc_attr( $product->get_id() ) . '">' . esc_html( $text ) . '</button>';
		echo '</div>';
	}

	public function add_quote_button_loop() {
		global $product;
		$text = get_option( 'woo_rquote_button_text', 'Add to Quote' );
		echo '<div class="rquote-add-btn-wrapper">';
		echo '<button type="button" class="rquote-add-btn button" data-product-id="' . esc_attr( $product->get_id() ) . '">' . esc_html( $text ) . '</button>';
		echo '</div>';
	}

	public function load_sidecart_template() {
		include plugin_dir_path( __FILE__ ) . '../templates/side-cart.php';
	}
}

new Woo_RQuote_Frontend();
