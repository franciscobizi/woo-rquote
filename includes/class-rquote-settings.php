<?php
/**
 * Handles plugin settings in the WordPress admin
 */
class Woo_RQuote_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page() {
		add_submenu_page(
			'woocommerce',
			__( 'Woo RQuote Settings', 'woo-rquote' ),
			__( 'Quote Settings', 'woo-rquote' ),
			'manage_options',
			'woo-rquote-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'woo_rquote_options', 'woo_rquote_hide_price' );
		register_setting( 'woo_rquote_options', 'woo_rquote_hide_add_to_cart' );
		register_setting( 'woo_rquote_options', 'woo_rquote_button_text' );
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Woo RQuote Settings', 'woo-rquote' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'woo_rquote_options' );
				do_settings_sections( 'woo_rquote_options' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide Price', 'woo-rquote' ); ?></th>
						<td>
							<input type="checkbox" name="woo_rquote_hide_price" value="1" <?php checked( 1, get_option( 'woo_rquote_hide_price' ), true ); ?> />
							<p class="description"><?php _e( 'Hide the product price on shop and product pages.', 'woo-rquote' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide Add to Cart Button', 'woo-rquote' ); ?></th>
						<td>
							<input type="checkbox" name="woo_rquote_hide_add_to_cart" value="1" <?php checked( 1, get_option( 'woo_rquote_hide_add_to_cart' ), true ); ?> />
							<p class="description"><?php _e( 'Hide the default WooCommerce Add to Cart button.', 'woo-rquote' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Quote Button Text', 'woo-rquote' ); ?></th>
						<td>
							<input type="text" name="woo_rquote_button_text" value="<?php echo esc_attr( get_option( 'woo_rquote_button_text', 'Add to Quote' ) ); ?>" />
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<div class="rquote-info" style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
				<h3><?php _e( 'How to use', 'woo-rquote' ); ?></h3>
				<p><?php _e( 'Use the shortcode <code>[woo_rquote_list]</code> on any page to display the quote request list and form.', 'woo-rquote' ); ?></p>
			</div>
		</div>
		<?php
	}
}

new Woo_RQuote_Settings();
