<?php

namespace BEGS\Block_Editor_Gallery_Slider;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEGS_Init' ) ) {

	class BEGS_Init extends Block_Editor_Gallery_Slider {

		public function __construct() {
			parent::__construct();

			$this->opt = new BEGS_Options;
		}

		public function init() {
			add_action( 'activated_plugin', array( $this, 'activate_plugin' ) );
			add_action( 'deactivated_plugin', array( $this, 'deactivate_plugin' ) );
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			// Rating notices
			add_action( 'admin_notices', array( $this, 'rating_notice_display' ) );
			add_action( 'admin_init', array( $this, 'rating_notice_dismiss' ) );
			// Front-end
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_front_plugin_urls' ) );
			// Back-end
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'localize_admin_plugin_urls' ) );
			// Back-end options
			add_action( 'admin_init', array( $this, 'add_plugin_links' ) );
			add_action( 'admin_init', array( $this->opt, 'add_plugin_options' ) );
			// Block editor
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_scripts' ) );
			add_filter( 'render_block', array( $this, 'add_gallery_slider_container_size' ), 10, 2 );
			// Classic editor
			add_filter( 'add_meta_boxes', array( $this, 'add_classic_editor_support' ) );
		}

		public function activate_plugin( $plugin ) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->active_block_editor_gallery_slider();
			}
		}

		public function deactivate_plugin( $plugin ) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->deactive_block_editor_gallery_slider();
			}
		}

		public function add_gallery_slider_container_size( $block_content, $block ) {
			if ( isset( $block['blockName'] ) && 'core/gallery' === $block['blockName'] ) {

				$defaults = array( 'blockClassName' => '' );
				$args     = wp_parse_args( $block['attrs'], $defaults );

				$html = str_replace(
					'<figure class="wp-block-gallery',
					'<figure class="wp-block-gallery ' . esc_attr( $args['blockClassName'] ) . ' ',
					$block_content
				);
				return $html;
			}
			return $block_content;
		}

		public function enqueue_block_editor_scripts() {
			if ( true === $this->settings['dev_mode'] ) {

				wp_register_script(
					'block-editor-gallery-slider-gutenberg',
					$this->settings['plugin_url'] . 'assets/js/block-editor-gallery-slider-gutenberg.js',
					array( 'jquery', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider-gutenberg',
					$this->settings['plugin_url'] . 'assets/css/block-editor-gallery-slider-gutenberg.css',
					array(),
					'1.0',
					'all'
				);
			} else {
				wp_register_script(
					'block-editor-gallery-slider-gutenberg',
					$this->settings['plugin_url'] . 'assets/build/js/block-editor-gallery-slider-gutenberg.min.js',
					array( 'jquery', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider-gutenberg',
					$this->settings['plugin_url'] . 'assets/build/css/block-editor-gallery-slider-gutenberg.min.css',
					array(),
					'1.0',
					'all'
				);
			}

			wp_enqueue_script( 'block-editor-gallery-slider-gutenberg' );
			wp_enqueue_style( 'block-editor-gallery-slider-gutenberg' );
		}

		public function enqueue_admin_scripts() {
			if ( true === $this->settings['dev_mode'] ) {

				wp_register_script(
					'block-editor-gallery-slider',
					$this->settings['plugin_url'] . 'assets/js/block-editor-gallery-slider-init.js',
					array( 'jquery' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider',
					$this->settings['plugin_url'] . 'assets/css/block-editor-gallery-slider.css',
					array(),
					'1.0',
					'all'
				);
			} else {

				wp_register_script(
					'block-editor-gallery-slider',
					$this->settings['plugin_url'] . 'assets/build/js/block-editor-gallery-slider.min.js',
					array( 'jquery' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider',
					$this->settings['plugin_url'] . 'assets/build/css/block-editor-gallery-slider.min.css',
					array(),
					'1.0',
					'all'
				);
			}

			wp_enqueue_script( 'block-editor-gallery-slider' );
			wp_enqueue_style( 'block-editor-gallery-slider' );
		}

		public function enqueue_front_scripts() {
			if ( true === $this->settings['dev_mode'] ) {

				wp_register_script(
					'block-editor-gallery-slider-front',
					$this->settings['plugin_url'] . 'assets/js/front/block-editor-gallery-slider-init.js',
					array( 'jquery' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider-front',
					$this->settings['plugin_url'] . 'assets/css/block-editor-gallery-slider-front.css',
					array(),
					'1.0',
					'all'
				);
			} else {

				wp_register_script(
					'block-editor-gallery-slider-front',
					$this->settings['plugin_url'] . 'assets/build/js/block-editor-gallery-slider-front.min.js',
					array( 'jquery' ),
					'1.0',
					true
				);

				wp_register_style(
					'block-editor-gallery-slider-front',
					$this->settings['plugin_url'] . 'assets/build/css/block-editor-gallery-slider-front.min.css',
					array(),
					'1.0',
					'all'
				);
			}

			// Disable all slider galleries on the front-end if settings is selected.
			if ( ! get_option( 'begs_disable_all_galleries' ) ) {
				wp_enqueue_script( 'block-editor-gallery-slider-front' );
				wp_enqueue_style( 'block-editor-gallery-slider-front' );
			}
		}

		public function localize_admin_plugin_urls() {
			wp_localize_script(
				'block-editor-gallery-slider',
				'begs',
				array(
					'plugin_url' => $this->settings['plugin_url'],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		public function localize_front_plugin_urls() {
			wp_localize_script(
				'block-editor-gallery-slider-front',
				'begs',
				array(
					'plugin_url' => $this->settings['plugin_url'],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		public function add_plugin_links() {
			add_action( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );
		}

		public function add_action_links( $links, $file_path ) {
			if ( $file_path === $this->settings['plugin_basename'] ) {
				$links['settings'] = '<a href="' . esc_url( admin_url( 'options-media.php#block-editor-gallery-slider' ) ) . '">Settings</a>';
				return array_reverse( $links );
			}
			return $links;
		}

		public function add_meta_links( $links, $file_path ) {
			if ( $file_path === $this->settings['plugin_basename'] ) {
				$links['docmentation'] = '<a href="' . esc_url( $this->settings['plugin_docurl'] ) . '" target="_blank">Documentation</a>';
			}
			return $links;
		}

		function add_classic_editor_support() {
			global $post;

			$post_types                    = get_option( 'begs_post_types' );
			$editor_support                = get_option( 'begs_editor_support' );
			$classic_editor_plugin_default = get_post_type( 'classic-editor-remember' );
			$enabled_screens               = array();

			// Don't want to add the meta box for Block Editor,
			// if both Block and Classic editors are enabled.
			// We have duplicate meta boxes it is already loaded
			// via assets/js/block-editor-search-replace.mjs
			if ( ! isset( $_GET['classic-editor'] ) && isset( $_GET['classic-editor__forget'] ) ) {
				return false;
			}

			if ( ! $this->is_classic_editor_active() && 'classic-editor' !== $classic_editor_plugin_default ) {
				return false;
			}

			if ( ! is_array( $post_types ) ) {
				$post_types = array();
			}

			if ( ! is_array( $editor_support ) ) {
				$editor_support = array();
			}

			// Create an array with enabled screens to show the Navigato Controls.
			// Available for Posts, Pages, WooCommerce Products & Custom Post Types.
			foreach ( $post_types as $post_type => $enabled ) {
				array_push( $enabled_screens, $post_type );
			}

			if ( key_exists( 'classic', $editor_support )
				&& true === $editor_support['classic']
				&& key_exists( $post->post_type, $post_types )
				&& true === $post_types[ $post->post_type ] ) {

					add_meta_box(
						'classic_gallery_slider_metabox',
						'Gallery Slider Options',
						function() {
							global $post;
							if ( get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' ) ) {
								$post_classic_meta = get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' )[0];
							}
							if ( ! isset( $post_classic_meta ) ) {
								$post_classic_meta = array(
									'is_gallery_slider_enabled' => 0,
									'container_class_name' => '',
								);
							}
							?>
							<div class="begs">
								<div class="begs-ui">
									<p>
										Turn you Classic Editor image galleries into sliders. If you select to enable this feature it will be applied to all the galleries on the current page/post.
									</p>
									<form name="begs-block-editor" id="begs-block-editor" type="post">
										<input type="hidden" name="begs-current-post-type" id="begs-current-post-type" value="<?php echo esc_attr( $post->post_type ); ?>">
										<input type="hidden" name="begs-current-post-id" id="begs-current-post-id" value="<?php echo esc_attr( $post->ID ); ?>">
										<input type="hidden" name="begs-classic-editor" id="begs-classic-editor" value="1">
										<p>
											<label for="begs-enable-gallery-slider">
												<input type="checkbox" name="begs-enable-gallery-slider" id="begs-enable-gallery-slider" class="begs-enable-gallery-slider" <?php echo ( 1 === $post_classic_meta['is_gallery_slider_enabled'] ) ? 'checked' : ''; ?> />
												<span>Enable gallery slider for the post/page.</span>
											</label>
										</p>
										<p>
											<label for="begs-select-gallery-slider-container">
												<strong>Select container width<sup>*</sup></strong>
												<select name="begs-select-gallery-slider-container" id="begs-select-gallery-slider-container" class="begs-select-gallery-slider-container">
												<option value="" <?php echo ( '' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>default</option>
													<option value="begs-classic-gallery-wrap" <?php echo ( 'begs-classic-gallery-wrap' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>100% / full-width</option>
													<option value="begs-classic-gallery-wrap-1680" <?php echo ( 'begs-classic-gallery-wrap-1680' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>1680px</option>
													<option value="begs-classic-gallery-wrap-1200" <?php echo ( 'begs-classic-gallery-wrap-1200' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>1200px</option>
													<option value="begs-classic-gallery-wrap-1024" <?php echo ( 'begs-classic-gallery-wrap-1024' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>1024px</option>
													<option value="begs-classic-gallery-wrap-992" <?php echo ( 'begs-classic-gallery-wrap-992' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>992px</option>
													<option value="begs-classic-gallery-wrap-768" <?php echo ( 'begs-classic-gallery-wrap-768' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>768px</option>
													<option value="begs-classic-gallery-wrap-540" <?php echo ( 'begs-classic-gallery-wrap-540' === $post_classic_meta['container_class_name'] ) ? 'selected' : ''; ?>>540px</option>
												</select>
											</label>
										</p>
										<p>
											<button name="begs-classic-gallery-slider" class="button button-primary begs-classic-gallery-slider-button">
												<i class="dashicons dashicons-saved"></i>
												Save
											</button>
										</p>
										<div class="begs-gallery-slider-opts-output d-none"></div>
										<p>
											<small><sup>*</sup> The gallery slider container width is relative to the parent container and it will always fit the parent if its size is less than the selected one.</small>
										</p>
									</form>
								</div>
							</div>
							<?php
						},
						$enabled_screens,
						'side',
						'default'
					);
			}
		}

		// Add temporary plugin options.
		public function active_block_editor_gallery_slider() {
			// Activate plugin for the first time add default permanent options.
			if ( get_option( 'block_editor_gallery_slider' ) === false ) {
				add_option( 'block_editor_gallery_slider', 1 );
				add_option(
					'begs_editor_support',
					array(
						'block_editor' => 'true',
						'classic'      => 'true',
					)
				);
				add_option(
					'begs_post_types',
					array(
						'page' => 'true',
						'post' => 'true',
					)
				);
			}
		}

		// Remove temporary plugin options.
		public function deactive_block_editor_gallery_slider() {
			if ( get_option( 'begs_rating_notice' ) ) {
				delete_option( 'begs_rating_notice' );
			}
		}

		private function is_classic_editor_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
				return true;
			}
			return false;
		}
	}

	$begs = new BEGS_Init();
	$begs->init();
}
