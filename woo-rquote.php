<?php
/**
 * Plugin Name: Woo RQuote
 * Plugin URI: https://github.com/franciscobizi/woo-rquote
 * Description: A production-ready Request a Quote plugin for WooCommerce.
 * Version: 1.0.0
 * Author: Francisco Bizi
 * Author URI: https://github.com/franciscobizi
 * Text Domain: woo-rquote
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define plugin constants
define( 'WOO_RQUOTE_VERSION', '1.0.0' );
define( 'WOO_RQUOTE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_RQUOTE_URL', plugin_dir_url( __FILE__ ) );
define( 'WOO_RQUOTE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Class for Woo RQuote
 */
class Woo_RQuote {

	/**
	 * Instance of this class
	 * @var Woo_RQuote
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin
	 */
	public function __construct() {
		// Check if WooCommerce is active
		if ( ! $this->is_woocommerce_active() ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Check if WooCommerce is active
	 */
	private function is_woocommerce_active() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Display WooCommerce missing notice
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'Woo RQuote requires WooCommerce to be installed and active.', 'woo-rquote' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Include required files
	 */
	private function includes() {
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-session.php';
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-ajax.php';
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-shortcode.php';
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-emails.php';
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-settings.php';
		require_once WOO_RQUOTE_PATH . 'includes/class-rquote-frontend.php';
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'woo-rquote-style', WOO_RQUOTE_URL . 'assets/css/woo-rquote.css', array(), WOO_RQUOTE_VERSION );
		wp_enqueue_script( 'woo-rquote-script', WOO_RQUOTE_URL . 'assets/js/woo-rquote.js', array( 'jquery', 'wp-i18n' ), WOO_RQUOTE_VERSION, true );

		wp_localize_script( 'woo-rquote-script', 'woo_rquote_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'woo_rquote_nonce' ),
		) );

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'woo-rquote-script', 'woo-rquote', WOO_RQUOTE_PATH . 'languages' );
		}
	}
}

// Initialize the plugin
add_action( 'plugins_loaded', array( 'Woo_RQuote', 'get_instance' ) );
