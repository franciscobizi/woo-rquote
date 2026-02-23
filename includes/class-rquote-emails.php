<?php
/**
 * Handles custom emails for quote requests
 */
class Woo_RQuote_Emails {

	public function __construct() {
		add_action( 'woo_rquote_after_submit_request', array( $this, 'send_quote_email' ), 10, 2 );
	}

	public function send_quote_email( $order_id, $form_data ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) return;

		$admin_email = get_option( 'admin_email' );
		/* translators: 1: order ID, 2: customer name */
		$subject = sprintf( __( 'New Quote Request #%1$s from %2$s', 'woo-rquote' ), $order_id, $form_data['rquote_name'] );
		
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$headers[] = 'Reply-To: ' . $form_data['rquote_name'] . ' <' . $form_data['rquote_email'] . '>';
		$headers[] = 'From: Shantipi <sells@shantipi.co.il>';

		$message = $this->get_email_content( $order, $form_data );

		// Send to Admin
		wp_mail( $admin_email, $subject, $message, $headers );

		// Send confirmation to Customer
		wp_mail( $form_data['rquote_email'], __( 'Your Quote Request Confirmation', 'woo-rquote' ), $message, $headers );
	}

	private function get_email_content( $order, $form_data ) {
		ob_start();
		?>
		<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
			<img src="<?php echo esc_url( WOO_RQUOTE_URL . 'assets/images/email-header.png' ); ?>" style="display: block; width: 80px; height: auto; margin-left: auto;" alt="<?php bloginfo( 'name' ); ?> Logo">
			<h2 style="color: #731616; border-bottom: 2px solid #731616; padding-bottom: 10px; direction: rtl;">
				<?php _e( 'Quote Request Details', 'woo-rquote' ); ?>
			</h2>
			
			<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
				<thead>
					<tr style="background: #f4f4f4;">
						<th style="padding: 10px; border: 1px solid #ddd;  direction: rtl;"><?php _e( 'Quantity', 'woo-rquote' ); ?></th>
						<th style="padding: 10px; border: 1px solid #ddd; direction: rtl; text-align: right;"><?php _e( 'Product', 'woo-rquote' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $order->get_items() as $item_id => $item ) : 
						$product = $item->get_product();
						?>
						<tr>
							<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
								<?php echo esc_html( $item->get_quantity() ); ?>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;">
								<?php if ( $product ) : ?>
									<div style="display: flex; align-items: center;  direction: rtl;">
										<div style="margin-left: 10px;">
											<?php echo $product->get_image( array( 50, 50 ) ); ?>
										</div>
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" style="color: #731616; text-decoration: none;">
											<?php echo esc_html( $item->get_name() ); ?>
										</a>
									</div>
								<?php else : ?>
									<?php echo esc_html( $item->get_name() ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h3 style="color: #444; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; direction: rtl;">
				<?php _e( 'Customer Information', 'woo-rquote' ); ?>
			</h3>
			<table style="width: 100%; border-collapse: collapse; direction: rtl;">
				<tr>
					<td style="padding: 8px 0; font-weight: bold; width: 60px;"><?php _e( 'Name:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo esc_html( $form_data['rquote_name'] ); ?></td>
				</tr>
				<tr>
					<td style="padding: 8px 0; font-weight: bold;"><?php _e( 'Email:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo esc_html( $form_data['rquote_email'] ); ?></td>
				</tr>
				<tr>
					<td style="padding: 8px 0; font-weight: bold;"><?php _e( 'Phone:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo esc_html( $form_data['rquote_phone'] ); ?></td>
				</tr>
				<tr>
					<td style="padding: 8px 0; font-weight: bold;"><?php _e( 'Address:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo esc_html( $form_data['rquote_address'] ); ?></td>
				</tr>
				<tr>
					<td style="padding: 8px 0; font-weight: bold;"><?php _e( 'Date:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo esc_html( $form_data['rquote_date'] ); ?></td>
				</tr>
				<tr>
					<td style="padding: 8px 0; font-weight: bold; vertical-align: top;"><?php _e( 'Message:', 'woo-rquote' ); ?></td>
					<td style="padding: 8px 0;"><?php echo nl2br( esc_html( $form_data['rquote_message'] ) ); ?></td>
				</tr>
			</table>
			
				<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #888; direction: rtl;">
					<p><?php /* translators: %s: site title. */ printf( __( 'This request was sent from %s.', 'woo-rquote' ), 'Shantipi' ); ?></p>
				</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

new Woo_RQuote_Emails();
