<?php
/**
 * Contains the IP to Location class.
 *
 * @author Lindeni Mahlalela
 * @package Who_Hit_The_Page_Hit_Counter/Includes
 */

use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IP to Location class
 *
 * @version 1.4.10
 * @since   1.4.0
 */
class WHTP_Geolocation {
	/**
	 * Table name for storing country locations
	 *
	 * @var     string
	 * @version 1.4.10
	 * @since   1.4.0
	 */
	private static $ip_to_location_table;

	/**
	 * Current user's location data
	 *
	 * @since 1.4.0
	 * @var   array
	 */
	private static $location = null;

	/**
	 * Instance of this class.
	 *
	 * @since 1.4.0
	 * @var   object
	 */
	protected static $instance = null;

	/**
	 * Geolocation integration
	 *
	 * @var  $integration The active integration object
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static $integration = null;

	/**
	 * GeoLite2 DB.
	 *
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	const GEOLITE2_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz';

	/**
	 * Initialize the class
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function __construct() {
		self::$location = self::get_location();

		global $wpdb;
		self::$ip_to_location_table = $wpdb->prefix . 'whtp_ip2location';
	} // __construct

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $plugin_file The main plugin file.
	 * @return object A single instance of this class.
	 */
	public static function get_instance( $plugin_file ) {

		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_file );
		}

		return self::$instance;

	} // get_instance

	/**
	 * Initialize the geolocation functionality.
	 *
	 * @return  void
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function init() {}

	/**
	 * Get the current visitor's country code
	 *
	 * @version 1.4.10
	 * @since 1.4.0
	 *
	 * @param string $ip The IP address to get country code for.
	 * @return string
	 */
	public static function get_country_code( $ip = ' ' ) {

		if ( '' === $ip ) {
			$location = self::get_location();
		} else {
			$location = self::locate_ip( $ip );
		}

		if ( isset( $location['country_code'] ) ) {
			return $location['country_code'];
		}

		return __( 'AA', 'whtp' );  // Dummy country code.
	} // get_country_code

	/**
	 * Get the name of the current visitor's country
	 *
	 * @param string $ip The IP address.
	 * @return string - name of the current visitor's country
	 */
	public static function get_country_name_by_ip( $ip = '' ) {
		if ( '' === $ip ) {
			$location = self::get_location();
		} else {
			$location = self::locate_ip( $ip );
		}

		if ( isset( $location['country_name'] ) ) {
			return $location['country_name'];
		}

		return __( 'Unknown Country', 'whtp' );
	} // get_country_name

	/**
	 * Get the location data of the current visitor
	 *
	 * @return array - the location data of the current visitor
	 */
	public static function get_results() {
		$ip_address = self::get_ip_address();
		return self::locate_ip( $ip_address );
	}

	/**
	 * Get location by IP address
	 *
	 * @param   string $ip_address The IP address to geolocate.
	 * @return  array
	 * @version 1.4.10
	 * @since   1.4.0
	 */
	public static function locate_ip( $ip_address ) {
		if ( is_null( $ip_address ) ) {
			return array(
				'country_code'   => __( 'AA', 'whtp' ),
				'country_name'   => __( 'Unknown Country', 'whtp' ),
				'continent_code' => __( 'AA', 'whtp' ),
				'continent_name' => __( 'Unknown Continent' . 'whtp' ),
			);
		}

		$country_code = '';
		$country_name = '';

		$api       = get_option( 'whtp_geolocation_api', 'ip-api' );
		$cache_key = 'whtp_ipinfo_' . implode( '_', explode( '.', $ip_address ) );
		if ( 'ip-api' === $api ) {
			try {
				$details = get_transient( $cache_key );
				if ( ! $details ) {
					$response = wp_remote_get( 'http://ip-api.com/json/' . $ip_address );
					if ( ! is_wp_error( $response ) && isset( $response['body'] ) ) {
						$details = json_decode( $response['body'] );
						set_transient( $cache_key, $details, WEEK_IN_SECONDS );
					}
				}

				$details = maybe_unserialize( $details );

				if ( is_array( $details ) ) {
					$country_code = $details['countryCode'];
					$country_name = $details['country'];
				} elseif ( is_object( $details ) ) {
					$country_code = $details->countryCode;
					$country_name = $details->country;
				}
			} catch ( Exception $e ) {
				$e->getMessage();
			}
		} elseif ( 'ipinfo' === $api ) {
			$ipinfo_access_token = get_option( 'whtp_ipinfo_token', '' );
			if ( '' === $ipinfo_access_token ) {
				$client = new IPinfo();
			} else {
				$client = new IPinfo( $ipinfo_access_token );
			}
			try {
				$cache_key = 'whtp_ipinfo_' . implode( '_', explode( '.', $ip_address ) );
				$details   = get_transient( $cache_key );
				if ( ! $details ) {
					$details = $client->getDetails( $ip_address );
					set_transient( $cache_key, $details, WEEK_IN_SECONDS );
				}
				$details = maybe_unserialize( $details );

				$country_code = property_exists( $details, 'country' ) ? $details->country : __( 'AA', 'whtp' );
				$country_name = self::code_to_country_name( $country_code );
			} catch ( IPinfoException $e ) {
				$e->getMessage();
			}
		}

		$country_code = '' === $country_code ? __( 'AA', 'whtp' ) : $country_code;
		$country_name = '' === $country_name ? __( 'Unknown Country', 'whtp' ) : $country_name;

		return apply_filters(
			'whtp_locate_ip',
			array(
				'country_code'   => $country_code,
				'country_name'   => $country_name,
				'continent_code' => __( 'AA', 'whtp' ),
				'continent_name' => __( 'Unknown Continent' . 'whtp' ),
			)
		);
	}

	/**
	 * Get the current user's IP Address
	 *
	 * @return string - the IP address of the current visitor
	 */
	public static function get_ip_address() {

		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			return getenv( 'HTTP_CLIENT_IP' );
		} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			return getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
			return getenv( 'HTTP_X_FORWARDED' );
		} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			return getenv( 'HTTP_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
			return getenv( 'HTTP_FORWARDED' );
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			return getenv( 'REMOTE_ADDR' );
		}

		// We don't want to get here.
		return '127.0.0.1';
	}

	/**
	 * Get the current location
	 *
	 * @return array - the current visitor's location data
	 */
	public static function get_location() {

		if ( is_null( self::$location ) ) {
			self::$location = self::get_results();
		}

		return self::$location;
	}

	/**
	 * Set the current user's location data
	 *
	 * @param  array $location The retrieved location.
	 * @return array - the current user's location details
	 */
	public static function set_location( $location ) {
		self::$location = $location;

		return self::$location;
	}

	/**
	 * Get country name.
	 *
	 * @param string $country_code The country code.
	 * @return void
	 * @version
	 * @since
	 */
	public static function code_to_country_name( $country_code ) {
		$countries = self::countries();
		if ( array_key_exists( $country_code, $countries ) ) {
			return $countries[ $country_code ];
		}

		return __( 'Unknown Country', 'whtp' );
	}

	/**
	 * List of countries
	 *
	 * @return  array
	 * @version 1.4.14.2
	 * @since   1.4.14.2
	 */
	public static function countries() {
		$countries = include WHTP_INCLUDES_DIR . 'geography/countries.php';
		return apply_filters( 'whtp_geolocation_countries', $countries );
	}

	/**
	 * List of states
	 *
	 * @return  array
	 * @version 1.4.14.2
	 * @since   1.4.14.2
	 */
	public static function states() {
		$states = include WHTP_INCLUDES_DIR . 'geography/cities.php';
		return apply_filters( 'whtp_geolocation_states', $states );
	}

	/**
	 * Check if geolocation is integrated.
	 *
	 * @return   bool
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function has_integration() {
		if ( is_null( self::$integration ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Path to our local db.
	 *
	 * @version 1.4.11
	 * @since   1.4.11
	 * @return string
	 */
	public static function get_local_database_path() {
		if ( is_null( self::$integration ) ) {
			return apply_filters(
				'whtp_geolocation_local_database_path',
				WHTP_PLUGIN_DIR_PATH . 'geodata/GeoLite2-City.mmdb'
			);
		}

		return apply_filters(
			'whtp_geolocation_local_database_path',
			WP_CONTENT_DIR . '/uploads/whtp_uploads/GeoLite2-City.mmdb'
		);
	}
}

WHTP_Geolocation::init();
