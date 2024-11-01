<?php

/**
 * Class WP_Block_Ink_Admin_Page
 */
class WP_Block_Ink_Admin_Page {

	/**
	 * @var string
	 */
	public static $slug = 'wp-block-ink';

	/**
	 * @var string
	 * @see WP_Block_Ink_Status::determine_palette_origin()
	 */
	public $status;

	/**
	 * WP_Block_Ink_Admin_Page constructor.
	 */
	function __construct() {
		add_action( 'admin_init', array( $this, 'setup_status' ), 5 );
		add_action( 'admin_init', array( $this, 'handle_wp_block_ink_admin_redirect' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_page_for_wp_block_ink' ) );
	}

	/**
	 * Setup Block Status
	 */
	function setup_status() {
		if ( ! class_exists( 'WP_Block_Ink_Status' ) ) {
			require_once trailingslashit( dirname( __FILE__ ) ) . 'class-wp-block-ink-status.php';
		}
		$this->status = WP_Block_Ink_Status::determine_palette_origin();
	}

	/**
	 * Force use of &tab parameter for the wp-block-ink Admin Page
	 */
	function handle_wp_block_ink_admin_redirect() {
		if ( ! empty( $_GET['page'] ) && static::$slug === esc_html( $_GET['page'] ) ) {
			if ( empty( $_GET['tab'] ) ) { // always be on a tab
				wp_safe_redirect( esc_url_raw( admin_url( 'themes.php?page=' . static::$slug . '&tab=status' ) ) );
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_enqueues' ) );
		}
	}

	/**
	 * Register & Enqueue Assets Needed on Admin Page
	 */
	function admin_page_enqueues() {
		wp_register_script(
			'wp-block-ink-prism',
			plugin_dir_url( __FILE__ ) . 'assets/prism.js',
			array(),
			'custom'
		);

		wp_register_style(
			'wp-block-ink-prism',
			plugin_dir_url( __FILE__ ) . 'assets/prism.css',
			array(),
			'custom'
		);

		wp_enqueue_script( 'wp-block-ink-prism' );
		wp_enqueue_style( 'wp-block-ink-prism' );
	}

	/**
	 * Register Page Content Callback
	 */
	function add_theme_page_for_wp_block_ink() {
		add_theme_page(
			'Block Ink',
			'🎨 Block Ink',
			'read',
			static::$slug,
			array( $this, 'wp_block_ink_admin_page' )
		);
	}

	/**
	 * Page Content
	 */
	function wp_block_ink_admin_page() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
		?>
        <div class="wrap about-wrap">
            <h1>🎨 <?php esc_html_e( 'Block Ink', 'wp-block-ink' ); ?></h1>
            <div class="about-text"><?php esc_html_e( 'Set custom colors for the new Editor\'s color palette', 'wp-block-ink' ); ?></div>
            <h2 class="nav-tab-wrapper">
                <a href="<?php $this->tab_url( 'status' ); ?>"
                   class="nav-tab<?php if ( 'status' === $tab ) { ?> nav-tab-active<?php } ?>">
                    <span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e( 'Status', 'wp-block-ink' ); ?>
                </a>
				<?php if ( 'wp-block-ink' === $this->status ) { ?>
                    <a href="<?php $this->tab_url( 'style-guide' ); ?>"
                       class="nav-tab<?php if ( 'style-guide' === $tab ) { ?> nav-tab-active<?php } ?>">
                        <span class="dashicons dashicons-art"></span> <?php esc_html_e( get_bloginfo( 'name' ), 'wp-block-ink' ); ?> <?php esc_html_e( 'Style Guide', 'wp-block-ink' ); ?>
                    </a>
                    <a href="<?php $this->tab_url( 'css' ); ?>"
                       class="nav-tab<?php if ( 'css' === $tab ) { ?> nav-tab-active<?php } ?>">
                        <span class="dashicons dashicons-media-code"></span> <?php esc_html_e( 'CSS Reference', 'wp-block-ink' ); ?>
                    </a>
				<?php } ?>
            </h2>
			<?php
			switch ( $tab ) {
				case 'css':
					$this->css_ref();
					break;
				case 'style-guide':
					$this->style_guide();
					break;
				default:
				case 'status':
					$this->status();
					break;

			} ?>
        </div>
		<?php
	}

	/**
	 * Status Indicator & Link into Customizer
	 */
	function status() {
		$language = 'wp-block-ink' === $this->status ? __( 'Configure', 'wp-block-ink' ) : __( 'Set Custom', 'wp-block-ink' );
		?>
        <div class="card" style="text-align:center !important;">
			<?php echo WP_Block_Ink_Status::return_response_markup( $this->status ); ?>
        </div>
        <div class="card">
            <h3 class="card-title">Set Color Palette</h3>
            <p>Set the palette colors and disable the user's custom color picker.</p>
            <a href="<?php echo esc_url( add_query_arg( array( 'autofocus[control]' => 'wp_block_ink_enabled' ), admin_url( 'customize.php' ) ) ); ?>"
               class="button button-primary button-hero"
               style="float:right;"><?php esc_attr_e( $language, 'wp-block-ink' ); ?>
            </a>
            <br/><br/><br/>
        </div>
        <?php if ( 'wp-block-ink' === $this->status ) { ?>
            <div class="card">
                <h3 class="card-title">See Visual Reference</h3>
                <p>Reference the color names and codes used in the Editor Blocks.</p>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wp-block-ink', 'tab' => 'style-guide' ), admin_url( 'themes.php' ) ) ); ?>"
                   class="button button-hero"
                   style="float:right;"><?php esc_attr_e( 'Style Guide', 'wp-block-ink' ); ?>
                </a>
                <br/><br/><br/>
            </div>
            <div class="card">
                <h3 class="card-title">Reference CSS Class Names</h3>
                <p>See the CSS classes added to the theme to use elsewhere in your designs.</p>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wp-block-ink', 'tab' => 'css' ), admin_url( 'themes.php' ) ) ); ?>"
                   class="button button-hero"
                   style="float:right;"><?php esc_attr_e( 'CSS Classes', 'wp-block-ink' ); ?>
                </a>
                <br/><br/><br/>
            </div>
        <?php
        }

	}

	/**
	 * Style Guide Tab (shows color swatches)
	 */
	function style_guide() {
		global $_wp_theme_features;
		?>
        <h3 class="wp-people-group"><?php echo __( 'Brand Palette', 'wp-block-ink' ); ?></h3>
        <ul class="wp-people-group " id="wp-people-group-project-leaders">
			<?php foreach ( $_wp_theme_features['editor-color-palette'][0] as $color ) { ?>
                <li class="wp-person">
                    <strong class="web" style="font-weight:700!important;">
                        <div class="gravatar"
                             style="display:inline-block;width:72px;height:72px;background-color:<?php esc_attr_e( $color['color'], 'wp-block-ink' ); ?>;border-radius:50%;-webkit-border-radius:50%;-moz-border-radius;50%; border: 3px double #ccc;"
                        ></div>
						<?php esc_attr_e( $color['name'], 'wp-block-ink' ); ?></strong>
                    <span class="title"><code><?php esc_attr_e( $color['color'], 'wp-block-ink' ); ?></code></span>
                </li>
			<?php } ?>

        </ul>
		<?php
	}

	/**
	 * CSS Reference Tab
	 */
	function css_ref() {
		global $_wp_theme_features;

		if ( ! empty( $_wp_theme_features['editor-color-palette'][0] ) && ! empty( get_option( 'wp_block_ink_color_count' ) ) ) {
			?>
            <p><?php echo __( "This is an uncompressed copy of the CSS automatically added to your website. These helper CSS classes can be used anywhere in your Theme as well.", 'wp-block-ink' ); ?></p>
            <pre>
                <code class="language-css">
                <?php foreach ( $_wp_theme_features['editor-color-palette'][0] as $color ) { ?>

                    /**
                    * <?php esc_attr_e( $color['name'], 'wp-block-ink' ); ?>

                    */
                    .has-<?php esc_attr_e( $color['slug'], 'wp-block-ink' ); ?>-color {
                      color: <?php esc_attr_e( $color['color'], 'wp-block-ink' ); ?>;
                    }
                    .has-<?php esc_attr_e( $color['slug'], 'wp-block-ink' ); ?>-background-color {
                      background-color: <?php esc_attr_e( $color['color'], 'wp-block-ink' ); ?>;
                    }

                <?php } ?>
                </code>
            </pre>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Helper for admin page tab url.
	 *
	 * @param string $tab
	 */
	function tab_url( $tab = 'status' ) {
		echo admin_url( 'themes.php?page=wp-block-ink&tab=' . $tab );
	}
}
