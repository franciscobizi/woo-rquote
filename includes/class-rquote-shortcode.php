<?php
/**
 * Handles the [woo_rquote_list] shortcode
 */
class Woo_RQuote_Shortcode {

	public function __construct() {
		add_shortcode( 'woo_rquote_list', array( $this, 'render_shortcode' ) );
	}

	public function render_shortcode() {
		$cart = Woo_RQuote_Session::get_cart();
		
		ob_start();
		?>
		<div id="woo-rquote-container" class="woo-rquote-container">
			<?php if ( empty( $cart ) ) : ?>
				<p class="woo-rquote-empty-msg"><?php _e( 'Your quote list is currently empty.', 'woo-rquote' ); ?></p>
			<?php else : ?>
				<div class="woo-rquote-table-wrapper">
					<table class="woo-rquote-table">
						<thead>
							<tr>
								<th><?php _e( 'Product', 'woo-rquote' ); ?></th>
								<th><?php _e( 'Quantity', 'woo-rquote' ); ?></th>
								<th><?php _e( 'Actions', 'woo-rquote' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $cart as $item_id => $item ) : 
								$product = wc_get_product( $item['product_id'] );
								if ( ! $product ) continue;
								?>
								<tr data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>">
									<td class="product-info">
										<div class="product-thumbnail" style="margin-left: 10px;">
											<?php echo $product->get_image( 'thumbnail' ); ?>
										</div>
										<div class="product-name">
											<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
												<?php echo esc_html( $product->get_name() ); ?>
											</a>
										</div>
									</td>
									<td class="product-quantity">
										<div class="quantity-controls">
											<button type="button" class="qty-btn minus">-</button>
											<input type="number" class="qty-input" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="1" readonly>
											<button type="button" class="qty-btn plus">+</button>
										</div>
									</td>
									<td class="product-remove">
										<button type="button" class="remove-item-btn" data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>">
											&times;
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="woo-rquote-form-wrapper">
					<h3><?php _e( 'Submit Your Quote Request', 'woo-rquote' ); ?></h3>
					<form id="woo-rquote-submit-form">
						<div class="form-row">
							<label for="rquote_name"><?php _e( 'Name *', 'woo-rquote' ); ?></label>
							<input type="text" id="rquote_name" name="rquote_name" required>
						</div>
						<div class="form-row">
							<label for="rquote_email"><?php _e( 'Email *', 'woo-rquote' ); ?></label>
							<input type="email" id="rquote_email" name="rquote_email" required>
						</div>
						<div class="form-row">
							<label for="rquote_phone"><?php _e( 'Phone', 'woo-rquote' ); ?></label>
							<input type="text" id="rquote_phone" name="rquote_phone">
						</div>
						<div class="form-row">
							<label for="rquote_address"><?php _e( 'Address', 'woo-rquote' ); ?></label>
							<input type="text" id="rquote_address" name="rquote_address">
						</div>
						<div class="form-row">
							<label for="rquote_date"><?php _e( 'Preferred Date', 'woo-rquote' ); ?></label>
							<input type="date" id="rquote_date" name="rquote_date">
						</div>
						<div class="form-row">
							<label for="rquote_message"><?php _e( 'Message', 'woo-rquote' ); ?></label>
							<textarea id="rquote_message" name="rquote_message" rows="4"></textarea>
						</div>
						<div class="form-submit">
							<button type="submit" class="button alt" id="woo-rquote-submit-btn">
								<?php _e( 'Send Request', 'woo-rquote' ); ?>
							</button>
							<span class="spinner-loader" style="display:none;"></span>
						</div>
					</form>
				</div>
				<div id="woo-rquote-msg"></div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

new Woo_RQuote_Shortcode();
