<?php
/**
 * Shortcode error template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */
$attributes = (array) cptui_shortcode_atts( $attributes );
/*
 * This file will display if a shortcode template file is not found or an error with the shortcode data.
 */
echo '<span class="cptui-error">' . sprintf( __( 'Sorry, error displaying %s shortcode.', 'cptuiext' ), esc_attr( $attributes['cptui_shortcode'] ) ) . '</span>';
