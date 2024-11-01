<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">    
	<h2 class="mdl-card__title-text"><?php esc_attr_e( 'Help and Support', 'whtp' ); ?></h2>
	<p></p>
	<div class="mdl-color--white mdl-cell mdl-cell--12-col">
		<div class="whtp-updates mdl-card mdl-shadow--2dp mdl-cell mdl-cell--12-col">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'Getting Started', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text mdl-color-text--grey-600">
				<p><?php echo wp_kses_post( '1. Add the shortcode tp any page you want to count. The shortcode is <code>[whohit]Page Name[/whohit]</code>.', 'whtp' ); ?></p>
				<p><?php esc_attr_e( '2. After addin the shortcode to the page, visit the page once and then check if the page is added in the "Who Hit The Page > View Page Hits" page.', 'whtp' ); ?></p>
				<p><?php esc_attr_e( '3. Go to the "Settings" page under "Who Hit The Page - Hit Counter", then under "Uninstall Settings" choose an option that is suitable to your needs and click "Update Options". This is an impotant decision that you need to make regarding the action that should be taken when uninstalling the plugin.', 'whtp' ); ?></p>
			</div>
			<div class="mdl-card__actions mdl-card--border">
				<a href="http://whohit.co.za/who-hit-the-page-hit-counter" target="_blank" class="mdl-button mdl-js-button mdl-js-ripple-effect"><?php esc_attr_e( 'Read More', 'whtp' ); ?></a>
			</div>
		</div>  
	</div>

	<div class="mdl-color--white mdl-cell mdl-cell--12-col">
		<div class="whtp-updates mdl-card mdl-shadow--2dp mdl-cell mdl-cell--12-col">
			<div class="mdl-card__title mdl-color--teal-300">
				<?php esc_attr_e( 'How to?', 'whtp' ); ?>
			</div>
			<div class="mdl-card__supporting-text mdl-color-text--grey-600">                
				<p><?php echo wp_kses_post( 'The hits per page are shown on the first table, and the visitor\'s IP addresses are shown on the last table. Place the following shortcode snippet in any page or post you wish visitors counted on.', 'whtp' ); ?></p>
				<p><?php echo wp_kses_post( '<code>[whohit]-Page name or identifier-[/whohit]</code> - please remember to replace the <code>-Page name or identifier-</code> with the name of the page you placed the shortcode on, if you like you can put in anything you want to use as an identifier for the page.', 'whtp' ); ?></p>
				<p><?php echo wp_kses_post( 'For example: On our <a href="https://whohit.co.za/about-us">about us page</a> we placed <code>[whohit]About Us[/whohit]</code> and on our <a href="https://whohit.co.za/web-hosting">web hosting</a> page we placed <code>[whohit]Web Hosting[/whohit]</code>. Please note that what you put between <code>[whohit]</code> and <code>[/whohit]</code> doesn\'t need to be the same as the page name - that means; for our <a href="https://whohit.co.za/web-design-and-development">website design and development page</a> we can use <code>[whohit]Development[whohit]</code> instead of the whole <code>[whohit]website design and development page[whohit]</code> string, its completely up to you what you put as long as you will be able to see it on your admin what page has how many visits.', 'whtp' ); ?></p>
				<p><?php echo wp_kses_post( 'Please make sure you place the shortcode <code>[whohit]..[/whohit]</code> only once in a page or post, if you place it twice, that page will be counted twice and thats not what you want. If you don\'t put anything between the inner square brackets of the shortcode, like so:<code>[whohit][/whohit]</code>, then you will have an unknown page appering with a count on the hits table and you will not know what page that is on your website.', 'whtp' ); ?></p>
				<p><?php echo wp_kses_post( 'Please link to our website if you like our plugin, we really appreciate your kind gesture. Visit our website at https://whohit.co.za/', 'whtp' ); ?></p>
				<p><?php echo wp_kses_post( 'Please copy and paste this: <code>[supportlink]</code> on any page or post in visual view to display the link shown above.', 'whtp' ); ?></p>
			</div>
			<div class="mdl-card__actions mdl-card--border">
				<a href="http://whohit.co.za/who-hit-the-page-hit-counter" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-color--teal-300" target="_blank">
		<?php esc_attr_e( 'Read Documentation', 'whtp' ); ?>
				</a>
				<a href="http://lindeni.co.za" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-color--teal-300" target="_blank">
		<?php esc_attr_e( 'Author\'s Website', 'whtp' ); ?>
				</a>
			</div>
		</div>  
	</div>    

	<div class="whtp-graphs mdl-shadow--2dp mdl-color--white mdl-cell mdl-cell--12-col">
		<?php require_once WHTP_PLUGIN_DIR_PATH . 'partials/disclaimer.php'; ?>
	</div>
</div>
