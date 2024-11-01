<?php
/**
 * Plugin Name:     WP Block Ink
 * Plugin URI:      https://github.com/0aveRyan/wp-block-ink
 * Description:     🎨 Set custom colors for the new Editor's color palettes.
 * Author:          Dave Ryan
 * Author URI:      https://daveryan.io
 * Text Domain:     wp-block-ink
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         WPBlockIink
 */

/**
 * Main Helper To Determine Active
 */
function wp_block_ink_is_enabled() {
	return apply_filters( 'wp_block_ink_enabled', get_option( 'wp_block_ink_enabled', false ) );
}


/**
 * Register Admin Status Indicators on Installed Plugins Page
 */
if ( is_admin() ) {
	global $pagenow;

	if ( 'plugins.php' === $pagenow ) { // only run when necessary pls
		require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-status.php';
		new WP_Block_Ink_Status();
	}

	require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-admin-page.php';
	new WP_Block_Ink_Admin_Page();
}

/**
 * 🛑 Hard Stop if Gutenberg Isn't Active or not PHP 5.3+
 */
if ( ! function_exists( 'gutenberg_init' ) || version_compare( phpversion(), '5.3.0', '<' ) ) {
	return;
}

// always needs instantiated w/o scoping because Customizer 🤷‍
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-customizer.php';
new WP_Block_Ink_Customizer();

/**
 * When Enabled, Intercept Theme Supports array() and apply CSS to frontend + Editor.
 */
if ( wp_block_ink_is_enabled() ) {
	require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-intercept-palette.php';
	new WP_Block_Ink_Intercept_Palette();

	require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-apply-styles.php';
	new WP_Block_Ink_Apply_Styles();
}
