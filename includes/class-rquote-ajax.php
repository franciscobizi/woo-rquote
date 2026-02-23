<?php
/**
 * Handles AJAX requests for the quote cart
 */
class Woo_RQuote_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_rquote_add_to_cart', array( $this, 'add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_rquote_add_to_cart', array( $this, 'add_to_cart' ) );

		add_action( 'wp_ajax_rquote_load_sidecart', array( $this, 'load_sidecart' ) );
		add_action( 'wp_ajax_nopriv_rquote_load_sidecart', array( $this, 'load_sidecart' ) );

		add_action( 'wp_ajax_rquote_remove_from_cart', array( $this, 'remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_rquote_remove_from_cart', array( $this, 'remove_from_cart' ) );

		add_action( 'wp_ajax_rquote_update_quantity', array( $this, 'update_quantity' ) );
		add_action( 'wp_ajax_nopriv_rquote_update_quantity', array( $this, 'update_quantity' ) );

		add_action( 'wp_ajax_rquote_submit_request', array( $this, 'submit_request' ) );
		add_action( 'wp_ajax_nopriv_rquote_submit_request', array( $this, 'submit_request' ) );
	}

	public function load_sidecart() {
		check_ajax_referer( 'woo_rquote_nonce', 'nonce' );
		ob_start();
		include plugin_dir_path( __FILE__ ) . '../templates/side-cart-content-v2.php';
		$content = ob_get_clean();
		wp_send_json_success( array( 'content' => $content, 'status' => true ) );
	}

	public function add_to_cart() {
		check_ajax_referer( 'woo_rquote_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

		if ( $product_id ) {
			Woo_RQuote_Session::add_to_cart( $product_id, $quantity );
			ob_start();
			include plugin_dir_path( __FILE__ ) . '../templates/side-cart-content-v2.php';
			$content = ob_get_clean();
			wp_send_json_success( array( 'content' => $content, 'message' => __( 'Product added to quote list.', 'woo-rquote' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'woo-rquote' ) ) );
	}

	public function remove_from_cart() {
		check_ajax_referer( 'woo_rquote_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

		if ( $product_id ) {
			Woo_RQuote_Session::remove_from_cart( $product_id );
			wp_send_json_success( array( 'message' => __( 'Product removed from quote list.', 'woo-rquote' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'woo-rquote' ) ) );
	}

	public function update_quantity() {
		check_ajax_referer( 'woo_rquote_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

		if ( $product_id ) {
			Woo_RQuote_Session::update_quantity( $product_id, $quantity );
			wp_send_json_success( array( 'message' => __( 'Quantity updated.', 'woo-rquote' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'woo-rquote' ) ) );
	}

	public function submit_request() {
		check_ajax_referer( 'woo_rquote_nonce', 'nonce' );

		$form_data = array();
		parse_str( $_POST['form_data'], $form_data );

		// Validate required fields
		if ( empty( $form_data['rquote_name'] ) || empty( $form_data['rquote_email'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'woo-rquote' ) ) );
		}

		$cart = Woo_RQuote_Session::get_cart();
		if ( empty( $cart ) ) {
			wp_send_json_error( array( 'message' => __( 'Your quote list is empty.', 'woo-rquote' ) ) );
		}

		// Create WooCommerce Order
		$order = wc_create_order();
		foreach ( $cart as $item ) {
			$order->add_product( wc_get_product( $item['product_id'] ), $item['quantity'] );
		}

		// Set order details from form
		$address = array(
			'first_name' => $form_data['rquote_name'],
			'email'      => $form_data['rquote_email'],
			'phone'      => $form_data['rquote_phone'],
			'address_1'  => $form_data['rquote_address'],
		);
		$order->set_address( $address, 'billing' );
		
		// Add custom meta data
		$order->update_meta_data( '_rquote_date', sanitize_text_field( $form_data['rquote_date'] ) );
		$order->update_meta_data( '_rquote_message', sanitize_textarea_field( $form_data['rquote_message'] ) );
		$order->set_status( 'pending', __( 'Thank you! Your quote request has been submitted.', 'woo-rquote' ) );
		$order->save();

		// Trigger Email
		do_action( 'woo_rquote_after_submit_request', $order->get_id(), $form_data );

		// Clear Cart
		Woo_RQuote_Session::clear_cart();

		wp_send_json_success( array( 
			'message' => __( 'Thank you! Your quote request has been submitted.', 'woo-rquote' ),
			'order_id' => $order->get_id()
		) );
	}
}

new Woo_RQuote_Ajax();
