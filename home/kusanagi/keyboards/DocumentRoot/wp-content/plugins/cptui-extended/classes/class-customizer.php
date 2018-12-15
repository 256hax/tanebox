<?php
/**
 * CPTUI Extended Customizer integration.
 *
 * @package CPTUIExtended
 * @subpackage Loader
 * @author Pluginize
 * @since 1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Our Customizer additions.
 *
 * @internal
 */
class CPTUIEXT_Customizer {

	/**
	 * CPTUIEXT_Customizer constructor.
	 *
	 * @access public
	 * @since  1.3.0
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'cptui_ext_customizer_sections' ) );
		add_action( 'wp_head', array( $this, 'cptui_ext_custom_css_output' ) );
	}

	/**
	 * Add all Sections to the Customizer
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 *
	 * @access public
	 * @since  1.3.0
	 */
	public function cptui_ext_customizer_sections( $wp_customize ) {
		// Add our Customizer Section.
		// https://developer.wordpress.org/themes/advanced-topics/customizer-api/#sections.
		$wp_customize->add_section(
			'cptui_ext_section', array(
				'title'       => esc_html__( 'CPT UI Extended', 'cptuiext' ),
				'description' => esc_html__( 'Settings for CPT UI Extended.', 'cptuiext' ),
			)
		);

		// Add our Settings to Section.
		$this->cptui_ext_customizer_settings( $wp_customize );
	}

	/**
	 * Add all Settings to the Customizer
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 *
	 * @access private
	 * @since  1.3.0
	 */
	private function cptui_ext_customizer_settings( $wp_customize ) {
		// Adjust Shortcode section title size & color.
		// Add Setting to adjust the color of Shortcode section title.
		$wp_customize->add_setting( 'cptui_ext_shortcode_title_color_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our colorpicker Control to allow user to select color of Shortcode section title.
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cptui_ext_shortcode_title_color_control', array(
			'label'    => __( 'Shortcode section Title Text Color', 'cptuiext' ),
			'section'  => 'cptui_ext_section',
			'settings' => 'cptui_ext_shortcode_title_color_setting',
		) ) );

		// Add Setting to adjust font size of Shortcode overall section title.
		$wp_customize->add_setting( 'cptui_ext_shortcode_title_font_size_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our Customizer Control to allow user to adjust Shortcode overall section title.
		$wp_customize->add_control( 'cptui_ext_shortcode_title_font_size_control', array(
			'description' => __( 'Min: 1.5 <--> Max: 4', 'cptuitext' ),
			'label'       => __( 'Shortcode section Title Font Size', 'cptuiext' ),
			'section'     => 'cptui_ext_section',
			'settings'    => 'cptui_ext_shortcode_title_font_size_setting',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1.5,
				'max'  => 4,
				'step' => 0.25,
			),
		) );

		// Adjust an entry title within shortcode output.
		// Add Setting to adjust the color of entry title.
		$wp_customize->add_setting( 'cptui_ext_entry_title_color_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our colorpicker Control to allow user to select color of entry title.
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cptui_ext_entry_title_color_control', array(
			'label'    => __( 'Each Entry Title Text Color', 'cptuiext' ),
			'section'  => 'cptui_ext_section',
			'settings' => 'cptui_ext_entry_title_color_setting',
		) ) );

		// Add Setting to adjust font size of each entry title.
		$wp_customize->add_setting( 'cptui_ext_entry_title_font_size_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our number Control to allow user to adjust font size of each entry title.
		$wp_customize->add_control( 'cptui_ext_entry_title_font_size_control', array(
			'description' => __( 'Min: 1 <--> Max: 4', 'cptuitext' ),
			'label'       => __( 'Each Entry Title Font Size', 'cptuiext' ),
			'section'     => 'cptui_ext_section',
			'settings'    => 'cptui_ext_entry_title_font_size_setting',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 4,
				'step' => 0.25,
			),
		) );

		// Adjust the entry content within shortcode output.
		// Add Setting to adjust the color of entry content.
		$wp_customize->add_setting( 'cptui_ext_entry_content_color_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our colorpicker Control to allow user to select color of entry content.
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cptui_ext_entry_content_color_control', array(
			'label'    => __( 'Each Entry Content Text Color', 'cptuiext' ),
			'section'  => 'cptui_ext_section',
			'settings' => 'cptui_ext_entry_content_color_setting',
		) ) );

		// Add Setting to adjust font size of the entry content.
		$wp_customize->add_setting( 'cptui_ext_entry_content_font_size_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our number Control to allow user to adjust font size of the entry content.
		$wp_customize->add_control( 'cptui_ext_entry_content_font_size_control', array(
			'description' => __( 'Min: 1 <--> Max: 2', 'cptuitext' ),
			'label'       => __( 'The Entry Content Font Size', 'cptuiext' ),
			'section'     => 'cptui_ext_section',
			'settings'    => 'cptui_ext_entry_content_font_size_setting',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 2,
				'step' => 0.25,
			),
		) );

		// Add Setting to adjust bottom padding between each post item.
		$wp_customize->add_setting( 'cptui_ext_post_padding_bottom_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our select Control to allow user to adjust bottom padding between each post item.
		$wp_customize->add_control( 'cptui_ext_post_padding_bottom_control', array(
			'description' => __( 'Min: 0.5 <--> Max: 4', 'cptuitext' ),
			'label'       => __( 'The space between each post.', 'cptuiext' ),
			'section'     => 'cptui_ext_section',
			'settings'    => 'cptui_ext_post_padding_bottom_setting',
			'type'        => 'select',
			'choices' => array(
				'0.5' => 0.5,
				'1'   => 1,
				'1.5' => 1.5,
				'2'   => 2,
				'2.5' => 2.5,
				'3'   => 3,
				'3.5' => 3.5,
				'4'   => 4,
			),
		) );

		// Add Setting to adjust side padding between post and thumbnail on desktop only.
		$wp_customize->add_setting( 'cptui_ext_post_featured_image_spacing_setting', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		// Add our select Control to allow user to adjust side padding between post and thumbnail.
		$wp_customize->add_control( 'cptui_ext_post_featured_image_spacing_control', array(
			'description' => __( 'Min: 2 <--> Max: 10', 'cptuitext' ),
			'label'       => __( 'The space between the Featured Image and post', 'cptuiext' ),
			'section'     => 'cptui_ext_section',
			'settings'    => 'cptui_ext_post_featured_image_spacing_setting',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2,
				'max'  => 10,
				'step' => 1,
			),
		) );
	}

	/**
	 * Output CSS style overrides
	 *
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function cptui_ext_custom_css_output() {
		?>

		<!-- CPT UI Extended Customizer CSS -->
		<style type="text/css" id="cpt-ui-extended-css">
			<?php $this->cptui_ext_generate_css( '.cptui-shortcode-title, .entry-content .cptui-shortcode-title', 'color', 'cptui_ext_shortcode_title_color_setting' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-shortcode-title, .entry-content .cptui-shortcode-title', 'font-size', 'cptui_ext_shortcode_title_font_size_setting', null, 'rem' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-entry-title, .entry-content .cptui-entry-title, .cptui-entry-title .cptui-link', 'color', 'cptui_ext_entry_title_color_setting' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-entry-title, .entry-content .cptui-entry-title, .cptui-entry-title .cptui-link', 'font-size', 'cptui_ext_entry_title_font_size_setting', null, 'rem' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-entry-summary, .entry-content .cptui-entry-summary', 'color', 'cptui_ext_entry_content_color_setting' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-entry-summary, .entry-content .cptui-entry-summary', 'font-size', 'cptui_ext_entry_content_font_size_setting', null, 'rem' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-shortcode .cptui-entry', 'padding-bottom', 'cptui_ext_post_padding_bottom_setting', null, 'rem' ); ?>
			<?php $this->cptui_ext_generate_css( '.cptui-shortcode .cptui-entry-thumbnail ~ .cptui-entry-header, .cptui-shortcode .cptui-entry-thumbnail ~ .cptui-entry-summary, .cptui-shortcode .cptui-entry-thumbnail ~ .cptui-entry-footer', 'padding-left', 'cptui_ext_post_featured_image_spacing_setting', null, '%' ); ?>
		</style>
		<!-- /CPT UI Extended Customizer CSS -->

		<?php
	}

	/**
	 * This will generate a line of CSS for use in header output. If the setting
	 * ($setting_name) has no defined value, the CSS will not be output.
	 *
	 * @uses get_option()
	 *
	 * @since 1.3.0
	 *
	 * @param string $selector     CSS selector.
	 * @param string $style        The name of the CSS *property* to modify.
	 * @param string $setting_name The name of the Customizer Setting option to fetch.
	 * @param string $prefix       Optional. Anything that needs to be output before the CSS property.
	 * @param string $postfix      Optional. Anything that needs to be output after the CSS property.
	 * @param bool   $echo         Optional. Whether to print directly to the page (default: true).
	 * @return string Returns a single line of CSS with selectors and a property.
	 */
	public function cptui_ext_generate_css( $selector, $style, $setting_name, $prefix = '', $postfix = '', $echo = true ) {
		$return = '';
		$customizer_option = get_option( $setting_name );
		if ( ! empty( $customizer_option ) ) {
			$return = sprintf( '%s { %s:%s; }',
				$selector,
				$style,
				$prefix . ' ' . $customizer_option . $postfix
			);
			if ( $echo ) {
				echo esc_html( $return );
			}
		}
		return $return;
	}
}
