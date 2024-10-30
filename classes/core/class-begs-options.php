<?php

namespace BEGS\Block_Editor_Gallery_Slider;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEGS_Options' ) ) {

	class BEGS_Options extends Block_Editor_Gallery_Slider {

		public function __construct() {
			parent::__construct();
		}

		public function add_plugin_options() {
			$types = array_merge(
				array(
					'post' => 'post',
					'page' => 'page',
				),
				get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'names',
					'and'
				)
			);

			add_settings_section(
				'begs_section',
				'',
				function() {
					?>
						<h2 class="title" id="block-editor-search-replace">
							Block Editor Gallery Slider
						</h2>
						<p>
							For the price of a cup of coffee per month, you can <a href="https://patreon.com/krasenslavov" target="_blank"><strong>help and support me on Patreon</strong></a> in continuing to develop and maintain all of my free WordPress plugins, every little bit helps and is greatly appreciated!
						</p>
						<div class="begs-notice">
							<p>
								<strong>Please rate us</strong>
								<a href="<?php echo esc_url( $this->settings['plugin_wporgrate'] ); ?>" target="_blank"><img src="<?php echo esc_url( $this->settings['plugin_url'] ); ?>assets/img/rate.png" alt="Rate us @ WordPress.org" /></a>
							</p>
							<p>
								<strong>Having issues?</strong>
								<a href="<?php echo esc_url( $this->settings['plugin_wporgurl'] ); ?>" target="_blank">Create a Support Ticket</a>
							</p>
							<p>
								<strong>Developed by</strong>
								<a href="https://krasenslavov.com/" target="_blank">Krasen Slavov @ Developry</a>
							</p>
						</div>
						<hr />
						<ul>
							<li>&bullet; Convert your built-in Block and Classic editor galleries into fancy sliders.</li>
							<li>&bullet; Add gallery sliders for posts and pages (<em>these options will only turn on/off the meta box from the admin area, front-end galleries will still have the sliders applied to them</em>).</li>
							<li>&bullet; Support all available CPTs and WooCommerce Products (<em>if they are enabled</em>).</li>
							<li>&bullet; Support for older galleries in WordPress 5.8, as well as 5.9 and up.</li>
						</ul>
					<?php
				},
				'media'
			);

			register_setting(
				'media',
				'begs_disable_all_galleries',
				function( $input ) {
					if ( is_array( $input ) ) {
						$input = array_map( 'sanitize_text_field', $input );
					}
					return $input;
				}
			);

			add_settings_field(
				'begs_disable_all_galleries',
				'Gallery Sliders',
				function() {
					$options = get_option( 'begs_disable_all_galleries ' );
					?>
						<div class="begs">
							<div class="begs-disable-all-galleries">
								<p>
									<label for="begs_disable_all_galleries[selected]">
										<input type="checkbox" id="begs_disable_all_galleries[selected]" name="begs_disable_all_galleries[selected]" onclick="this.value = !(this.value != 'false' );" value="<?php echo ( ! empty( $options['selected'] ) ) ? esc_attr( $options['selected'] ) : 'false'; ?>" <?php echo ( ! empty( $options['selected'] ) && esc_attr( $options['selected'] ) === 'true' ) ? 'checked' : ''; ?> />
										<strong class="text-fail">Yes, I want to <em>DISABLE</em> all the gallery sliders on the front-end.</strong>
									</label>
								</p>
							</div>
						</div>
					<?php
				},
				'media',
				'begs_section'
			);

			register_setting(
				'media',
				'begs_editor_support',
				function( $input ) {
					if ( is_array( $input ) ) {
						$input = array_map( 'sanitize_text_field', $input );
					}
					return $input;
				}
			);

			add_settings_field(
				'begs_editor_support',
				'Supported Editors',
				function() {
					$options = get_option( 'begs_editor_support ' );
					?>
						<div class="begs">
							<div class="begs-editors">
								<p>
									<label for="begs_editor_support[block_editor]">
										<input type="checkbox" id="begs_editor_support[block_editor]" name="begs_editor_support[block_editor]" onclick="this.value = !(this.value != 'false');" value="<?php echo ( ! empty( $options['block_editor'] ) ) ? esc_attr( $options['block_editor'] ) : 'false'; ?>" <?php echo ( ! empty( $options['block_editor'] ) && esc_attr( $options['block_editor'] ) === 'true' ) ? 'checked' : ''; ?> />
										Block Editor (Gutenberg)
									</label>
								</p>
								<p>
									<label for="begs_editor_support[classic]">
										<input type="checkbox" id="begs_editor_support[classic]" name="begs_editor_support[classic]" onclick="this.value = !(this.value != 'false');" value="<?php echo ( ! empty( $options['classic'] ) ) ? esc_attr( $options['classic'] ) : 'false'; ?>" <?php echo ( ! empty( $options['classic'] ) && esc_attr( $options['classic'] ) === 'true' ) ? 'checked' : ''; ?> />
										Classic Editor
									</label>
								</p>
							</div>
						</div>
					<?php
				},
				'media',
				'begs_section'
			);

			register_setting(
				'media',
				'begs_post_types',
				function( $input ) {
					if ( is_array( $input ) ) {
						$input = array_map( 'sanitize_text_field', $input );
					}
					return $input;
				}
			);

			add_settings_field(
				'begs_post_types',
				'Supported Types',
				function( $types ) {
					foreach ( $types as $type ) {
						$options = get_option( 'begs_post_types ' );

						switch ( $type ) {
							case 'post':
								$label = 'Posts';
								break;
							case 'page':
								$label = 'Pages';
								break;
							case 'product':
								$label = 'WooCommerce Products';
								break;
							default:
								$label = 'Custom Post Type (<em>' . esc_attr( $type ) . '</em>)';
								break;
						}
						?>
							<div class="begs">
								<div class="begs-post-types">
									<p>
										<label for="begs_post_types[<?php echo esc_attr( $type ); ?>]">
											<input type="checkbox" id="begs_post_types[<?php echo esc_attr( $type ); ?>]" name="begs_post_types[<?php echo esc_attr( $type ); ?>]" onclick="this.value = !(this.value != 'false' );" value="<?php echo ( ! empty( $options[ $type ] ) ) ? esc_attr( $options[ $type ] ) : 'false'; ?>" <?php echo ( ! empty( $options[ $type ] ) && esc_attr( $options[ $type ] ) === 'true' ) ? 'checked' : ''; ?> />
											<?php echo $label; ?>
										</label>
									</p>
								</div>
							</div>
						<?php
					}
				},
				'media',
				'begs_section',
				$types
			);
		}
	}
}
