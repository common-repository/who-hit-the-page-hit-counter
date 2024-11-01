<?php
/**
 * Static functions class
 *
 * @author Lindeni Mahlalela
 * @package Who_Hit_The_Page_Hit_Counter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Static functions options
 *
 * @version 1.4.11
 * @since   1.4.11
 */
class WHTP_Functions {

	/**
	 * Instance of the class.
	 *
	 * @var WHTP_Functions
	 * @version 1.4.12
	 * @since   1.4.12
	 */
	private static $instance = null;

	private static $hits_table;
	private static $hitinfo_table;
	private static $user_agents_table;
	private static $ip_hits_table;
	private static $visiting_countries_table;
	private static $ip_to_location_table;

	/**
	 * Construct the class.
	 *
	 * @version 1.4.12
	 * @since   1.4.12
	 */
	public function __construct() {}

	/**
	 * Get instance of the class.
	 *
	 * @return  WHTP_Functions
	 * @version 1.4.12
	 * @since   1.4.12
	 */
	public static function get_instance() {
		global $wpdb;

		self::$hits_table               = "{$wpdb->prefix}whtp_hits";
		self::$hitinfo_table            = "{$wpdb->prefix}whtp_hitinfo";
		self::$user_agents_table        = "{$wpdb->prefix}whtp_user_agents";
		self::$ip_hits_table            = "{$wpdb->prefix}whtp_ip_hits";
		self::$visiting_countries_table = "{$wpdb->prefix}whtp_visiting_countries";
		self::$ip_to_location_table     = "{$wpdb->prefix}whtp_ip2location";

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function plugin_info() {
		$plugin_data = get_plugin_data( __FILE__ );

		return $plugin_data;
	}

	public static function make_backup_dir() {
		$whtp_backup_dir = WP_CONTENT_DIR . '/uploads/whtp_backups';
		if ( ! wp_mkdir_p( $whtp_backup_dir ) ) {
			if ( mkdir( $whtp_backup_dir ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Return all ip addresses
	 *
	 * @return  array
	 * @version 1.4.11
	 * @since   1.4.5
	 */
	public static function all_ips() {
		global $wpdb;

		$all_ips = array();
		$all_ips = $wpdb->get_col( "SELECT ip_address FROM {$wpdb->prefix}whtp_hitinfo" );

		return $all_ips;
	}

	public static function pagination( $number, $page, $total, $page_name = '', $links = 5, $list_class = '' ) {
		if ( $number == 'all' || $total <= $number ) {
			return '';
		}

		if ( $page_name == '' ) {
			$page_name = 'whtp-view-page-hits';
		}
		$url = admin_url( 'admin.php?page=' . $page_name );

		$last = ceil( $total / $number );

		$start = ( ( $page - $number ) > 0 ) ? $page - $number : 1;
		$end   = ( ( $page + $number ) < $last ) ? $page + $number : $last;

		$html  = '<div class="' . $list_class . '">';
		$html .= sprintf( __( '<p>Showing %1$d to %2$d of %3$d results.</p>', 'whtp' ), $number * ( $page - 1 ), ( $number * $page ) + $number, $total );

		if ( $last < $number ) {
			for ( $i = 0; $i <= $last; $i++ ) {
				$class = ( $page == $i ) ? ' active' : '';
				$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number' . $class . '" href="' . $url . '&number=' . $number . '&paging=' . $i . '">' . $i . '</a>';
			}
		} else {
			$class = ( $page == 1 ) ? ' disabled' : '';
			$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number' . $class . '" href="' . $url . '&number=' . $number . '&paging=' . ( $page - 1 ) . '"><i class="material-icons">arrow_left</i></a>';

			if ( $start > 1 ) {
				$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number" href="' . $url . '&number=' . $number . '&paging=1">1</a>';
				$html .= '<a href="#" class="disabled"><span>...</span></a>';
			}

			for ( $i = $start; $i <= $end; $i++ ) {
				$class = ( $page == $i ) ? ' active' : '';
				$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number' . $class . '" href="' . $url . '&number=' . $number . '&paging=' . $i . '">' . $i . '</a>';
			}

			if ( $end < $last ) {
				$html .= '<a href="#" class="mdl-button mdl-js-button mdl-button--icon page-number disabled"><span>...</span></a>';
				$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number" href="' . $url . '&number=' . $number . '&paging=' . $last . '">' . $last . '</a>';
			}

			$class = ( $page == $last ) ? ' disabled' : '';
			$html .= '<a class="mdl-button mdl-js-button mdl-button--icon page-number' . $class . '" href="' . $url . '&number=' . $number . '&paging=' . ( $page + 1 ) . '"><i class="material-icons">arrow_right</i></a>';
		}

		$html .= '</div>';

		return $html;
	}

	/*
	* subscribe to plugin development
	* subscription sent from current admin, forward to developer's email address
	* Developer's email address hard coded below
	*/
	public static function admin_message_sender() {
		$s_email = stripslashes( $_POST['asubscribe_email'] );
		if ( $s_email != '' ) {
			$s_email = $s_email;
		} else {
			$s_email = get_option( 'admin_email' );
		}

		// try to get email address from MPMF if it is installed
		if ( $s_email == '' ) {
			$mpmf_installed = get_option( 'mpmf_installed' );
			if ( $mpmf_installed ) {
				if ( get_option( 'mpmf_email_to_us' ) != '' && get_option( 'mpmf_email_to_us' ) != 'Your receiving email address' ) {
					// && $send_to_us != "Your receiving email address" && $send_to_us != ""
					$s_email = get_option( 'mpmf_email_to_us' );
				}
			} else {
				$err_msg = 'You did not give us an email address. Please enter your email address to subscribe to updates, we will send update notices to this email address.';
			}
		}
		$headers  = '';
		$headers .= 'From: ' . $s_email . "\r\n";
		$headers .= 'Reply-To: ' . $s_email . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();

		$message = 'Subscribe me to `Who Hit The Page - Hit Counter` updates my email address is ' . $s_email;
		if ( $s_email == '' && $err_msg != '' ) {
			echo "<div class='error-msg'>Subscription message not sent $err_msg. Please enter email address and retry.</div>";
		} else {
			if ( who_hit_send_email( 'himself@lindeni.co.za', 'Who Hit The Page - Hit Counter', $message, $headers ) ) {
				echo "<div class='success-msg'>Your subscription has been submitted.</div>";
			} else {
				echo "<div class='error-msg'>Subscription message not sent. Please retry.</div>";
			}
		}
	}
	// functions
	/*
	* Email function to send an email
	*/
	public static function who_hit_send_email( $user, $subject, $message, $headers ) {
		if ( mail( $user, $subject, $message, $headers ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function signup_form() {
		echo '<form action="" method="post" id="signup">
			<input type="hidden" name="whtpsubscr" value="y" />
			<label for="asubscribe_email">Enter your email address to subscribe to updates</label>
			<input type="email" placeholder="e.g. ' . get_option( 'admin_email' ) . '" name="asubscribe_email" value="" class="90" /><br />
			<input type="submit" value="Subscribe to updates" class="button button-primary button-hero" />
		</form>';
	}

	public static function deny_wordpress_host_ip() {

		$local   = $_SERVER['HTTP_HOST'];    // this host's name
		$siteurl = get_option( 'siteurl' );    // WordPress site url
		$ref     = $_SERVER['HTTP_REFERER']; // referrer host name
		$rem     = WHTP_Geolocation::get_ip_address();  // visitor's ip address

		if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
			$local_addr = $_SERVER['SERVER_ADDR'];  // this host's ip address
		}

		return $deny;
	}

	/*
	* $out = fopen('php://output','w'); to send output to the browser_id
	* Functions to import and export csv files
	*/
	public static function write_csv( $file_name, array $list, $delimeter = ',', $enclosure = '"' ) {
		$handle = fopen( $file_name, 'w' );
		foreach ( $list as $fields ) {
			if ( fputcsv( $handle, $fields ) ) {
				$done = true; // flag successful write
			} else {
				$done = false; // flag failed write
			}
		}
		/*
		fputcsv( $out, $list );*/
		fclose( $handle );
		return $done; // return success or failed as true/false
	}


	/*
	* export the hits table to a CSV file
	* uses the function write_csv
	* returns the file's url and file name as an array
	*/
	public static function export_hits( $backup_date ) {
		global $wpdb;

		$filename_url = WHTP_BACKUP_DIR;
		$filename     = $filename_url . '/' . $backup_date . '/whtp-hits.csv';
		$hits         = $wpdb->get_results( "SELECT * FROM `$hits_table`" );

		$fields = array(); // csv rows / whole document
		if ( count( $hits ) ) {
			foreach ( $hits as $hit ) {
				$csv_row  = array(
					$hit->page,
					$hit->count_hits,
				);   // new row
				$fields[] = $csv_row; // append row to others
			}
			// whtp_write_csv( $filename, $fields);
			if ( whtp_write_csv( $filename, $fields ) ) {
				$export_url = WP_CONTENT_URL . '/uploads/whtp_backups/' . $backup_date . '/whtp-hits.csv';
				echo '<p>Page "Hits" backup successful.</p>';
			}
		}
		return $recent_backup = array(
			'link'     => $export_url,
			'filename' => 'whtp-hits',
		);

	}

	/*
	* export the hitinfo table to a CSV file
	* uses the function write_csv
	* returns the file's url and file name as an array
	*/
	public static function export_hitinfo( $backup_date ) {
		global $wpdb;

		$filename_url = WHTP_BACKUP_DIR;
		$filename     = $filename_url . '/' . $backup_date . '/whtp-hitinfo.csv';

		$hitsinfo = $wpdb->get_results( "SELECT * FROM `$hitinfo_table`" );

		$fields = array(); // csv rows / whole document
		if ( count( $hitsinfo ) > 0 ) {
			foreach ( $hitsinfo as $hitinfo ) {
				$csv_row  = array(
					$hitinfo->ip_address,
					$hitinfo->ip_total_visits,
					$hitinfo->user_agent,
					$hitinfo->datetime_first_visit,
					$hitinfo->datetime_last_visit,
				);   // new row
				$fields[] = $csv_row;    // new row;
			}
			if ( whtp_write_csv( $filename, $fields ) ) {
				$export_url = WP_CONTENT_URL . '/uploads/whtp_backups/' . $backup_date . '/whtp-hitinfo.csv';
				echo '<p>Hitinfo backup successful.</p>';
			}
		}
		return $recent_backup = array(
			'link'     => $export_url,
			'filename' => 'whtp-hitinfo',
		);
	}

	/*
	* export the user_agents table to a CSV file
	* uses the function write_csv
	* returns the file's url and file name as an array
	*/
	public static function export_user_agents( $backup_date ) {
		global $wpdb;

		$filename_url = WHTP_BACKUP_DIR;
		$filename     = $filename_url . '/' . $backup_date . '/whtp-user-agents.csv';

		$user_agents = $wpdb->get_results( "SELECT * FROM `$user_agents_table`" );
		$fields      = array();

		if ( count( $user_agents ) > 0 ) {
			foreach ( $user_agents as $user_agent ) {
				$csv_row  = array(
					$user_agent->agent_id,
					$user_agent->agent_name,
					$user_agent->agent_details,
				);
				$fields[] = $csv_row;
			}
			// write to csv
			// whtp_write_csv( $filename, $fields);
			if ( whtp_write_csv( $filename, $fields ) ) {
				$export_url = WP_CONTENT_URL . '/uploads/whtp_backups/' . $backup_date . '/whtp-user-agents.csv';
				echo '<p>User Agents backup successful.</p>';
			}
		}
		return $recent_backup = array(
			'link'     => $export_url,
			'filename' => 'whtp-user-agents',
		);
	}

	/*
	* export the ip_hits table to a CSV file
	* uses the function write_csv
	* returns the file's url and file name as an array
	*/
	public static function export_ip_hits( $backup_date ) {
		global $wpdb;

		$filename_url = WHTP_BACKUP_DIR;
		$filename     = $filename_url . '/' . $backup_date . '/whtp-ip-hits.csv';

		$ip_hits = $wpdb->get_results( "SELECT * FROM `$ip_hits_table`" );
		$fields  = array();
		if ( count( $ip_hits ) > 0 ) {
			foreach ( $ip_hits as $ip_hit ) {
				$csv_row  = array(
					$ip_hit->ip_id,
					$ip_hit->page_id,
					$ip_hit->datetime_first_visit,
					$ip_hit->datetime_last_visit,
					$ip_hit->browser_id,
				);
				$fields[] = $csv_row;
			}
			if ( whtp_write_csv( $filename, $fields, ',', '' ) ) {
				$export_url = WP_CONTENT_URL . '/uploads/whtp_backups/' . $backup_date . '/whtp-ip-hits.csv';
				echo '<p>IP Hits Table backup successful.</p>';
			}
		}
		return $recent_backup = array(
			'link'     => $export_url,
			'filename' => 'whtp-ip-hits',
		);
	}

	/**
	 * Capability options
	 *
	 * @return  array
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function caps_options() {
		$caps = self::caps();

		$options = array();
		foreach ( $caps as $_cap ) {
			$options[ $_cap ] = ucwords( implode( ' ', explode( '_', $_cap ) ) );
		}

		return $options;
	}

	/**
	 * Get capabilities
	 *
	 * @return  array
	 * @version 1.4.11
	 * @since   1.4.11
	 */
	public static function caps() {
		return apply_filters(
			'whtp_capabilities',
			array(
				'remove_user',
				'promote_user',
				'add_users',
				'edit_user',
				'edit_users',
				'delete_post',
				'delete_page',
				'edit_post',
				'edit_page',
				'read_post',
				'read_page',
				'publish_post',
				'edit_post_meta',
				'delete_post_meta',
				'add_post_meta',
				'edit_comment_meta',
				'delete_comment_meta',
				'add_comment_meta',
				'edit_term_meta',
				'delete_term_meta',
				'add_term_meta',
				'edit_user_meta',
				'delete_user_meta',
				'add_user_meta',
				'edit_comment',
				'unfiltered_upload',
				'edit_css',
				'unfiltered_html',
				'edit_files',
				'edit_plugins',
				'edit_themes',
				'update_plugins',
				'delete_plugins',
				'install_plugins',
				'upload_plugins',
				'update_themes',
				'delete_themes',
				'install_themes',
				'upload_themes',
				'update_core',
				'install_languages',
				'update_languages',
				'activate_plugins',
				'deactivate_plugins',
				'activate_plugin',
				'deactivate_plugin',
				'resume_plugin',
				'resume_theme',
				'delete_user',
				'delete_users',
				'create_users',
				'manage_links',
				'customize',
				'edit_term',
				'delete_term',
				'assign_term',
				'manage_post_tags',
				'edit_categories',
				'edit_post_tags',
				'delete_categories',
				'delete_post_tags',
				'assign_categories',
				'assign_post_tags',
				'create_sites',
				'delete_sites',
				'manage_network',
				'manage_sites',
				'manage_network_users',
				'manage_network_plugins',
				'manage_network_themes',
				'manage_network_options',
				'upgrade_network',
				'setup_network',
				'update_php',
				'export_others_personal_data',
				'erase_others_personal_data',
				'manage_privacy_options',
			)
		);
	}

	/**
	 * Default capabilities setting.
	 *
	 * @return  array
	 * @version 1.4.10
	 * @since   1.4.10
	 */
	public static function default_caps() {
		return apply_filters(
			'whtp_default_caps',
			array(
				'main_menu'  => 'manage_options',
				'page_hits'  => 'manage_options',
				'ip_hits'    => 'manage_options',
				'denied_ips' => 'manage_options',
				'settings'   => 'manage_options',
			)
		);
	}

	/**
	 * Custom select function.
	 *
	 * TODO: Replace with MDL Select
	 *
	 * @param   array $args The properties of the select.
	 * @return  string
	 * @version 1.4.10
	 * @since   1.4.10
	 */
	public static function select( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'text',
				'id'          => 'select',
				'name'        => '',
				'label'       => 'Check',
				'checked'     => false,
				'label'       => 'Choose',
				'class'       => '',
				'empty_value' => null,
				'value'       => 1,
				'options'     => array(),
				'description' => '',
			)
		);

		$options = '';
		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $value => $label ) {
				$selected = $args['selected'] == $value ? 'selected' : '';
				$options .= '<option class="mdl-menu__item" value="' . $value . '" ' . $selected . '>' . $label . '</option>' . PHP_EOL;
			}
		}

		$select  = '<p><label for="' . $args['id'] . '">' . $args['label'] . '</label><br />';
		$select .= '<select name="' . $args['name'] . '" id="' . $args['id'] . '">';
		$select .= $options;
		$select .= '</select>';
		$select .= '' !== $args['description'] ? '<small>' . wp_kses_post( $args['description'] ) . '</small>' : '';
		$select .= '</p>';

		return $select;
	}

	/**
	 * Log
	 *
	 * @param   string $message Message to log.
	 * @return  void
	 * @version 1.4.10
	 * @since   1.4.9
	 */
	public static function log( $message ) {
		if ( defined( 'WHTP_LOG' ) && WHTP_LOG ) {
			$date = current_time( 'mysql' );
			if ( ! is_string( $message ) ) {
				$message = print_r( $message, true );
			}
			$message = '[' . $date . '] - ' . $message . "\n";
			file_put_contents( trailingslashit( WP_CONTENT_DIR ) . 'whtp-debug.log', $message, FILE_APPEND );
		}
	}
}
