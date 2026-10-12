<?php
/**
 * Admin notices and plugin row meta.
 *
 * @package UpdraftCentral_Persian
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UCP_Admin
 */
class UCP_Admin {

	/**
	 * @var UCP_Admin|null
	 */
	private static $instance = null;

	/**
	 * @return UCP_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hooks.
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'maybe_missing_uc_notice' ) );
		add_filter( 'plugin_action_links_' . UCP_BASENAME, array( $this, 'action_links' ) );
	}

	/**
	 * Warn if UpdraftCentral is inactive.
	 */
	public function maybe_missing_uc_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( defined( 'UD_CENTRAL_DIR' ) || class_exists( 'UpdraftCentral', false ) ) {
			return;
		}

		if ( ! file_exists( WP_PLUGIN_DIR . '/updraftcentral/site-management.php' ) ) {
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'افزونه UpdraftCentral Persian نصب است، اما UpdraftCentral یافت نشد. لطفاً UpdraftCentral را نصب کنید.', 'updraftcentral-persian' );
			echo '</p></div>';
			return;
		}

		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'برای اعمال فارسی‌سازی، افزونه UpdraftCentral را فعال کنید.', 'updraftcentral-persian' );
		echo '</p></div>';
	}

	/**
	 * Plugin list links.
	 *
	 * @param string[] $links Links.
	 * @return string[]
	 */
	public function action_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=updraft-central' ) ) . '">' . esc_html__( 'داشبورد UpdraftCentral', 'updraftcentral-persian' ) . '</a>';
		return $links;
	}
}
