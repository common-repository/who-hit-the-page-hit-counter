<?php
/**
 * @package Who_Hit_The_Page
 * @author Lindeni Mahlalela
 *
 * Plugin Name: Who Hit The Page - Hit Counter
 * Plugin URI: https://whohit.co.za/who-hit-the-page-hit-counter
 * Description: Lets you know who visted your pages by adding an invisible page hit counter on your website, so you know how many times a page has been visited in total and how many times each user identified by IP address has visited each page. You will also know the IP addresses of your visitors and relate the IP addresses to the country of the visitor and all browsers used by that IP/user.
 * Version: 1.4.14.3
 * Author: mahlamusa
 * Author URI: http://lindeni.co.za
 * License: GPL
 * Text Domain: whtp
 * Domain Path: /languages
 *
 * Copyright © 2012 - 2020 Lindeni Mahlalela. All rights reserved.
 * <himself@lindeni.co.za>
 * <https://lindeni.co.za>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WHTP_VERSION', '1.4.14.3' );
define( 'WHTP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WHTP_INCLUDES_DIR', plugin_dir_path( __FILE__ ) . 'includes/' );
define( 'WHTP_BASE_NAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

require_once WHTP_PLUGIN_DIR_PATH . 'vendor/autoload.php';

/**
 * Load text domain.
 *
 * @return void
 * @version 1.4.10
 * @since   1.4.11
 */
function whtp_text_domain() {
	load_plugin_textdomain( 'whtp', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'whtp_text_domain' );

require_once 'includes/config.php';
require_once 'includes/classes/class-whtp-database.php';
require_once 'includes/classes/class-browser-detection.php';
require_once 'includes/classes/class-browser.php';
require_once 'includes/classes/class-hit-info.php';
require_once 'includes/classes/class-hits.php';
require_once 'includes/classes/class-ip-hits.php';
require_once 'includes/classes/class-whtp-logger.php';
require_once 'includes/classes/class-whtp-geolocation.php';
require_once 'includes/classes/class-shortcodes.php';
require_once 'includes/classes/class-visiting-countries.php';
require_once 'includes/functions.php';
require_once 'includes/class-whtp-admin.php';
require_once 'includes/classes/class-whtp-admin-notices.php';

// New integrations.
require_once 'integrations/abstract-class-whtp-geolocation-integration.php';

register_activation_hook( __FILE__, 'whtp_installer' );
register_deactivation_hook( __FILE__, 'whtp_remove' );

/**
 * $plugin = WHTP_Functions::plugin_info();
 * define( "WHTP_VERSION", $plugin['Version'] );
 */

/**
 * Installer
 *
 * @return void
 * @version 1.4.11
 * @since   1.4.11
 */
function whtp_installer() {
	require_once 'includes/config.php';
	require_once 'includes/installer.php';
	new WHTP_Installer();
}

/**
 * Deactivate
 *
 * @return void
 * @version 1.4.11
 * @since   1.4.11
 */
function whtp_remove() {
	require_once 'includes/config.php';
	require_once 'includes/uninstaller.php';
	new WHTP_Deactivator();
}

/**
 * Initialize plugin once plugins are loaded.
 */
add_action( 'plugins_loaded', 'whtp' );

/**
 * Create and return an instance of admin.
 *
 * @version 1.4.11
 * @since   1.4.11
 */
function whtp() {
	global $whtpa;
	$whtpa = WHTP_Admin::get_instance();
	return $whtpa;
}
