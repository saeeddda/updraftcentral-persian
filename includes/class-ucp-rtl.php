<?php
/**
 * RTL styles, Bootstrap RTL swap, and Persian fonts for the UpdraftCentral dashboard.
 *
 * @package UpdraftCentral_Persian
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UCP_RTL
 */
class UCP_RTL {

	/**
	 * @var UCP_RTL|null
	 */
	private static $instance = null;

	/**
	 * @return UCP_RTL
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register hooks.
	 */
	public function init() {
		if ( ! UCP_I18n::instance()->should_localize() ) {
			return;
		}

		add_action( 'updraftcentral_load_dashboard_css', array( $this, 'enqueue_dashboard_rtl' ), 20 );
		// UpdraftCentral enqueues LTR Bootstrap on wp_footer (priority 10); swap after that.
		add_action( 'wp_footer', array( $this, 'swap_bootstrap_rtl' ), 20 );
		add_action( 'wp_footer', array( $this, 'requeue_modal_assets' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_front' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_rtl' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_head', array( $this, 'print_dir_hint' ), 1 );
		add_action( 'admin_head', array( $this, 'print_dir_hint' ), 1 );
	}

	/**
	 * Enqueue companion RTL + font CSS on the UC dashboard CSS hook.
	 *
	 * @param string|int $enqueue_version Version from UpdraftCentral.
	 */
	public function enqueue_dashboard_rtl( $enqueue_version ) {
		unset( $enqueue_version );

		// jsDelivr is more reachable in Iran than fonts.googleapis.com.
		wp_enqueue_style(
			'ucp-vazirmatn',
			'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css',
			array(),
			'33.003'
		);

		wp_enqueue_style(
			'ucp-fonts',
			UCP_URL . 'assets/css/fonts.css',
			array(
				'ucp-vazirmatn',
				'updraftcentral-dashboard-css',
				'updraftcentral-dashboard-temp-css',
				'updraftcentral-new-design',
			),
			UCP_VERSION
		);

		wp_enqueue_style(
			'ucp-rtl',
			UCP_URL . 'assets/css/rtl.css',
			array(
				'updraftcentral-dashboard-css',
				'updraftcentral-mobile-css',
				'updraftcentral-dashboard-temp-css',
				'updraftcentral-new-design',
				'ucp-fonts',
			),
			UCP_VERSION
		);

		wp_enqueue_script(
			'ucp-rtl',
			UCP_URL . 'assets/js/rtl.js',
			array( 'jquery' ),
			UCP_VERSION,
			true
		);
	}

	/**
	 * Ensure font/RTL CSS stay after Bootstrap and theme styles in the footer.
	 */
	public function requeue_modal_assets() {
		if ( ! $this->is_dashboard_context() ) {
			return;
		}
		if ( wp_style_is( 'ucp-vazirmatn', 'registered' ) ) {
			wp_enqueue_style( 'ucp-vazirmatn' );
		}
		if ( wp_style_is( 'ucp-fonts', 'registered' ) ) {
			wp_enqueue_style( 'ucp-fonts' );
		}
		if ( wp_style_is( 'ucp-rtl', 'registered' ) ) {
			wp_enqueue_style( 'ucp-rtl' );
		}
	}

	/**
	 * Replace LTR Bootstrap with the RTL build that ships with UpdraftCentral.
	 */
	public function swap_bootstrap_rtl() {
		if ( ! $this->is_dashboard_context() ) {
			return;
		}

		$uc_url = defined( 'UD_CENTRAL_URL' ) ? UD_CENTRAL_URL : plugins_url( 'updraftcentral' );
		$min    = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$ver    = defined( 'UD_CENTRAL_DIR' ) && class_exists( 'UpdraftCentral' )
			? null
			: UCP_VERSION;

		if ( wp_style_is( 'bootstrap5', 'enqueued' ) || wp_style_is( 'bootstrap5', 'registered' ) ) {
			wp_dequeue_style( 'bootstrap5' );
			wp_deregister_style( 'bootstrap5' );
		}

		wp_enqueue_style(
			'bootstrap5-rtl',
			$uc_url . '/css/bootstrap/bootstrap.rtl' . $min . '.css',
			array(),
			$ver ? $ver : UCP_VERSION
		);
	}

	/**
	 * Front-end safety net if dashboard CSS hook did not fire yet.
	 */
	public function maybe_enqueue_front() {
		if ( ! $this->is_dashboard_context() ) {
			return;
		}

		if ( ! wp_style_is( 'ucp-rtl', 'enqueued' ) ) {
			$this->enqueue_dashboard_rtl( UCP_VERSION );
		}
	}

	/**
	 * Admin page (Settings → UpdraftCentral) RTL polish.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_rtl( $hook ) {
		if ( false === strpos( $hook, 'updraft-central' ) && ! $this->is_uc_admin_page() ) {
			return;
		}

		wp_enqueue_style(
			'ucp-vazirmatn',
			'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css',
			array(),
			'33.003'
		);

		wp_enqueue_style(
			'ucp-fonts',
			UCP_URL . 'assets/css/fonts.css',
			array( 'ucp-vazirmatn' ),
			UCP_VERSION
		);

		wp_enqueue_style(
			'ucp-admin-rtl',
			UCP_URL . 'assets/css/admin-rtl.css',
			array( 'ucp-fonts' ),
			UCP_VERSION
		);
	}

	/**
	 * Add helper body classes.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public function body_class( $classes ) {
		if ( $this->is_dashboard_context() ) {
			$classes[] = 'ucp-rtl';
			$classes[] = 'updraftcentral-persian';
		}
		return $classes;
	}

	/**
	 * Ensure dashboard root inherits RTL direction early.
	 */
	public function print_dir_hint() {
		if ( ! $this->is_dashboard_context() && ! $this->is_uc_admin_page() ) {
			return;
		}
		echo "<style id=\"ucp-dir-hint\">html,body,#updraftcentral_dashboard,#updraft-central-content-container,#updraft-central-content,#updraftcentral_modal_dialog,.bootbox.modal,.modal{direction:rtl!important;text-align:right!important;font-family:Vazirmatn,Tahoma,\"Source Sans Pro\",Roboto,\"Helvetica Neue\",Arial,sans-serif!important;}#updraftcentral_dashboard h1,#updraftcentral_dashboard h2,#updraftcentral_dashboard h3,#updraftcentral_dashboard h4,#updraftcentral_dashboard h5,#updraftcentral_dashboard h6,#updraftcentral_dashboard .screen-title,#updraftcentral_modal_dialog h1,#updraftcentral_modal_dialog h2,#updraftcentral_modal_dialog h3,#updraftcentral_modal_dialog h4,#updraftcentral_modal_dialog h5,#updraftcentral_modal_dialog h6,#updraftcentral_modal_dialog .modal-title,.bootbox.modal h1,.bootbox.modal h2,.bootbox.modal h3,.bootbox.modal h4,.bootbox.modal h5,.bootbox.modal h6,.bootbox.modal .modal-title{font-family:Vazirmatn,Tahoma,\"Source Sans Pro\",Roboto,\"Helvetica Neue\",Arial,sans-serif!important;}</style>\n";
	}

	/**
	 * Detect UpdraftCentral dashboard (front shortcode page or fullscreen UI).
	 *
	 * @return bool
	 */
	private function is_dashboard_context() {
		if ( is_admin() ) {
			return $this->is_uc_admin_page();
		}

		global $post;
		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'updraft_central' ) ) {
			return true;
		}

		if ( isset( $_GET['updraftcentral'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		return (bool) apply_filters( 'ucp_is_dashboard_context', false );
	}

	/**
	 * Detect WP admin UpdraftCentral page.
	 *
	 * @return bool
	 */
	private function is_uc_admin_page() {
		if ( ! is_admin() ) {
			return false;
		}
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( 'updraft-central' === $page );
	}
}
