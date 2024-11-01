<?php
/**
 * Holds the Deactivator class
 *
 * @author Lindeni Mahlalela
 * @package Who_hit_The_Page_Hit_Counter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivator class.
 *
 * @version 1.4.11
 * @since   1.4.5
 */
class WHTP_Deactivator {

	private static $hits_table;
	private static $hitinfo_table;
	private static $user_agents_table;
	private static $ip_hits_table;
	private static $visiting_countries_table;
	private static $ip_to_location_table;

	/**
	 * Contruct the uninstaller.
	 *
	 * @version 1.4.11
	 * @since   1.4.5
	 */
	public function __construct() {
		global $wpdb;
		self::$hits_table               = $wpdb->prefix . 'whtp_hits';
		self::$hitinfo_table            = $wpdb->prefix . 'whtp_hitinfo';
		self::$user_agents_table        = $wpdb->prefix . 'whtp_user_agents';
		self::$ip_hits_table            = $wpdb->prefix . 'whtp_ip_hits';
		self::$visiting_countries_table = $wpdb->prefix . 'whtp_visiting_countries';
		self::$ip_to_location_table     = $wpdb->prefix . 'whtp_ip2location';

		if ( get_option( 'whtp_data_action' ) == 'delete-all' ) {
			self::delete_all();
			if ( ! update_option( 'whtp_installed', 'no' ) ) {
				add_option( 'whtp_installed', 'no' );
			}
		} elseif ( get_option( 'whtp_data_action' ) == 'clear-tables' ) {
			self::empty_all();
		}
	}

	/**
	 * Delete all data / Empty all tables leave table structures.
	 *
	 * @return  void
	 * @version 1.4.11
	 * @since   1.4.5
	 */
	public static function empty_all() {
		global $wpdb;
		$wpdb->query( 'TRUNCATE `' . self::$hits_table . '`' );
		$wpdb->query( 'TRUNCATE `' . self::$hitinfo_table . '`' );
		$wpdb->query( 'TRUNCATE `' . self::$user_agents_table . '`' );
		$wpdb->query( 'TRUNCATE `' . self::$ip_hits_table . '`' );
		$wpdb->query( 'TRUNCATE `' . self::$visiting_countries_table . '`' );
		$wpdb->query( 'TRUNCATE `' . self::$ip_to_location_table . '`' );
	}

	/**
	 * Delete all tables and their data.
	 *
	 * @return  void
	 * @version 1.4.11
	 * @since   1.4.15
	 */
	public static function delete_all() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE `' . self::$hits_table . '`' );
		$wpdb->query( 'DROP TABLE `' . self::$hitinfo_table . '`' );
		$wpdb->query( 'DROP TABLE `' . self::$user_agents_table . '`' );
		$wpdb->query( 'DROP TABLE `' . self::$ip_hits_table . '`' );
		$wpdb->query( 'DROP TABLE `' . self::$visiting_countries_table . '`' );
		$wpdb->query( 'DROP TABLE `' . self::$ip_to_location_table . '`' );
	}
}
