<?php

namespace BEGS\Block_Editor_Gallery_Slider;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BEGS_Gallery_Slider' ) ) {

	class BEGS_Gallery_Slider extends Block_Editor_Gallery_Slider {

		public function __construct() {
			parent::__construct();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'wp_ajax_block_editor_gallery_slider_options', array( $this, 'block_editor_gallery_slider_options' ) );
			add_action( 'wp_ajax_classic_gallery_slider_options', array( $this, 'classic_gallery_slider_options' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'localize_admin_control_options' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_front_control_options' ) );
		}

		public function localize_admin_control_options() {
			global $post;

			if ( '' === $post ) {
				return false;
			}

			$post_classic_meta = array(
				'is_gallery_slider_enabled' => 0,
				'container_class_name'      => '',
			);

			if ( null !== $post && get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' ) ) {
				$post_classic_meta = get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' )[0];
				// TinyMCE (visual-aid, just for demonstation)
				if ( 1 === $post_classic_meta['is_gallery_slider_enabled'] ) {
					add_filter(
						'mce_css',
						function( $mce_css ) {
							$mce_css .= ',' . $this->settings['plugin_url'] . 'assets/css/block-editor-gallery-slider-tinymce.css';
							return $mce_css;
						}
					);
				}
			}

			wp_localize_script(
				'block-editor-gallery-slider',
				'begs_options',
				array(
					'post_classic_gallery_slider_enabled' => $post_classic_meta['is_gallery_slider_enabled'],
					'post_classic_gallery_slider_container_wrap_class' => $post_classic_meta['container_class_name'],
				)
			);
		}

		public function localize_front_control_options() {
			global $post;

			$post_classic_meta = array(
				'is_gallery_slider_enabled' => 0,
				'container_class_name'      => '',
			);

			if ( get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' ) ) {
				$post_classic_meta = get_post_meta( $post->ID, 'begs_classic_gallery_slider_options' )[0];
			}

			if ( get_option( 'begs_disable_all_galleries' ) ) {
				$option_disable_all_galleries = get_option( 'begs_disable_all_galleries' )['selected'];
			} else {
				$option_disable_all_galleries = false;
			}

			wp_localize_script(
				'block-editor-gallery-slider-front',
				'begs_options',
				array(
					'option_disable_all_galleries'        => $option_disable_all_galleries,
					'post_classic_gallery_slider_enabled' => $post_classic_meta['is_gallery_slider_enabled'],
					'post_classic_gallery_slider_container_wrap_class' => $post_classic_meta['container_class_name'],
				)
			);
		}

		public function classic_gallery_slider_options() {
			$current_post_id           = sanitize_text_field( $_REQUEST['current_post_id'] );
			$current_post_type         = sanitize_text_field( $_REQUEST['current_post_type'] );
			$is_classic_editor         = sanitize_text_field( $_REQUEST['is_classic_editor'] );
			$is_gallery_slider_enabled = sanitize_text_field( $_REQUEST['is_gallery_slider_enabled'] );
			$container_class_name      = sanitize_text_field( $_REQUEST['container_class_name'] );

			if ( get_post_meta( $current_post_id, 'begs_classic_gallery_slider_options' ) ) {
				update_post_meta(
					$current_post_id,
					'begs_classic_gallery_slider_options',
					array(
						'is_gallery_slider_enabled' => $is_gallery_slider_enabled,
						'container_class_name'      => $container_class_name,
					)
				);
			} else {
				add_post_meta(
					$current_post_id,
					'begs_classic_gallery_slider_options',
					array(
						'is_gallery_slider_enabled' => $is_gallery_slider_enabled,
						'container_class_name'      => $container_class_name,
					)
				);
			}

			echo json_encode(
				array(
					array(
						'id'      => 0,
						'message' => 'You have successfully saved the current post gallery slider options. <a href="' . esc_url( home_url( '?p=' . $current_post_id ) ) . '" target="_blank">Preview Changes</a>',
					),
				),
			);
			exit;
		}
	}

	$begs = new BEGS_Gallery_Slider();
	$begs->init();
}
