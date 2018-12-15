<?php
/**
 * Shorcode Button
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @license GPLV2
 * @since 1.0.0
 */

if ( ! class_exists( 'CPTUIEXT_Shortcode_Admin', false ) ) {

	/**
	 * CPTUIEXT_Shortcode_Admin
	 *
	 * Sets up shortcode button
	 */
	class CPTUIEXT_Shortcode_Admin extends WDS_Shortcode_Admin {

		/**
		 * Hooks
		 *
		 * @return void
		 */
		public function hooks() {
			add_filter( $this->shortcode . '_shortcode_fields', array( $this, 'filter_shortcode_field' ), 10, 2 );
			parent::hooks();
		}

		/**
		 * Array of button data
		 *
		 * @return array
		 */
		function js_button_data() {

			return array(
				'qt_button_text' => esc_attr__( 'CPTUI', 'cptuiext' ),
				'button_tooltip' => esc_html__( 'Custom Post Type UI Extended', 'cptuiext' ),
				'icon'           => 'dashicons-format-aside',
				'include_close'  => true,
				'modalClass'     => 'cptui',
				'modalHeight'    => 'auto',
				'modalWidth'     => 800,
			);

		}

		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @param array $fields CMB2 fields data.
		 * @param array $button_data Shotcode button data.
		 * @return array $fields
		 */
		function fields( $fields, $button_data ) {

			$fields[] = array(
				'name'             => esc_html__( 'Choose a Post Type', 'cptuiext' ) . '<a style="float:right;" id="support-beacon" href="#" data-search="Shortcode Builder">' . esc_html__( 'Need Help?', 'cptuiext' ) . '</a>',
				'desc'             => esc_html__( 'Choosing a post type will filter the shortcode select', 'cptuiext' ),
				'id'               => 'cptui_posttype',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => cptui_get_post_types(),
			);

			/**
			 * Filters the fields to be added to the button modal.
			 *
			 * @since 1.0.0
			 *
			 * @param array $fields Array of CMB2 fields data.
			 */
			$fields = apply_filters( 'cptui_fields', $fields );

			return $fields;
		}

		/**
		 * Filters the data sent to the editor.
		 *
		 * @todo What's the point of this method.
		 *
		 * @param array $fields CMB2 fields data.
		 * @param array $shortcode_button Shortcode buttond data.
		 * @return array
		 */
		function filter_shortcode_field( $fields, $shortcode_button ) {
			if ( ! $shortcode_button instanceof Shortcode_Button ) {
				return $fields;
			}

			return $fields;
		}
	}

}
