<?php

/**
 * Class WP_Block_Ink_Apply_Styles
 */
class WP_Block_Ink_Apply_Styles {
	/**
	 * WP_Block_Ink_Apply_Styles constructor
	 */
	function __construct() {
		add_action( 'wp_head', array( $this, 'public_frontend_css' ), 150 );
		add_action( 'init', array( $this, 'admin_editor_css' ) );
	}

	/**
	 * Inserts inline style into <head> of Theme
	 */
	function public_frontend_css() {
		global $_wp_theme_features;

		if ( ! empty( $_wp_theme_features['editor-color-palette'][0] ) && ! empty( get_option( 'wp_block_ink_color_count' ) ) ) {
			echo '<style type="text/css" data-wp-block-ink-palette-styles="1">';
			foreach ( $_wp_theme_features['editor-color-palette'][0] as $color ) {
				echo '.has-' . $color['slug'] . '-color{color:' . $color['color'] . ';}';
				echo '.has-' . $color['slug'] . '-background-color{background-color:' . $color['color'] . ';}';
			}
			echo '</style>';
		}
	}

	/**
	 * Hooks inline style onto the Core Blocks CSS file, assuring theme colors always apply even if names match.
	 */
	function admin_editor_css() {
		global $_wp_theme_features;

		if ( ! is_admin() ) {
			return;
		}

		if ( ! empty( $_wp_theme_features['editor-color-palette'][0] ) && ! empty( get_option( 'wp_block_ink_color_count' ) ) ) {
			ob_start();
			foreach ( $_wp_theme_features['editor-color-palette'][0] as $color ) {
				echo '.has-' . $color['slug'] . '-color{color:' . $color['color'] . ';}';
				echo '.has-' . $color['slug'] . '-background-color{background-color:' . $color['color'] . ';}';
			}
			wp_add_inline_style( 'wp-core-blocks-theme', ob_get_clean() );
		}
	}
}
