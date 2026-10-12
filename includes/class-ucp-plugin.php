<?php
/**
 * Main companion plugin controller.
 *
 * @package UpdraftCentral_Persian
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UCP_Plugin
 */
class UCP_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var UCP_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton.
	 *
	 * @return UCP_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Wire hooks.
	 */
	public function init() {
		require_once UCP_DIR . 'includes/class-ucp-i18n.php';
		require_once UCP_DIR . 'includes/class-ucp-rtl.php';
		require_once UCP_DIR . 'includes/class-ucp-admin.php';

		UCP_I18n::instance()->init();
		UCP_RTL::instance()->init();
		UCP_Admin::instance()->init();
	}
}
