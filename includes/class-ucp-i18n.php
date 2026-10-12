<?php
/**
 * Loads Persian translations for UpdraftCentral without modifying the core plugin.
 *
 * @package UpdraftCentral_Persian
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UCP_I18n
 */
class UCP_I18n {

	/**
	 * Singleton.
	 *
	 * @var UCP_I18n|null
	 */
	private static $instance = null;

	/**
	 * @return UCP_I18n
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Optional PHP dictionary fallback (bin/dict-fa.php).
	 *
	 * @var array<string,string>
	 */
	private $dict = array();

	/**
	 * Register hooks.
	 */
	public function init() {
		$this->load_dict();
		add_filter( 'load_textdomain_mofile', array( $this, 'override_mofile' ), 10, 2 );
		add_action( 'plugins_loaded', array( $this, 'load_updraftcentral_persian' ), 1 );
		add_action( 'init', array( $this, 'load_own_textdomain' ) );
		add_filter( 'gettext', array( $this, 'filter_hardcoded_strings' ), 20, 3 );
		// Dict overrides MO so quality fixes apply immediately.
		add_filter( 'gettext', array( $this, 'filter_dict_override' ), 30, 3 );
		add_filter( 'gettext_with_context', array( $this, 'filter_dict_override_context' ), 30, 4 );
		add_filter( 'ngettext', array( $this, 'filter_hardcoded_nstrings' ), 20, 5 );
	}

	/**
	 * Load hand-crafted / generated dictionary if present.
	 */
	private function load_dict() {
		$path = UCP_DIR . 'bin/dict-fa.php';
		if ( file_exists( $path ) ) {
			$dict = include $path;
			if ( is_array( $dict ) ) {
				$this->dict = $dict;
			}
		}
	}

	/**
	 * Whether Persian localization should apply.
	 *
	 * @return bool
	 */
	public function should_localize() {
		$locale = determine_locale();
		$apply  = ( 0 === strpos( $locale, 'fa' ) ) || is_rtl();

		/**
		 * Filter whether Persian localization should run.
		 *
		 * @param bool   $apply  Default based on locale/RTL.
		 * @param string $locale Current locale.
		 */
		return (bool) apply_filters( 'ucp_should_localize', $apply, $locale );
	}

	/**
	 * Absolute path to the companion MO file for a locale.
	 *
	 * @param string $locale Locale code.
	 * @return string
	 */
	public function mo_path( $locale = '' ) {
		if ( '' === $locale ) {
			$locale = determine_locale();
		}

		// Prefer exact locale, then fa_IR fallback for any fa_* locale.
		$candidates = array(
			UCP_DIR . 'languages/updraftcentral-' . $locale . '.mo',
			UCP_DIR . 'languages/updraftcentral-fa_IR.mo',
		);

		foreach ( $candidates as $path ) {
			if ( file_exists( $path ) ) {
				return $path;
			}
		}

		return $candidates[1];
	}

	/**
	 * Force UpdraftCentral to load our MO when available.
	 *
	 * @param string $mofile Path WordPress wants to load.
	 * @param string $domain Text domain.
	 * @return string
	 */
	public function override_mofile( $mofile, $domain ) {
		if ( UCP_UC_TEXTDOMAIN !== $domain || ! $this->should_localize() ) {
			return $mofile;
		}

		$custom = $this->mo_path();
		return file_exists( $custom ) ? $custom : $mofile;
	}

	/**
	 * Eagerly load translations early so JS localize arrays are Persian.
	 */
	public function load_updraftcentral_persian() {
		if ( ! $this->should_localize() ) {
			return;
		}

		$mo = $this->mo_path();
		if ( file_exists( $mo ) ) {
			load_textdomain( UCP_UC_TEXTDOMAIN, $mo );
		}
	}

	/**
	 * Load this companion plugin's own text domain.
	 */
	public function load_own_textdomain() {
		load_plugin_textdomain( 'updraftcentral-persian', false, dirname( UCP_BASENAME ) . '/languages' );
	}

	/**
	 * Translate a few hardcoded English strings that UpdraftCentral does not wrap in __().
	 *
	 * @param string $translation Current translation.
	 * @param string $text        Original text.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function filter_hardcoded_strings( $translation, $text, $domain ) {
		if ( ! $this->should_localize() ) {
			return $translation;
		}

		// Only touch default / unmatched domains for hardcoded admin menu title.
		$map = array(
			'UpdraftCentral' => 'آپ‌درافت سنترال',
			'FAQs'           => 'سؤالات متداول',
		);

		if ( isset( $map[ $text ] ) && ( 'default' === $domain || UCP_UC_TEXTDOMAIN === $domain || '' === $domain ) ) {
			// Avoid rewriting when a proper MO translation already differs.
			if ( $translation === $text || 'default' === $domain || '' === $domain ) {
				return $map[ $text ];
			}
		}

		return $translation;
	}

	/**
	 * Pass-through for plurals (reserved for future hardcoded plurals).
	 *
	 * @param string $translation Translated string.
	 * @param string $single      Singular.
	 * @param string $plural      Plural.
	 * @param int    $number      Number.
	 * @param string $domain      Domain.
	 * @return string
	 */
	public function filter_hardcoded_nstrings( $translation, $single, $plural, $number, $domain ) {
		unset( $single, $plural, $number, $domain );
		return $translation;
	}

	/**
	 * Prefer PHP dictionary over MO (fixes mixed/heuristic translations).
	 *
	 * @param string $translation Current translation.
	 * @param string $text        Original.
	 * @param string $domain      Domain.
	 * @return string
	 */
	public function filter_dict_override( $translation, $text, $domain ) {
		if ( UCP_UC_TEXTDOMAIN !== $domain || ! $this->should_localize() ) {
			return $translation;
		}

		if ( isset( $this->dict[ $text ] ) && '' !== $this->dict[ $text ] ) {
			return $this->dict[ $text ];
		}

		return $translation;
	}

	/**
	 * Same override for contextual gettext.
	 *
	 * @param string $translation Translation.
	 * @param string $text        Original.
	 * @param string $context     Context.
	 * @param string $domain      Domain.
	 * @return string
	 */
	public function filter_dict_override_context( $translation, $text, $context, $domain ) {
		unset( $context );
		return $this->filter_dict_override( $translation, $text, $domain );
	}
}
