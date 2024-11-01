<?php
/**
 * Plugin installer
 *
 * @author Lindeni Mahlalela
 * @package Who_Hit_The_Page_Hit_Counter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin installer class.
 *
 * @version 1.4.10
 * @since   1.4
 */
class WHTP_Installer {

	/**
	 * Hits table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $hits_table;

	/**
	 * Hit info table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $hitinfo_table;

	/**
	 * User agents table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $user_agents_table;

	/**
	 * IP hits table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $ip_hits_table;

	/**
	 * Visiting countries table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $visiting_countries_table;

	/**
	 * Ip to location table name
	 *
	 * @var string
	 * @version 1.4.10
	 * @since   1.4
	 */
	private static $ip_to_location_table;

	/**
	 * Construct the class
	 *
	 * @version 1.4.10
	 * @since   1.4
	 */
	public function __construct() {
		global $wpdb;

		/**
		 * Include the config file.
		 */
		include_once 'config.php';

		/*
		* define backup directory
		*/
		if ( ! defined( 'WHTP_BACKUP_DIR' ) ) {
			WHTP_Functions::make_backup_dir();
		}

		if ( ! defined( 'WHTP_VERSION' ) ) {
			define( 'WHTP_VERSION', '1.4.6' );
		}

		self::upgrade_db();

		if ( ! self::is_installed() ) {
			self::create();
		}

		if ( ! get_option( 'whtp_version' ) ) {
			update_option( 'whtp_version', WHTP_VERSION );
		}

		if ( get_option( 'whtp_count_renamed', 'no' ) !== 'yes' && version_compare( get_option( 'whtp_version' ), '1.4.6', '<' ) ) {
			self::update_count();
		}
	}

	/**
	 * Check if plugin is installed
	 *
	 * @return  boolean
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function is_installed() {
		if ( get_option( 'whtp_installed' ) === 'yes' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create database tables
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4
	 */
	public static function create() {
		self::create_hits_table();
		self::create_hitinfo_table();
		self::create_visiting_countries();
		self::create_user_agents();
		self::create_ip_2_location_country();
		self::create_ip_hits_table();

		update_option( 'whtp_installed', 'yes' );
	}

	/**
	 * Upgrade database.
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4
	 */
	public static function upgrade_db() {
		self::check_rename_tables();
		self::update_old_user_agents();
	}

	/**
	 * Update counts.
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4
	 */
	public static function update_count() {
		if ( ! WHTP_Hits::count_exists() || ! WHTP_Visiting_Countries::count_exists() ) {
			return;
		}

		global $wpdb;
		if ( ! WHTP_Hits::count_exists() ) {
			$updated_hits = $wpdb->query( "UPDATE `{$wpdb->prefix}whtp_hits` SET `count_hits`=`count`" );
			if ( $updated_hits ) {
				$updated_hits = $wpdb->query( "ALTER TABLE `{$wpdb->prefix}whtp_hits` DROP COLUMN count" );
			}
		}

		if ( ! WHTP_Visiting_Countries::count_exists() ) {
			$updated_countries = $wpdb->query( "UPDATE `{$wpdb->prefix}whtp_visiting_countries` SET `count_hits`=`count`" );
			if ( $updated_countries ) {
				$updated_countries = $wpdb->query( "ALTER TABLE `{$wpdb->prefix}whtp_visiting_countries` DROP COLUMN count" );
			}
		}
	}

	/**
	 * Check and rename tables.
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4
	 */
	public static function check_rename_tables() {
		if ( self::table_exists( "hits" ) && ! self::table_exists( WHTP_HITS_TABLE ) ) {
			self::rename_table( "hits", WHTP_HITS_TABLE );
		}

		if ( self::table_exists( "hitinfo" ) && ! self::table_exists( WHTP_HITINFO_TABLE ) ) {
			self::rename_table( "hitinfo", WHTP_HITINFO_TABLE );
		}

		if ( self::table_exists( "whtp_hits" ) && ! self::table_exists( WHTP_HITS_TABLE ) ) {
			self::rename_table( "whtp_hits", WHTP_HITS_TABLE );
		}

		if ( self::table_exists( "whtp_hitinfo" ) && ! self::table_exists( WHTP_HITINFO_TABLE ) ) {
			self::rename_table( "whtp_hitinfo", WHTP_HITINFO_TABLE );
		}

		if ( self::table_exists( "whtp_user_agents" ) && ! self::table_exists( WHTP_USER_AGENTS_TABLE ) ) {
			self::rename_table( "whtp_user_agents", WHTP_USER_AGENTS_TABLE );
		}

		if ( self::table_exists( "whtp_ip_hits" ) && ! self::table_exists( WHTP_IP_HITS_TABLE ) ) {
			self::rename_table( "whtp_ip_hits", WHTP_IP_HITS_TABLE );
		}

		if ( self::table_exists( "whtp_visiting_countries" ) && ! self::table_exists( WHTP_VISITING_COUNTRIES_TABLE ) ) {
			self::rename_table( "whtp_visiting_countries", WHTP_VISITING_COUNTRIES_TABLE );
		}

		if ( self::table_exists( "whtp_ip2location" ) && ! self::table_exists( WHTP_IP2_LOCATION_TABLE ) ) {
			self::rename_table( "whtp_ip2location", WHTP_IP2_LOCATION_TABLE );
		}
	}

	/**
	 * Rename an existing table
	 *
	 * @param   string $old_table_name Name of table to rename.
	 * @param   string $new_table_name The new name of the table.
	 * @return  bool
	 * @version 1.4.10
	 * @since   1.4
	 */
	public static function rename_table( $old_table_name, $new_table_name ) {
		global $wpdb;
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		if ( $wpdb->query( 'RENAME TABLE `' . $old_table_name . '` TO `' . $new_table_name . '`;' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update old user agents into browser names
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function update_old_user_agents() {
		set_time_limit( 0 );
		global $wpdb;

		$user_agents = $wpdb->get_results( 'SELECT ip_address, user_agent FROM ' . WHTP_HITINFO_TABLE );
		if ( count( $user_agents ) > 0 ) {
			foreach ( $user_agents as $uagent ) {
				$ua      = WHTP_Browser::browser_info();
				$browser = $ua['name'];
				$ip      = $uagent->ip_address;
				if ( $uagent->user_agent != $browser ) {
					$update_browser = $wpdb->update(
						WHTP_HITINFO_TABLE,
						array( 'user_agent' => $browser ),
						array( 'ip_address' => $ip ),
						array( '%s', '%s' )
					);
				}
			}
		}
	}

	/**
	 * Create hits table
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.10
	 */
	public static function create_hits_table() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_HITS_TABLE . "` (
			`page_id` int(10) NOT NULL AUTO_INCREMENT,
			`page` varchar(100) NOT NULL,
			`count_hits` int(15) DEFAULT 0,
			PRIMARY KEY (`page_id`)
			) $charset_collate"
		);
	}

	/**
	 * Create hitinfo table
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function create_hitinfo_table() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_HITINFO_TABLE . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`ip_address` varchar(30) DEFAULT NULL,
			`ip_status` varchar(10) NOT NULL DEFAULT 'active',
			`ip_total_visits` int(15) DEFAULT '0',
			`user_agent` varchar(50) DEFAULT NULL,
			`datetime_first_visit` varchar(25) DEFAULT NULL,
			`datetime_last_visit` varchar(25) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) $charset_collate"
		);
	}

	/**
	 * Create visiting countries
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function create_visiting_countries() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_VISITING_COUNTRIES_TABLE . "` (
			`country_code` char(2) NOT NULL,
			`country_name` varchar(125) DEFAULT NULL,
			`count_hits` int(11) NOT NULL,
			UNIQUE KEY `country_code` (`country_code`)
			) $charset_collate"
		);
	}


	/**
	 * Create Ip hits table
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function create_ip_hits_table() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_IP_HITS_TABLE . "` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`ip_id` int(11) NOT NULL,
			`page_id` int(10) NOT NULL,
			`datetime_first_visit` datetime NOT NULL,
			`datetime_last_visit` datetime NOT NULL,
			`browser_id` int(11) NOT NULL,
			PRIMARY KEY (`id`)
			) $charset_collate"
		);
	}

	/**
	 * Create user agents table
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function create_user_agents() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_USER_AGENTS_TABLE . "` (
			`agent_id` int(11) NOT NULL AUTO_INCREMENT,
			`agent_name` varchar(20) NOT NULL,
			`agent_details` text NOT NULL,
			PRIMARY KEY (`agent_id`),
			UNIQUE KEY `agent_name` (`agent_name`)
			) $charset_collate"
		);
	}

	/**
	 * Create ip to location table.
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function create_ip_2_location_country() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $charset_collate;
		dbDelta(
			"CREATE TABLE IF NOT EXISTS `" . WHTP_IP2_LOCATION_TABLE . "`(
			`ip_from` varchar(15) DEFAULT NULL,
			`ip_to` varchar(15) DEFAULT NULL,
			`decimal_ip_from` int(11) NOT NULL,
			`decimal_ip_to` int(11) NOT NULL,
			`country_code` char(2) DEFAULT NULL,
			`country_name` varchar(64) DEFAULT NULL,
			KEY `idx_ip_from` (`ip_from`),
			KEY `idx_ip_to` (`ip_to`),
			KEY `idx_ip_from_to` (`ip_from`,`ip_to`)
			) $charset_collate"
		);
	}

	/**
	 * Functions to export the old `hits` and `hitinfo` tables to the new `whtp_hits` and `whtp_hitinfo` tables.
	 * First run the function `whtp_table_exists()` to check if the table exists, then.
	 * Start the export if both the source and destination tables exists.
	 * If the destinatio table doesn't exist, create it and run the export again.
	 *
	 * Check if a table exists in the database.
	 *
	 * @param string $tablename The name of the table.
	 */
	public static function table_exists( $tablename ) {
		global $wpdb;
		$table = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $tablename ) );

		if ( ! empty( $table ) && is_array( $table ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Export hits data to whtp_hits
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function export_hits() {
		global $wpdb;

		$wpdb->hide_errors();

		$hits = $wpdb->get_results( 'SELECT * FROM `hits`, ARRAY_A' );
		if ( count( $hits ) > 0 ) {
			$message  = '';
			$exported = false;
			foreach ( $hits as $hit ) {
				$insert = $wpdb->insert(
					WHTP_HITS_TABLE,
					array(
						'page'       => $hit['page'],
						'count_hits' => $hit['count_hits'],
					),
					array( '%s', '%d' )
				);
				if ( ! $insert ) {
					$exported = false;
				} else {
					$exported = true;
				}
			}
		}
		if ( $exported == true ) {
			$wpdb->query( 'DROP TABLE IF EXISTS `hits`' );
		}
	}

	/**
	 * Export hitinfo data to whtp_hitinfo table
	 *
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function export_hitinfo() {
		global $wpdb;
		$wpdb->hide_errors();

		$hitsinfo = $wpdb->get_results( 'SELECT * FROM hitinfo' );

		if ( count( $hitsinfo ) > 0 ) {
			$message  = '';
			$exported = false;
			foreach ( $hitsinfo as $info ) {
				$insert = $wpdb->insert(
					WHTP_HITINFO_TABLE,
					array(
						'ip_address'           => $info->ip_address,
						'ip_status'            => 'active',
						'user_agent'           => $info->user_agent,
						'datetime_first_visit' => $info->datetime,
						'datetime_last_visit'  => $info->datetime,
					),
					array( '%s', '%s', '%s', '%s', '%s' )
				);
				if ( ! $insert ) {
					$exported = false;
				} else {
					$exported = true;
				}
			}
		}

		if ( $exported == true ) {
			$wpdb->query( 'DROP TABLE IF EXISTS `hitinfo`' );
		}
	}
}
