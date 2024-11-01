<?php
/**
 * Custom meta box for notes tags: configure rules about location for notes of this type
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for location rules
 */
class WDPSN_LocationMetaBox extends WDPSN_MetaBoxBuilder {

	/**
	 * Initialize meta box
	 */
	public function configure_metabox() {

		$this->metabox_key   = 'wdpsn_note_types_location';
		$this->metabox_title = esc_html( __( 'Location - where notes of this type should be visible?', 'super-notes' ) );
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
					'field'       => 'everywhere',
					'title'       => esc_html( __( 'Everywhere where possible?', 'super-notes' ) ),
					'description' => esc_html( __( 'Should notes of this type be visible everywhere where possible?', 'super-notes' ) ),
					'type'        => 'yes-no',
					'default'     => 'yes',
					'labels'      => array(
						'yes' => esc_html( __( 'Yes, notes of this type should be visible everywhere where possible', 'super-notes' ) ),
						'no'  => esc_html( __( 'No, I want to set custom rules for that', 'super-notes' ) ),
					),
				),
				array(
					'field'       => 'custom_rules',
					'title'       => esc_html( __( 'Custom rules', 'super-notes' ) ),
					'description' => esc_html( __( 'Define custom rules about where notes of this type can be displayed.', 'super-notes' ) ),
					'type'        => 'rules',
					'data-set'    => 'locations',
					'labels'      => array(
						'allow-if' => esc_html( __( 'Show, if:', 'super-notes' ) ),
					),
					'dependency'  => array(
						'field'    => 'everywhere',
						'operator' => '==',
						'value'    => 'no',
					),
				),
			)
		);
	}
}
