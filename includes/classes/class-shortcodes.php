<?php
/**
 * Who Hit The Page Shortcodes
 *
 * @description adds all shortcodes
 * @package Who_Hit_The_Page_Hit_Counter
 * @subpackage Includes
 *
 * @version 1.4.11
 * @since   1.4.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes class
 *
 * @version 1.4.11
 * @since   1.4.6
 */
class WHTP_Shortcodes {

	/**
	 * Initialize shortcode hooks
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function init_hooks() {
		add_shortcode( 'whohit', array( $this, 'hit_counter_shortcode' ) );
		add_shortcode( 'whlinkback', array( $this, 'link_back' ) );
	}

	/**
	 * Hit counter shortcode.
	 *
	 * @param mixed $atts Shortcode atts.
	 * @param string $content Shortcode content.
	 * @return void
	 * @version 1.4.11
	 * @since   1.0.0
	 */
	public static function hit_counter_shortcode( $atts = null, $content = null ) {
		$atts = shortcode_atts( 
			array(
				'id'   => '',
				'page' => '',
			), 
			$atts,
			'whohit'
		);

		extract( $atts );
		
		if ( '' === $page && ! is_null( $content ) ) {
			$page = $content;
		}

		WHTP_Hits::count_page( $page );
	}

	/**
	 * Link back shortcode
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.6
	 */
	public static function link_back() {
		return sprintf(
			__( '<a href="https://conflated.co.za" rel="bookmark" title="%1$s" target="_blank">%2$s</a>' ),
			__( 'WordPress plugins and web design resources', 'whtp' ),
			__( 'With love by Conflated Solutions', 'whtp' )
		);
	}
}

/**
 * Hook the shortcodes to init.
 */
add_action(
	'init',
	function () {
		$whtpshortcodes = new WHTP_Shortcodes();
		$whtpshortcodes->init_hooks();
	}
);
