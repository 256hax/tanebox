<?php
// テーマの読込
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style' ) );
}

// Markdown Editor Plugin.
// By default Markdown Editor is only enabled on Posts, but you can enable it on pages and custom post types by adding post type support.
// reference: https://wordpress.org/plugins/markdown-editor/
add_post_type_support( 'page', 'wpcom-markdown' );
add_post_type_support( 'reviews', 'wpcom-markdown' );
add_post_type_support( 'blog', 'wpcom-markdown' );
add_post_type_support( 'photos', 'wpcom-markdown' );
add_post_type_support( 'customize', 'wpcom-markdown' );
?>
