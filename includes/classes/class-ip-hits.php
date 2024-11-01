<?php
/**
 * Holds the IP Hits Table
 *
 * @author Lindeni Mahlalela
 * @package Who_Hit_The_Page_hit_Counter
 *
 * @version 1.4.10
 * @since   1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IP hits table
 *
 * Class responsible for registering IP hits.
 *
 * @version 1.4.10
 * @since   1.4.5
 */
class WHTP_Ip_Hits {

	/**
	 * Ip hits table name
	 *
	 * @var string
	 * @version 1.4.5
	 * @since   1.4.10
	 */
	private static $ip_hits_table;

	/**
	 * Create instance of this class.
	 *
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public function __construct() {
		global $wpdb;

		self::$ip_hits_table = $wpdb->prefix . 'whtp_ip_hits';
	}

	/**
	 * Record an IP hit
	 *
	 * @param   int    $ip_id The IP d.
	 * @param   int    $page_id The page id.
	 * @param   string $date_ftime Date of first visit.
	 * @param   int    $ua_id The user agent id.
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function ip_hit( $ip_id, $page_id, $date_ftime, $ua_id ) {
		global $wpdb, $ip_hits_table;

		$wpdb->insert(
			$ip_hits_table,
			array(
				'ip_id'                => $ip_id,
				'page_id'              => $page_id,
				'datetime_first_visit' => $date_ftime,
				'browser_id'           => $ua_id,
			),
			array( '%d', '%d', '%s', '%d' )
		);
	}

	/**
	 * Get all the ids of the browsers used by the user.
	 *
	 * Return as an array of agent_ids.
	 *
	 * @param   int $ip_id The ID of the IP.
	 * @return  array
	 * @version 1.4.10
	 * @since   1.4.5
	 */
	public static function agent_ids_from_ip_id( $ip_id ) {
		global $wpdb, $ip_hits_table;

		$agent_ids = array();
		$ids       = $wpdb->get_col( "SELECT browser_id FROM `$ip_hits_table` WHERE ip_id = '$ip_id'" );
		if ( count( $ids ) ) {
			for ( $i = 0; $i < count( $ids ); $i ++ ) {
				if ( ! in_array( $ids[ $i ], $agent_ids ) ) {
					$agent_ids[] = $ids[ $i ];
				}
			}
		}
		return $agent_ids;
	}

	/**
	 * Get all the ids of the pages visted by the user
	 * Return as an array of page_ids
	 *
	 * @param   int $ip_id
	 * @return  void
	 * @version
	 * @since
	 */
	public static function page_ids_from_ip_id( $ip_id ) {
		global $wpdb, $ip_hits_table;

		$results = $wpdb->get_col( $wpdb->prepare( "SELECT page_id FROM `$ip_hits_table` WHERE ip_id = %d", $ip_id ) );

		if ( $results ) {
			$page_ids = array();
			for ( $i = 0; $i < count( $results ); $i ++ ) {
				if ( ! in_array( $results[ $i ], $page_ids ) ) {
					$page_ids[] = $results[ $i ];
				}
			}
			return $page_ids;
		}

		return array();
	}
}
