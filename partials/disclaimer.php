<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<?php
	$geo_api           = get_option( 'whtp_geolocation_api', 'ipapi' );
	$whtp_geo_provider = 'ipinfo' === $geo_api ? sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://ipinfo.io/', 'IpInfo' ) : sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'http://ip-api.com/', 'ip-api.com' );
	echo wp_kses_post( sprintf( 'IP Location data provided by %s', $whtp_geo_provider ) );
	?>
</p>
<p>
	<?php esc_attr_e( 'The accuracy of the Geolocation data used in this plugin is not guaranteed.', 'whtp' ); ?>
</p>
