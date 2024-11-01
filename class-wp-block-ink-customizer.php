<?php

/**
 * Hook Theme Supports Global If Colors Established
 */
class WP_Block_Ink_Customizer {

	/**
	 * Initialize Customizer Functionality & Assets
	 *
	 * NOTE: Using 'postMessage' for every Setting is critical to get JavaScript show/hide working correctly.
	 *
	 */
	function __construct() {
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'customize_register', array( $this, 'register_controls' ), 500 );
		add_action( 'customize_register', array( $this, 'register_color_settings_and_controls' ), 500 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'load_custom_control_assets' ) );
	}

	/**
	 * Registration for all non-color field options.
	 *
	 *
	 * @param object $wp_customize - Instance of WP_Customize_Manager.
	 */
	function register_settings( $wp_customize ) {

		$wp_customize->add_setting( 'wp_block_ink_enabled', array(
			'default'   => false,
			'type'      => 'option',
			'transport' => 'postMessage',
		) );


		$wp_customize->add_setting( 'wp_block_ink_color_count', array(
			'default'   => 6, // encourage a smart default...
			'type'      => 'option',
			'transport' => 'postMessage',
		) );


		$wp_customize->add_setting( 'wp_block_ink_disable_custom', array(
			'default'   => 'on',
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

	}

	/**
	 * Registration for all non-color field controls.
	 *
	 * @param object $wp_customize - Instance of WP_Customize_Manager.
	 */
	function register_controls( $wp_customize ) {
		require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-customizer-switch-control.php';

		$wp_customize->add_control( new WP_Block_Ink_Customizer_Switch_Control(
			$wp_customize, 'wp_block_ink_enabled',
			array(
				'label'       => __( 'Custom Block Ink', 'wp-block-ink' ),
				'description' => __( 'Set custom colors for the Editor color palette.', 'wp-block-ink' ),
				'section'     => 'colors',
				'settings'    => 'wp_block_ink_enabled',
			)
		) );

		$wp_customize->add_control( 'wp_block_ink_color_count', array(
			'type'            => 'number',
			'section'         => 'colors',
			'settings'        => 'wp_block_ink_color_count',
			'label'           => 'Palette Size',
			'description'     => __( 'Number of colors available in the Editor', 'wp-block-ink' ),
			'input_attrs'     => array(
				'min'     => 1,
				'max'     => 11,
				'onwheel' => 'this.blur()',
			),
			'active_callback' => function () use ( $wp_customize ) {
				$enabled = $wp_customize->get_setting( 'wp_block_ink_enabled' )->value();

				return $enabled ? true : false;
			},
		) );

		$wp_customize->add_control( new WP_Block_Ink_Customizer_Switch_Control(
			$wp_customize, 'wp_block_ink_disable_custom',
			array(
				'label'           => __( 'Prevent Non-Palette Colors', 'wp-block-ink' ),
				'section'         => 'colors',
				'settings'        => 'wp_block_ink_disable_custom',
				'description'     => __( 'Prevents users from setting custom colors in Gutenberg color palettes.', 'wp-block-ink' ),
				'active_callback' => function () use ( $wp_customize ) {
					$enabled = $wp_customize->get_setting( 'wp_block_ink_enabled' )->value();

					return $enabled ? true : false;
				},
			)
		) );
	}

	/**
	 * Need loop to register these nicely
	 *
	 * @param object $wp_customize - Instance of WP_Customize_Manager.
	 */
	function register_color_settings_and_controls( $wp_customize ) {
		for ( $i = 1; $i <= 11; $i ++ ) {
			$wp_customize->add_setting( 'wp_block_ink_color_' . $i . '_name', array(
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_setting( 'wp_block_ink_color_' . $i, array(
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'wp_block_ink_color_' . $i . '_name', array(
				'type'            => 'text',
				'section'         => 'colors',
				'settings'        => 'wp_block_ink_color_' . $i . '_name',
				'label'           => 'Palette Color ' . $i . ' Name',
				'description'     => __( '', 'wp-block-ink' ),
				'active_callback' => function () use ( $i, $wp_customize ) {
					$enabled = $wp_customize->get_setting( 'wp_block_ink_enabled' )->value();
					$show    = $wp_customize->get_setting( 'wp_block_ink_color_count' )->value();

					return $enabled && $i <= $show ? true : false;
				},
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize,
				'wp_block_ink_color_' . $i,
				array(
					'label'             => __( 'Palette Color ' . $i, 'wp-block-ink' ),
					'section'           => 'colors',
					'settings'          => 'wp_block_ink_color_' . $i,
					'sanitize_callback' => 'sanitize_hex_color',
					'active_callback'   => function () use ( $i, $wp_customize ) {
						$enabled = $wp_customize->get_setting( 'wp_block_ink_enabled' )->value();
						$show    = $wp_customize->get_setting( 'wp_block_ink_color_count' )->value();

						return $enabled && $i <= $show ? true : false;
					},
				)
			) );
		}
	}


	/**
	 * Initialize Show/Hide JavaScript in Customizer only.
	 */
	function load_custom_control_assets() {
		$maybe_minified = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script(
			'wp-block-ink-customizer',
			plugins_url( 'assets/wp-block-ink-customizer' . $maybe_minified . '.js', __FILE__ )
		);
	}

}
