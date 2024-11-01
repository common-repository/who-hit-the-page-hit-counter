<?php

use \Mahlamusa\Material\Lite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['update-backup-options'] ) && isset( $_POST['backup-action'] ) ) {
	$update_export_action = update_option( 'whtp_data_export_action', sanitize_text_field( wp_unslash( $_POST['backup-action'] ) ) );
}
$export_option = get_option( 'whtp_data_export_action' );

if ( isset( $_POST['update-uninstall-option'] ) && isset( $_POST['uninstall-action'] ) ) {
	$update_uninstall_action = update_option( 'whtp_data_action', sanitize_text_field( wp_unslash( $_POST['uninstall-action'] ) ) );
}
$option = get_option( 'whtp_data_action' );

if ( isset( $_POST['update-capabilities'] ) && isset( $_POST['update-uninstall-option'] ) || isset( $_POST['update-backup-options'] ) ) :
	$update_capabilities = update_option( 'whtp_menu_capabilities', wp_unslash( $_POST['capability'] ) );
	if ( isset( $update_uninstall_action ) && $update_uninstall_action || isset( $update_export_action ) && $update_export_action ) : ?>
		<div id="message" class="updated">
			<p>Settings updated.</p> 
		</div>
		<?php
	else :
		?>
		<div id="message" class="updated">
			<p>Failed to update settings.</p> 
		</div>
		<?php
	endif;
endif;

if ( isset( $_GET['action'] ) && $_GET['action'] == 'update_whtp_database' && wp_verify_nonce( $_GET['whtp_nonce'], 'whtp_update_db' ) ) :
	include_once WHTP_PLUGIN_DIR_PATH . 'includes/config.php';
	include_once WHTP_PLUGIN_DIR_PATH . 'includes/installer.php';
	WHTP_Installer::update_count();
	WHTP_Installer::create();
	WHTP_Visiting_Countries::update_visiting_countries();

	if ( get_option( 'whtp_hits_count_renamed' ) == 'yes' && get_option( 'whtp_countries_count_renamed' ) == 'yes' ) :
		?>
	<div id="message" class="updated">
		<p><?php esc_attr_e( 'Database updated. Enjoy!', 'whtp' ); ?></p> 
	</div>
		<?php
	endif;
endif;

// Update integration
if ( isset( $_POST['update-ipinfo-integration'] ) ) :
	$update_integration = update_option( 'whtp_ipinfo_token', wp_unslash( $_POST['whtp-ipinfo-token'] ) );
	$update_geolocation = update_option( 'whtp_geolocation_api', sanitize_text_field( wp_unslash( $_POST['whtp_geolocation_api'] ) ) );
	if ( isset( $update_integration ) || isset( $update_geolocation ) ) :
		?>
		<div id="message" class="updated">
			<p>Settings updated.</p> 
		</div>
		<?php
	else :
		?>
		<div id="message" class="updated">
			<p>Failed to update settings.</p> 
		</div>
		<?php
	endif;
endif;
?>
<div class="wrap">
	<h2 class="mdl-card__title-text">
	<?php esc_attr_e( 'Settings', 'whtp' ); ?>
	</h2>
</div>

<form action="" name="" method="post">    
	<div class="whtp-card-container mdl-grid">		
		<div class="mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet mdl-cell--12-col-phone mdl-card mdl-shadow--3dp">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'Capabilities', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text">
				Manage Menu Capabilities
			</div>
			<div class="mdl-card__supporting-text">
				<?php

					$whtp_menu_caps = WHTP_Functions::caps_options();
					$capability     = get_option( 'whtp_menu_capabilities', WHTP_Functions::default_caps() );

					echo WHTP_Functions::select(
						array(
							'id'       => 'whtp_main_menu_capability',
							'label'    => __( 'Main Menu', 'whtp' ),
							'options'  => $whtp_menu_caps,
							'name'     => 'capability[main_menu]',
							'value'    => $capability['main_menu'],
							'selected' => $capability['main_menu'],
						)
					);
					echo WHTP_Functions::select(
						array(
							'id'       => 'whtp_page_hits_capability',
							'label'    => __( 'Page Hits', 'whtp' ),
							'options'  => $whtp_menu_caps,
							'name'     => 'capability[page_hits]',
							'value'    => $capability['page_hits'],
							'selected' => $capability['page_hits'],
						)
					);
					echo WHTP_Functions::select(
						array(
							'id'       => 'whtp_ip_hits_capability',
							'label'    => __( 'Visitor Details', 'whtp' ),
							'options'  => $whtp_menu_caps,
							'name'     => 'capability[ip_hits]',
							'value'    => $capability['ip_hits'],
							'selected' => $capability['ip_hits'],
						)
					);
					echo WHTP_Functions::select(
						array(
							'id'       => 'whtp_denied_ips_capability',
							'label'    => __( 'Denied IPs', 'whtp' ),
							'options'  => $whtp_menu_caps,
							'name'     => 'capability[denied_ips]',
							'value'    => $capability['denied_ips'],
							'selected' => $capability['denied_ips'],
						)
					);
					echo WHTP_Functions::select(
						array(
							'id'       => 'whtp_settings_capability',
							'label'    => __( 'Settings', 'whtp' ),
							'options'  => $whtp_menu_caps,
							'name'     => 'capability[settings]',
							'value'    => $capability['settings'],
							'selected' => $capability['settings'],
						)
					);
					?>
			</div>
			<div class="mdl-card__actions">
				<input type="hidden" name="update-capabilities" value="update-capabilities" /> 
				<input type="submit" value="Update Options" class="whtp-link mdl-button mdl-js-button mdl-typography--text-uppercase mdl-color--teal-300" />
			</div>
		</div>
		<div class="mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet mdl-cell--12-col-phone mdl-card mdl-shadow--3dp">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'Uninstall Settings!', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text">
				What should happen when you un-install the plugin?
			</div>
			<div class="mdl-card__supporting-text">
				<p>
					<input type="radio" name="uninstall-action" value="delete-all" 
		<?php
		if ( $option == 'delete-all' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Delete all Tables</strong> and <strong>Data</strong>
				</p>
				<p>
					<input type="radio" name="uninstall-action" value="clear-tables"
		<?php
		if ( $option == 'clear-tables' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Clear data</strong>, leave table structures.
				</p>
				<p>
					<input type="radio" name="uninstall-action" value="do-nothing"
		<?php
		if ( $option == 'do-nothing' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Leave all</strong> tables and data
				</p>
			</div>
			<div class="mdl-card__actions">
				<input type="hidden" name="update-uninstall-option" value="update-uninstall-option" /> 
				<input type="submit" value="Update Options" class="whtp-link mdl-button mdl-js-button mdl-typography--text-uppercase mdl-color--teal-300" />
			</div>
		</div>

		<div class="mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet mdl-cell--12-col-phone mdl-card mdl-shadow--3dp">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'Backup Settings', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text">
				What should happen when you restore a backup?
			</div>
			<div class="mdl-card__supporting-text">
				<p>
					<input type="radio" name="backup-action" value="delete-all"
		<?php
		if ( $export_option == 'delete-all' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Override</strong> existing data 
				</p>
				<p>
					<input type="radio" name="backup-action" value="clear-tables"
		<?php
		if ( $export_option == 'clear-tables' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Update</strong> existing data, <strong>accumulate</strong> counts
				</p>
				<p>
					<input type="radio" name="backup-action" value="do-nothing"
		<?php
		if ( $export_option == 'do-nothing' ) {
			echo 'checked';
		}
		?>
  />
					<strong>Skip/Ignore</strong> Existing Records
				</p>
			</div>
			<div class="mdl-card__actions">
				<input type="hidden" name="update-backup-options" value="update-backup-options" /> 
				<input type="submit" value="Update Options" class="whtp-link mdl-button mdl-js-button mdl-typography--text-uppercase mdl-color--teal-300" />
			</div>
		</div>

		<div class="mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet mdl-cell--12-col-phone mdl-card mdl-shadow--3dp">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'Geolocation Integration', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text">
				<?php
				echo WHTP_Functions::select(
					array(
						'id'       => 'whtp_geolocation_api',
						'label'    => __( 'Geolocation API', 'whtp' ),
						'options'  => array(
							'ipinfo' => 'ipinfo.io',
							'ip-api' => 'ip-api.com',
						),
						'name'     => 'whtp_geolocation_api',
						'value'    => get_option( 'whtp_geolocation_api' ),
						'selected' => get_option( 'whtp_geolocation_api' ),
					)
				);
				echo Input(
					array(
						'id'    => 'whtp-ipinfo-token',
						'name'  => 'whtp-ipinfo-token',
						'label' => 'Access Token',
						'type'  => 'password',
						'value' => get_option( 'whtp_ipinfo_token', '' ),
					)
				);
				echo '<p>' . __( 'An Access Token is required if you prefer ipinfo.io', 'whtp' ) . '</p>';
				?>
			</div>
			<div class="mdl-card__actions">
				<input type="hidden" name="update-ipinfo-integration" value="update-ipinfo-integration" /> 
				<input type="submit" value="Update Options" class="whtp-link mdl-button mdl-js-button mdl-typography--text-uppercase mdl-color--teal-300" />

				<a href="https://ipinfo.io/account" class="mdl-button mdl-js-button mdl-js-ripple-effect" target="_blank">Get your API Token</a>
			</div>
		</div>

	<?php do_action( 'whtp-settings-after' ); ?>
	</div>
</form>

<div class="mdl-color--white mdl-cell mdl-cell--12-col">
	<div class="whtp-updates mdl-card mdl-shadow--2dp mdl-cell mdl-cell--12-col">
		<div class="mdl-card__title mdl-color--teal-300">
	<?php esc_attr_e( 'Tools', 'whtp' ); ?>
		</div>
		<div class="mdl-card__supporting-text mdl-color-text--grey-600">
			<a href="<?php echo admin_url( 'admin.php?page=whtp-force-update' ); ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-color--teal-300" target="_blank">
				<?php esc_attr_e( 'Click here to Force Database Update', 'whtp' ); ?>
			</a>            
		</div>
	</div>  
</div>
