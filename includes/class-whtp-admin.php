<?php
/**
 * Who Hit The Page Admin Class
 *
 * @version 1.4.12
 * @since   1.4.12
 * @author Lindeni Mahlalela <>
 * @package WHTP/Includes
 */

/**
 * Admin class
 *
 * @version 1.4.11
 * @since   1.4.11
 */
class WHTP_Admin {

	/**
	 * Instance of the plugin's admin
	 *
	 * @var WHTP_Admin
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	protected static $instance = null;

	/**
	 * Holds instance of admin notices
	 *
	 * @var object
	 * @version 1.4.14.2
	 * @since   1.4.14.2
	 */
	protected $admin_notices;

	/**
	 * Create a new instance of the class.
	 *
	 * @return  WHTP_Admin
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct
	 *
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function __construct() {
		$this->admin_notices = new WHTP_Admin_Notices();
		$this->add_notices();
		$this->hooks();
	}

	/**
	 * Hooks
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'suggest_privacy_content' ), 20 );
		add_filter( 'plugin_action_links_' . WHTP_BASE_NAME, array( $this, 'add_action_links' ) );

		if ( self::is_whtp_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		$this->admin_notices->init_hooks();
	}

	/**
	 * Add admin notices.
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function add_notices() {
		if ( WHTP_Hits::count_exists() && WHTP_Visiting_Countries::count_exists() ) :
			$message = sprintf(
				__( 'We notice that you have updated the plugin! We need to update the database to make sure the plugin works as it should. <a href="%s" class="button">Click here to update database</a>', 'whtp' ),
				admin_url( 'admin.php?page=whtp-settings&action=update_whtp_database&whtp_nonce=' . wp_create_nonce( 'whtp_update_db' ) )
			);
			$this->admin_notices->add( 'warning', $message, true );
		endif;

		if ( ! get_option( 'whtp_geolocation_api', false ) ) :
			$message = sprintf(
				__( 'Since version %1$s, you have to choose a Geolocation service that must be used by %2$s plugin otherwise the information gathered will be limited. You may choose a Geolocation service under the Geolocation Integration section in the %3$s', 'whtp' ),
				'1.4.14.2',
				'Who Hit The Page Hit Counter',
				sprintf( '<a href="%s">%s</a><br />', admin_url( 'admin.php?page=whtp-settings' ), __( 'Settings Page', 'whtp' ) )
			);
			$this->admin_notices->add( 'info', $message, true );
		endif;

		if ( ! get_option( 'whtp_ipinfo_token', false ) && 'ipinfo' === get_option( 'whtp_geolocation_api' ) ) {
			$message = sprintf(
				__( 'It seems that you have selected IpInfo as a Geolocation Service but you have not provided an Access Token. Please signup for a free account at %2$s and get an Access Token, then save the access token in the %3$s under Geolocation Integration', 'whtp' ),
				'IPInfo',
				sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://ipinfo.io/', 'IpInfo' ),
				sprintf( '<a href="%s">%s</a><br />', admin_url( 'admin.php?page=whtp-settings' ), __( 'Settings Page', 'whtp' ) )
			);
			$this->admin_notices->add( 'info', $message );
		}
	}

	/**
	 * Admin menu
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function admin_menu() {
		$menu_capability = apply_filters(
			'whtp_menu_permissions',
			get_option( 'whtp_menu_permissions', 'manage_options' )
		);

		add_menu_page(
			__( 'Who Hit The Page', 'whtp' ),
			__( 'Who Hit The Page', 'whtp' ),
			$menu_capability,
			'whtp-admin-menu',
			array( $this, 'whtp_object_page_callback' ),
			WHTP_PLUGIN_URL . 'assets/images/whtp-icon.png'
		);
		do_action( 'whtp_admin_menu_after_dashboard' );
		add_submenu_page(
			'whtp-admin-menu',
			__( 'View Page Hits', 'whtp' ),
			__( 'View Page Hits', 'whtp' ),
			$menu_capability,
			'whtp-view-page-hits',
			array( $this, 'whtp_view_page_hits' )
		);

		add_submenu_page(
			'whtp-admin-menu',
			__( 'View IP Hits', 'whtp' ),
			__( 'View IP Hits', 'whtp' ),
			$menu_capability,
			'whtp-view-ip-hits',
			array( $this, 'whtp_view_ip_hits' )
		);
		add_submenu_page(
			'whtp-admin-menu',
			__( 'Visitor Stats', 'whtp' ),
			__( 'Visitor Stats', 'whtp' ),
			$menu_capability,
			'whtp-visitor-stats',
			array( $this, 'whtp_visitors_stats_callback' )
		);
		add_submenu_page(
			'whtp-admin-menu',
			__( 'Denied IPs', 'whtp' ),
			__( 'Denied IPs', 'whtp' ),
			$menu_capability,
			'whtp-denied-ips',
			array( $this, 'whtp_denied_submenu_callback' )
		);
		/**
		 * add_submenu_page(
			'whtp-admin-menu',
			__('Export / Import', 'whtp'),
			__('Export / Import', 'whtp'),
			'administrator',
			'whtp-import-export',
			array( $this, 'whtp_export_import_submenu_callback' )
		);
		*/
		add_submenu_page(
			'whtp-admin-menu',
			__( 'Settings', 'whtp' ),
			__( 'Settings', 'whtp' ),
			$menu_capability,
			'whtp-settings',
			array( $this, 'whtp_settings_submenu_callback' )
		);
		do_action( 'whtp_admin_menu_after_settings' );

		add_submenu_page(
			'whtp-admin-menu',
			__( 'Help', 'whtp' ),
			__( 'Help', 'whtp' ),
			$menu_capability,
			'whtp-help',
			array( $this, 'whtp_help_submenu_callback' )
		);
		do_action( 'whtp_admin_menu_after_help' );

		add_submenu_page(
			'whtp-admin-menu',
			__( 'Force Update', 'whtp' ),
			'',
			'administrator',
			'whtp-force-update',
			array( $this, 'whtp_force_update' )
		);

		do_action( 'whtp_admin_menu_after' );
	}

	/**
	 * Submenu callback functions
	 */
	public function whtp_object_page_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/view/stats-summary.php';
	}

	/**
	 * Sow denied IPs
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_denied_submenu_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/view/denied-ips.php';
	}

	/**
	 * Show visitor stats
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_visitors_stats_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/view/visitor-info.php';
	}

	/**
	 * View IPs
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_view_ip_hits() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/view-ip-hits.php';
	}

	/**
	 * View page hits.
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_view_page_hits() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/view-page-hits.php';
	}

	/**
	 * Import exports page
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_export_import_submenu_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/export-import.php';
	}

	/**
	 * Settings page
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_settings_submenu_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/settings.php';
	}

	/**
	 * View help
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_help_submenu_callback() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/help.php';
	}

	/**
	 * Force update
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function whtp_force_update() {
		require_once WHTP_PLUGIN_DIR_PATH . 'partials/update/force-update.php';
	}

	/**
	 * Is page admin
	 *
	 * @param string $page The page to check.
	 * @return boolean
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function is_whtp_admin( $page = '' ) {
		if ( '' === $page ) {
			$page = ! empty( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : '';
		}

		if ( empty( $page ) ) {
			return false;
		}

		$whtp_pages = array( 'whtp-admin-menu', 'whtp-view-page-hits', 'whtp-visitor-stats', 'whtp-view-ip-hits', 'whtp-denied-ips', 'whtp-denied-ips', 'whtp-import-export', 'whtp-settings', 'whtp-help', 'whtp-widget-settings' );

		return in_array( $page, $whtp_pages, true );
	}

	/**
	 * Plugin action links
	 *
	 * @param array $links List of plugin action links.
	 * @return array
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function add_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=whtp-settings' ) . '">' . __( 'Settings', 'whtp' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'admin.php?page=whtp-help' ) . '">' . __( 'Help', 'whtp' ) . '</a>';
		$links[] = '<a href="http://whohit.co.za/who-hit-the-page-hit-counter" target="_blank">' . __( 'Documentation', 'whtp' ) . '</a>';
		return $links;
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function enqueue_styles() {
		wp_register_style(
			'mdl-admin-css',
			'https://code.getmdl.io/1.3.0/material.indigo-pink.min.css',
			array(),
			'all'
		);
		wp_register_style(
			'mdl-admin-icons',
			'https://fonts.googleapis.com/icon?family=Material+Icons',
			array(),
			'all'
		);
		wp_register_style(
			'select2',
			'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css',
			array(),
			'all'
		);
		wp_register_style(
			'whtp-admin-css',
			WHTP_PLUGIN_URL . 'assets/css/whtp-admin.min.css',
			array(),
			'all'
		);

		wp_enqueue_style( 'mdl-admin-css' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_style( 'mdl-admin-icons' );
		wp_enqueue_style( 'whtp-admin-css' );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public function enqueue_scripts() {
		wp_register_script(
			'mdl-js',
			'https://code.getmdl.io/1.3.0/material.min.js',
			null,
			true
		);
		wp_register_script(
			'whtp-admin-js',
			WHTP_PLUGIN_URL . 'assets/js/whtp-admin.js',
			array( 'jquery' ),
			true
		);
		wp_register_script(
			'select2',
			'//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js',
			array( 'jquery' ),
			true
		);

		wp_enqueue_script( 'mdl-js' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'whtp-admin-js' );
	}

	/**
	 * Admin notices
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function admin_notices() {
		if ( ! WHTP_Hits::count_exists() && ! WHTP_Visiting_Countries::count_exists() ) :
			return;
		else :
			?>
		<div class="notice notice-update update-nag is-dismissible">
			<p>
			<?php
				printf(
					__( 'We notice that you have updated the plugin! We need to update the database to make sure the plugin works as it should. <a href="%s" class="button">Click here to update database</a>', 'whtp' ),
					admin_url( 'admin.php?page=whtp-settings&action=update_whtp_database&whtp_nonce=' . wp_create_nonce( 'whtp_update_db' ) )
				);
			?>
			</p>
		</div>
			<?php
		endif;
	}

	/**
	 * Get default privacy content
	 *
	 * @return string
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function get_default_privacy_content() {
		return '<h2>' . __( 'The IP address, user agent/browser name will be recorded for internal statistical purposes.', 'whtp' ) . '</h2>' .
		'<p>' . __( 'Who Hit The Page Hit Counter collect the visitor\s IP address and the browser name or user agent used to visit the page, it also records the time and the pages visited by the speciic IP address. This data is collected for statistical purposes only and is not in any way linked to a user\'s account on this website', 'whtp' ) . '</p>';
	}

	/**
	 * Suggest privacy content
	 *
	 * @return void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function suggest_privacy_content() {
		$content = self::get_default_privacy_content();
		wp_add_privacy_policy_content( __( 'Who Hit The Page Hit Counter' ), $content );
	}
}
