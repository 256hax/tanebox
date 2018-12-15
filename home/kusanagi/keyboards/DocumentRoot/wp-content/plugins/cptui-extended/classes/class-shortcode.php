<?php
/**
 * CPTUIEXT_Shortcode class
 *
 * @package CPTUIExtended
 * @subpackage Loader
 * @author Pluginize
 * @since 1.0.0
 */

if ( ! class_exists( 'CPTUIEXT_Shortcode', false ) ) {

	class CPTUIEXT_Shortcode extends WDS_Shortcodes {

		/**
		 * The Shortcode Tag.
		 *
		 * @var string
		 */
		public $shortcode = 'cptui';

		/**
		 * Default attributes applied to the shortcode.
		 *
		 * @var array
		 */
		public $atts_defaults = array(
			'some_default_key' => 'default value',
		);

		/**
		 * Shortcode Output.
		 */
		public function shortcode() {

			// Parse default shortcode attributes.
			$attributes = $this->shortcode_object->atts;
			$default_atts = cptui_get_default_atts( $attributes['cptui_shortcode'] );

			/**
			 * Filters the arguments used for the shortcode.
			 *
			 * @since 1.0.0
			 *
			 * @param array $value Array of parsed values for the shortcode.
			 */
			$attributes = apply_filters( 'cptui_shortcode_attributes', wp_parse_args( $attributes, $default_atts ) );

			foreach ( $attributes as $attribute => $value ) {
				$result = substr( $attribute, 0, 8 );
				if ( 'taxonomy' === $result ) {
					$string = explode( 'taxonomy-', $attribute );
					if ( isset( $string[1] ) ) {
						$attributes['taxonomy'][ $string[1] ] = explode( ',', $attributes[ $attribute ] );
					}
					unset( $attributes[ $attribute ] );
				}
			}

			// Possibly available later at the template stage.
			$args = array(
				'post_type' => esc_attr( $attributes['cptui_posttype'] ),
			);

			if ( isset( $attributes['taxonomy'] ) ) {
				$attributes['tax_query']['relation'] = apply_filters( 'cptui_tax_query_relation', 'AND' );
				foreach ( $attributes['taxonomy'] as $taxonomy => $value ) {

					$tax = array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => $value,
					);

					$attributes['tax_query'][] = $tax;
				}
			}

			/**
			 * Filter shortcode template location
			 *
			 * @since 1.0.0
			 *
			 * @param array $attributes shortcode attributes.
			 */
			$template = apply_filters( 'cptui_shortcode_template',  cptui_locate_template( $attributes['cptui_shortcode'] ), $attributes );

			/**
			 * Hook when a shortcode loads.
			 *
			 * @since 1.0.0
			 *
			 * @param array $attributes shortcode attributes.
			 */
			do_action( $attributes['cptui_shortcode'] . '_shortcode_loaded', $attributes );

			// Load scripts per shortcode.
			if ( isset( $attributes['cptui_shortcode'] ) && ! is_admin() ) {
				if ( $styles = cptui_get_shortcode_style( $attributes['cptui_shortcode'] ) ) {

					if ( is_array( $styles ) ) {
						foreach ( $styles as $style ) {
							wp_enqueue_style( $style );
						}
					} else {
						wp_enqueue_style( $styles );
					}
				}
				if ( $scripts = cptui_get_shortcode_script( $attributes['cptui_shortcode'] ) ) {

					if ( is_array( $scripts ) ) {
						foreach ( $scripts as $script ) {
							wp_enqueue_script( $script );
						}
					} else {
						wp_enqueue_script( $scripts );
					}
				}
			}

			$cptui_class = cptui_shortcode_classes( $attributes['cptui_shortcode'] );
			$cptui_class .= ' cptui-shortcode-' . $attributes['cptui_shortcode_id'];
			$template_loader = new CPTUIEXT_Template_Loader();

			/*
			 * $template above gets set based on default locations provided by cptui_get_shortcode()
			 * and determining the template shortcode requested in this class. Instead of trying to
			 * re-fetch and re-process for CPTUIEXT_Template_Loader(), we will just grab the basename
			 * to pass into get_template_part().
			 */
			ob_start();
			echo '<div id="cptui" class="'. esc_attr( $cptui_class ) .'">';
			$template_loader->set_template_data( $attributes, 'attributes' );
			$template_loader->get_template_part( basename( $template, '.php' ) );
			echo '</div>';
			$shortcode = ob_get_contents();
			ob_end_clean();
			return $shortcode;

		}

		/**
		 * Override for attribute getter.
		 *
		 * You can use this to override specific attribute acquisition
		 * ex. Getting attributes from options, post_meta, etc...
		 *
		 * @see WDS_Shortcode::att
		 *
		 * @param string      $att     Attribute to override.
		 * @param string|null $default Default value.
		 * @return string
		 */
		public function att( $att, $default = null ) {
			$current_value = parent::att( $att, $default );
			return $current_value;
		}
	}

}
