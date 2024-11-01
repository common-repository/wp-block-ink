<?php

class WP_Block_Ink_Customizer_Switch_Control extends WP_Customize_Control {
	public $type = 'toggle';

	public function enqueue() {
		$maybe_minified = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style(
			'wp-block-ink-switch',
			plugins_url( 'assets/wp-block-ink-switch'. $maybe_minified .'.css', __FILE__ )
		);
	}

	/**
	 * Render the control in the customizer
	 */
	public function render_content() {
		?>
        <div class="toggle-switch-control">
            <div class="toggle-switch">
                <input type="checkbox" id="<?php echo esc_attr( $this->id ); ?>"
                       name="<?php echo esc_attr( $this->id ); ?>" class="toggle-switch-checkbox"
                       value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link();
				checked( $this->value() ); ?>>
                <label class="toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
                    <span class="toggle-switch-inner"></span>
                    <span class="toggle-switch-switch"></span>
                </label>
            </div>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( ! empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php } ?>
        </div>
		<?php
	}
}