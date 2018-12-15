<?php
/**
 * CPTUI Extended Help Scout.
 *
 * This file holds customizations and functions needed to add our HelpScout integration
 * for built-in support tasks for the user.
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Output the JavaScript needed to wire up HelpScout integration.
 *
 * @since 1.0.0
 *
 * @internal
 */
function cptui_helpscout_config() {

	$user = wp_get_current_user();

	if ( ! class_exists( 'Browser' ) ) {
		helpscout_customer()->include_vendor( 'browser' );
	}
	$site = helpscout_customer()->include_view( 'system-info' );

	if ( ! is_super_admin() ) : ?>
		<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!0,baseUrl:"//pluginize.helpscoutdocs.com/"},contact:{enabled:!1,formId:"3c553ddb-eb9c-11e5-a329-0ee2467769ff"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>
	<?php else : ?>
		<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!0,baseUrl:"//pluginize.helpscoutdocs.com/"},contact:{enabled:!0,formId:"97cc9fb4-eac6-11e5-a329-0ee2467769ff"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>
	<?php endif ; ?>

	<script>
		HS.beacon.config({
		  modal: true,
		  color: '#288edf',
		  topics: [
			{ val: 'need-help', label: 'Need help with the product' },
			{ val: 'bug', label: 'Bug Report'},
			{ val: 'billing', label: 'Billing'}
		  ],
		  attachment: true,
		  instructions:'Can\'t find help in support docs? Send us a message.'
		});

		var site_info = JSON.parse('<?php echo $site ?>');
		var data = '';
		var searchParam = '';

		for ( var index in site_info ) {
			data += index + ':  ' + site_info[index] + '</br>';
		}

		HS.beacon.ready(function() {
			HS.beacon.identify({
			   name: '<?php esc_attr_e( $user->user_login ); ?>',
			   email: '<?php esc_attr_e( $user->user_email ); ?>',
			   'First Name': '<?php esc_attr_e( $user->user_firstname ); ?>',
			   'Last Name': '<?php esc_attr_e( $user->user_lastname ); ?>',
			   'Site Info': data
			 });
		});

		jQuery( document ).ready(function() {
			 jQuery( '#support-beacon' ).on( 'click', function(e) {
				 searchParam = jQuery(this).data('search');
				 HS.beacon.search( searchParam );
				 HS.beacon.toggle();
				 wp_sc_buttons.close();
			 });
		 });
	</script>
	<?php
}
add_action( 'admin_footer', 'cptui_helpscout_config' );

/**
 * Adds HelpScout beacon toggle to CPTUI pages.
 *
 * @since 1.0.0
 *
 * @internal
 */
function cptui_add_help_button() {
	printf(
		'<a style="float:right; margin:1em; position:absolute; top:10px; right:10px;" id="support-beacon" class="button" href="#" data-search="%s">%s</a>',
		esc_attr__( 'Custom Post Type UI', 'cptuiext' ),
		esc_html__( 'Need Help?', 'cptuiext' )
	);
}
add_action( 'cptui_below_post_type_tab_menu', 'cptui_add_help_button' );
