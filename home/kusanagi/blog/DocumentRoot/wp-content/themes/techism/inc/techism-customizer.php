<?php

/*
 * techism Customizer
 *
 * @since techism 1.0
 * @package techism
 *
 */
function techism_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'techism_customize_register' );

/*
 * Loads Theme Customizer preview changes asynchronously.
 *
 * @since techism 1.0
 */
function techism_customize_preview_js() {
	wp_enqueue_script( 'techism-customizer', get_template_directory_uri() . '/assets/js/theme-customizer.js', array( 'customize-preview' ), '2013-12-20', false );
}
add_action( 'customize_preview_init', 'techism_customize_preview_js' );


function techism_theme_customizer($wp_customize){
//techism customizer begins

/* Slider Setting */
$wp_customize->add_section( 'techism_slider_section' , array(
    'title'       => __( 'Slider Setting', 'techism' ),
    'priority'    => 70,
    'description' => 'Customize the homepage slider',
) );
$wp_customize->add_setting(
    'techism_slider_activate',
    array(
			'sanitize_callback'	   => 'techism_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
    'techism_slider_activate',
    array(
        'type' => 'checkbox',
        'label' => 'Activate Homepage Slider',
        'section' => 'techism_slider_section'
    )
);


/**
  * techism Category Drop Down List Class
  * modified dropdown-pages from wp-includes/class-wp-customize-control.php *
  * @since techism v1.0
  */

if (class_exists('WP_Customize_Control'))
	{
	class WP_Customize_Dropdown_Categories_Control extends WP_Customize_Control

		{
		public $type = 'dropdown-categories';

		public

		function render_content()
			{
			$dropdown = wp_dropdown_categories(array(
				'name' => '_customize-dropdown-categories-' . $this->id,
				'echo' => 0,
				'hide_empty' => false,
				'show_option_none' => '&mdash; ' . __('Select', 'techism') . ' &mdash;',
				'hide_if_empty' => false,
				'selected' => $this->value() ,
			));
			$dropdown = str_replace('<select', '<select ' . $this->get_link() , $dropdown);
			printf('<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>', $this->label, $dropdown);
			}
		}
	}

$wp_customize->add_setting('techism_slider_category', array(
	'sanitize_callback'   => 'techism_sanitize_integer'
));
$wp_customize->add_control(new WP_Customize_Dropdown_Categories_Control($wp_customize, 'techism_slider_category', array(
	'label' => __('Featured Category', 'techism') ,
	'section' => 'techism_slider_section',
	'type' => 'dropdown-categories',
	'settings' => 'techism_slider_category',
	'priority' => 60,
)));

// Number of post control
$wp_customize->add_setting('techism_slider_postnum', array(
'sanitize_callback'   => 'techism_sanitize_integer',
'default'        => '5',
) );

$wp_customize->add_control('techism_slider_postnum', array(
 'label'   => 'Number of Slides',
'section' => 'techism_slider_section',
'type'    => 'select',
'choices' => array("1", "2", "3", "4", "5", "6", "7", "8", "9" ),
'priority' => 70
));

/* Slider Setting Ended */

/* techism Homepage setting to enable full post/excerpt */
$wp_customize->add_section( 'techism_Homepage_setting' , array(
    'title'       => __( 'Homepage Setting', 'techism' ),
    'priority'    => 70,
    'description' => 'Customize Homepage layout',
) );
$wp_customize->add_setting(
    'techism_home_content', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_home_content',
    array(
        'type' => 'checkbox',
        'label' => 'Enable Full Post on Home/Archive/Category/Search pages',
        'section' => 'techism_Homepage_setting',
    )
);

/* techism Fonts setting to enable/disable google web fonts */
$wp_customize->add_section( 'techism_font_section' , array(
    'title'       => __( 'Google Web Fonts Setting', 'techism' ),
    'priority'    => 90,
    'description' => 'Customize Google Web Fonts Fetching Settings.',
) );
$wp_customize->add_setting(
    'techism_google_font', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_google_font',
    array(
        'type' => 'checkbox',
        'label' => 'Disable Opensans Google Font.',
        'section' => 'techism_font_section',
    )
);
/* Post Meta Setting */
$wp_customize->add_section( 'techism_postmeta_section' , array(
    'title'       => __( 'Post Meta Settings', 'techism' ),
    'priority'    => 80,
    'description' => 'Enable/Disable Differnt Post Meta Shown in Homepage and post pages',
) );

// time and date
$wp_customize->add_setting(
    'techism_posttime_activate', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_posttime_activate',
    array(
        'type' => 'checkbox',
        'label' => 'Hide Time Below Post Title',
        'section' => 'techism_postmeta_section',
    )
);

// user name
$wp_customize->add_setting(
    'techism_postuser_activate', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_postuser_activate',
    array(
        'type' => 'checkbox',
        'label' => 'Hide Post User Below Post Title',
        'section' => 'techism_postmeta_section',
    )
);

// comment section
$wp_customize->add_setting(
    'techism_postcommentcount_activate', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_postcommentcount_activate',
    array(
        'type' => 'checkbox',
        'label' => 'Hide Comment Count Below Post Title',
        'section' => 'techism_postmeta_section',
    )
);

// category section
$wp_customize->add_setting(
    'techism_postcategory_activate', array(
    	'sanitize_callback'   => 'techism_sanitize_checkbox'
    	)
	);
	$wp_customize->add_control(
    'techism_postcategory_activate',
    array(
        'type' => 'checkbox',
        'label' => 'Hide Category Name Below Post Title',
        'section' => 'techism_postmeta_section',
    )
);
}
add_action('customize_register', 'techism_theme_customizer');

/**
 * Sanitize Integer
 *
 * @since  2.0.9
 * @access public
 * @return sanitized output
 */
function techism_sanitize_integer( $int ) {
	if( is_numeric( $int ) ) {
	return intval( $int );
}
}

/**
 * Sanitize text
 *
 * @since  2.0.9
 * @access public
 * @return sanitized output
 */
function techism_sanitize_text( $txt ) {
    return wp_kses_post( force_balance_tags( $txt ) );
}
/**
 * Sanitize text
 *
 * @since  2.0.9
 * @access public
 * @return sanitized output
 */
function techism_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
        return 1;
    } else {
        return '';
    }
}