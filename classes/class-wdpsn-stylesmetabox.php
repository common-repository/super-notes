<?php
/**
 * Custom meta box for notes tags: configure styles for notes of this type
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for note styles
 */
class WDPSN_StylesMetaBox extends WDPSN_MetaBoxBuilder {

	/**
	 * Initialize meta box
	 */
	public function configure_metabox() {

		$this->metabox_key   = 'wdpsn_note_types_styles';
		$this->metabox_title = esc_html( __( 'Style - what color notes of this type should use?', 'super-notes' ) );
	}

	/**
	 * Add custom meta box
	 */
	public function add_metabox() {

		$this->configure_metabox();
		add_meta_box(
			$this->metabox_key,
			$this->metabox_title,
			array( $this, 'display_metabox' ),
			'wdpsn_note_types'
		);
	}

	/**
	 * Save updated meta box
	 *
	 * @param integer $post_id Updated post ID.
	 */
	public function update_metabox( $post_id ) {

		if ( isset( $_POST['wdpsn_nonce'] ) && false !== wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wdpsn_nonce'] ) ), 'wdpsn_note_types' ) ) {

			$this->configure_metabox();
			if ( isset( $_POST[ $this->metabox_key ] ) ) {

				update_post_meta(
					$post_id,
					$this->metabox_key,
					wp_json_encode( $this->validate_insertion( wdpsn_sanitize_array( wp_unslash( $_POST[ $this->metabox_key ] ) ) ) )
				);
			}
		}
	}

	/**
	 * Display meta box
	 *
	 * @param object $post Current post.
	 */
	public function display_metabox( $post ) {

		$values = get_post_meta( $post->ID, $this->metabox_key, true );

		$this->render_fields(
			$this->metabox_key,
			$values,
			array(
				array(
					'field'       => 'color_scheme',
					'title'       => esc_html( __( 'Colors', 'super-notes' ) ),
					'description' => esc_html( __( 'Choose color scheme for notes of this type.', 'super-notes' ) ),
					'type'        => 'select',
					'default'     => 'alert',
					'values'      => array(
						'alert'   => esc_html( __( 'Alert', 'super-notes' ) ),
						'error'   => esc_html( __( 'Error', 'super-notes' ) ),
						'info'    => esc_html( __( 'Info', 'super-notes' ) ),
						'success' => esc_html( __( 'Success', 'super-notes' ) ),
						'custom'  => esc_html( __( 'Custom', 'super-notes' ) ),
					),
				),
				array(
					'field'       => 'colors',
					'title'       => esc_html( __( 'Custom color', 'super-notes' ) ),
					'description' => esc_html( __( 'Choose the custom color for notes of this type.', 'super-notes' ) ),
					'type'        => 'note_type_colors',
					'dependency'  => array(
						'field'    => 'color_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			)
		);
	}
}
