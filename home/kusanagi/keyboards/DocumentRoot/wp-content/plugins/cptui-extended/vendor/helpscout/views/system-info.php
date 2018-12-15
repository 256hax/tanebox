<?php
/**
 * @todo  - move all these vars to methods before view is included
 */
global $wpdb;

if ( class_exists( 'Browser' ) ) {
	$browser = new Browser();
}

if ( get_bloginfo( 'version' ) < '3.4' ) {
	$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
	$theme	  = $theme_data['Name'] . ' ' . $theme_data['Version'];
} else {
	$theme_data = wp_get_theme();
	$theme	  = $theme_data->Name . ' ' . $theme_data->Version;
}

$plugins	 = get_plugins();
$pg_count	= count( $plugins );

// MU plugins
$mu_plugins = get_mu_plugins();

if ( $mu_plugins ) {
	$mu_count = count( $mu_plugins );
}

$active	  = get_option( 'active_plugins', array() );
$ac_count	= count( $active );
$ic_count	= $pg_count - $ac_count;

// if multisite, grab network as well
if ( is_multisite() ) :
	$net_plugins = wp_get_active_network_plugins();
	$net_active  = get_site_option( 'active_sitewide_plugins', array() );
	$network_active_plugins = count( $net_plugins );
endif;


$info = array(

	'Multisite' =>	   	is_multisite() ? 'Yes' : 'No',

	'SITE_URL' =>			site_url(),
	'HOME_URL' =>			   home_url(),

	'WordPress Version' =>	  get_bloginfo( 'version' ),
	'Permalink Structure' =>	get_option( 'permalink_structure' ),
	'Active Theme' =>		$theme,

	'Registered Post Types' =>	implode( ', ', get_post_types( '', 'names' ) ),
	'Registered Post Stati' =>   implode( ', ', get_post_stati() ),

	'Browser Name' =>		  $browser->getBrowser(),
	'Browser Version' =>		 $browser->getVersion(),
	'Browser User Agent' =>	   $browser->getUserAgent(),
	'Platform' =>				 $browser->getPlatform(),

	'PHP Version' =>			 PHP_VERSION,
	'MySQL Version' =>			$wpdb->db_version(),

	'Web Server Info' =>		 $_SERVER['SERVER_SOFTWARE'],

	'PHP Safe Mode' =>			ini_get( 'safe_mode' ) ? "Yes" : "No",
	'PHP Memory Limit' =>		 ini_get( 'memory_limit' ),
	'PHP Upload Max Size' =>	 ini_get( 'upload_max_filesize' ),
	'PHP Post Max Size' =>	   ini_get( 'post_max_size' ),
	'PHP Upload Max Filesize' =>  ini_get( 'upload_max_filesize' ),
	'PHP Time Limit' =>		   ini_get( 'max_execution_time' ),
	'PHP Max Input Vars' =>	   ini_get( 'max_input_vars' ),

	'WP_DEBUG' =>				 defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set',

	'WP Table Prefix' =>		  $wpdb->prefix,

	'Show On Front' =>			get_option( 'show_on_front' ),
	'Page On Front'	=>		get_option( 'page_on_front' ),
	'Page For Posts' =>		   get_option( 'page_for_posts' ),

	'Session' =>				  isset( $_SESSION ) ? 'Enabled' : 'Disabled',
	'Session Name' =>			 esc_html( ini_get( 'session.name' ) ),
	'Cookie Path' =>			  esc_html( ini_get( 'session.cookie_path' ) ),
	'Save Path' =>				esc_html( ini_get( 'session.save_path' ) ),
	'Use Cookies' =>			  ini_get( 'session.use_cookies' ) ? 'On' : 'Off',
	'Use Only Cookies' =>		 ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off',

	'WordPress Memory Limit' =>   WP_MEMORY_LIMIT,
	'DISPLAY ERRORS' =>	   ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'NA',
	'FSOCKOPEN' =>				( function_exists( 'fsockopen' ) ) ? __( 'Your server supports fsockopen.', 'helpscout' ) : __( 'Your server does not support fsockopen.', 'helpscout' ),
	'cURL' =>					 ( function_exists( 'curl_init' ) ) ? __( 'Your server supports cURL.', 'helpscout' ) : __( 'Your server does not support cURL.', 'helpscout' ),
	'SOAP Client' =>			  ( class_exists( 'SoapClient' ) ) ? __( 'Your server has the SOAP Client enabled.', 'helpscout' ) : __( 'Your server does not have the SOAP Client enabled.', 'helpscout' ),
	'SUHOSIN' =>				  ( extension_loaded( 'suhosin' ) ) ? __( 'Your server has SUHOSIN installed.', 'helpscout' ) : __( 'Your server does not have SUHOSIN installed.', 'helpscout' ),

	'TOTAL PLUGINS' => $pg_count,
	'ACTIVE PLUGINS' => $ac_count,
	'INACTIVE PLUGINS' => $ic_count,

	//'PLUGINS' => $plugins,

);

return json_encode( $info );
