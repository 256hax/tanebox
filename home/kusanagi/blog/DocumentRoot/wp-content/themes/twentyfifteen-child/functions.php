<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style')
  );
}

/*--- [Security] remove WordPress and Plugins version display ---*/
// something like that => <meta name="generator" content="WordPress 5.1.1" />
remove_action('wp_head','wp_generator'); // meta name="generator"

// something like that => xxx.js?ver=4.6.1'
function remove_cssjs_ver2( $src ) {
	if ( strpos( $src, 'ver=' ) )
	$src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver2', 9999 );
add_filter( 'script_loader_src', 'remove_cssjs_ver2', 9999 );
?>
