<?php
/**
 * Plugin Name:       UpdraftCentral Persian
 * Plugin URI:        https://websila.co
 * Description:       فارسی‌سازی کامل و راستی‌چین‌سازی افزونه UpdraftCentral بدون تغییر فایل‌های اصلی (مقاوم در برابر بروزرسانی).
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Saeed Noori
 * Author URI:        https://saeedna.ir
 * Text Domain:       updraftcentral-persian
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package UpdraftCentral_Persian
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UCP_VERSION', '1.0.0' );
define( 'UCP_FILE', __FILE__ );
define( 'UCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'UCP_URL', plugin_dir_url( __FILE__ ) );
define( 'UCP_BASENAME', plugin_basename( __FILE__ ) );
define( 'UCP_UC_TEXTDOMAIN', 'updraftcentral' );

/**
 * Bootstrap the companion plugin after all plugins are registered.
 */
function ucp_bootstrap() {
	require_once UCP_DIR . 'includes/class-ucp-plugin.php';
	UCP_Plugin::instance()->init();
}
add_action( 'plugins_loaded', 'ucp_bootstrap', 0 );

/**
 * Activation checks.
 */
function ucp_activate() {
	if ( ! file_exists( WP_PLUGIN_DIR . '/updraftcentral/site-management.php' ) ) {
		deactivate_plugins( UCP_BASENAME );
		wp_die(
			esc_html__( 'افزونه UpdraftCentral Persian نیازمند نصب بودن UpdraftCentral است.', 'updraftcentral-persian' ),
			esc_html__( 'وابستگی یافت نشد', 'updraftcentral-persian' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( __FILE__, 'ucp_activate' );
