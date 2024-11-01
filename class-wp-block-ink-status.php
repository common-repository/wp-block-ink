<?php

/**
 * Class WP_Block_Ink_Status
 */
class WP_Block_Ink_Status {
	/**
	 * WP_Block_Ink_Status constructor.
	 */
	function __construct() {
		add_action( 'plugin_row_meta', array( $this, 'plugin_list_status_indicator' ), 10, 4 );
	}

	/**
	 * Adds Status to Plugin List
	 */
	function plugin_list_status_indicator( $links, $file, $data, $status ) {
		if ( false !== strpos( $file, 'wp-block-ink.php' ) ) {
			$links = array_merge(
				$links,
				array(
					'<strong><a href="' . esc_url( add_query_arg( array( 'autofocus[control]' => 'wp_block_ink_enabled' ), admin_url( 'customize.php' ) ) ) . '">🖌 Fill Inkjets</a></strong>',
					static::return_response_markup( static::determine_palette_origin() )
				)
			);
		}

		return $links;
	}

	/**
	 * @return string
	 */
	static function determine_palette_origin() {
		global $_wp_theme_features;

		if (
			function_exists( 'gutenberg_init' )
			&& wp_block_ink_is_enabled()
			&& ! empty( $_wp_theme_features['editor-color-palette'][0] )
			&& ! empty( get_option( 'wp_block_ink_color_count' ) )
		) {
			return 'wp-block-ink';
		} elseif (
			function_exists( 'gutenberg_init' )
			&& ! empty( $_wp_theme_features['editor-color-palette'][0] )
		) {
			return 'theme';
		} elseif (
			function_exists( 'gutenberg_init' )
			&& empty( $_wp_theme_features['editor-color-palette'][0] )
		) {
			return 'wordpress';
		} elseif ( version_compare( phpversion(), '5.3', '<' ) ) {
			return 'old-php';
		} else {
			return 'no-gutenberg';
		}
	}

	/**
	 * Return human-readable, color + iconized status based on status string.
	 *
	 * @param $status
	 *
	 * @return string
	 */
	static function return_response_markup( $status ) {
		switch ( $status ) {
			case 'old-php':
				$markup = '<strong style="color:#CB423B;"><span class="dashicons dashicons-warning"></span> '
				          . __( 'Please Upgrade to PHP 5.3', 'wp-block-ink' ) .
				          '</strong>';
				break;
			case 'no-gutenberg':
				$markup = '<strong style="color:#CB423B;"><span class="dashicons dashicons-warning"></span> '
				          . __( 'Please Activate Gutenberg Editor', 'wp-block-ink' ) .
				          '</strong>';
				break;
			case 'wordpress':
				$markup = '<strong style="color:#459ECD;"><span class="dashicons dashicons-yes"></span> '
				          . __( 'WordPress Palette', 'wp-block-ink' ) .
				          '</strong>';
				break;
			case 'theme':
				$markup = '<strong style="color:#459ECD;"><span class="dashicons dashicons-yes"></span> '
				          . __( 'Theme Palette', 'wp-block-ink' ) .
				          '</strong>';
				break;
			case 'wp-block-ink':
				$markup = '<strong style="color:#65B15C;"><span class="dashicons dashicons-yes"></span> '
				          . __( 'Custom Palette', 'wp-block-ink' ) .
				          '</strong>';
				break;
			default:
				$markup = 'Something went wrong';
				break;
		}

		return $markup;
	}

}
