<?php

/**
 * Class WP_Block_Ink_Intercept_Palette
 */
class WP_Block_Ink_Intercept_Palette {
	/**
	 * WP_Block_Ink_Intercept_Palette constructor.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'intercept_color_palette' ), 8 );
	}

	/**
	 * Tap into Theme Features global as Plugins can't use add_theme_support()
	 */
	function intercept_color_palette() {
		global $_wp_theme_features;

		$total = (int) esc_html__( get_option( 'wp_block_ink_color_count', 11 ), 'wp-block-ink' ); // default is 11 colors, never more, maybe less.
		$data  = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$color = esc_html__( get_option( 'wp_block_ink_color_' . $i ), 'wp-block-ink' );
			$name  = esc_html__( get_option( 'wp_block_ink_color_' . $i . '_name' ), 'wp-block-ink' );
			if ( ! empty( $color ) && ! empty( $name ) ) {
				$data[] = array(
					'name'  => $name,
					'slug'  => sanitize_title_with_dashes( $name ),
					'color' => $color,
				);
			}
		}

		if ( 1 == get_option( 'wp_block_ink_disable_custom' ) ) {
			$_wp_theme_features['disable-custom-colors'] = true;
		}

		if ( ! empty( $data ) ) {
			$_wp_theme_features['editor-color-palette'] = array( $data );
		}

	}
}
