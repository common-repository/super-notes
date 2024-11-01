<?php
/**
 * Custom meta box for notes tags: configure rules about owners for notes of this type
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rules about owners
 */
class WDPSN_OwnersMetaBox extends WDPSN_MetaBoxBuilder {

	/**
	 * Initialize meta box
	 */
	public function configure_metabox() {

		$this->metabox_key   = 'wdpsn_note_types_owners';
		$this->metabox_title = esc_html( __( 'Owners - who can manage notes of this type?', 'super-notes' ) );

		self::$js_localize = array_merge(
			self::$js_localize,
			array(
				'owners_no_one'   => wp_kses( __( 'No-one will be able to add, edit and remove notes of this type.', 'super-notes' ), array( 'span' => array() ) ),
				'owners_one'      => wp_kses( __( '<span>1 user</span> will be able to add, edit and remove notes of this type.', 'super-notes' ), array( 'span' => array() ) ),

				/* Translators: Number of users with availability granted. */
				'owners_multiple' => wp_kses( __( '<span>%d users</span> will be able to add, edit and remove notes of this type.', 'super-notes' ), array( 'span' => array() ) ),
			)
		);
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
					'field'       => 'can_everyone',
					'title'       => esc_html( __( 'Everyone can?', 'super-notes' ) ),
					'description' => esc_html( __( 'Can everyone add, edit and remove notes of this type?', 'super-notes' ) ),
					'type'        => 'yes-no',
					'default'     => 'yes',
					'labels'      => array(
						'yes' => esc_html( __( 'Yes, everyone can add, edit and remove notes of this type', 'super-notes' ) ),
						'no'  => esc_html( __( 'No, not everyone can add, edit and remove notes of this type', 'super-notes' ) ),
					),
				),
				array(
					'field'       => 'who_can',
					'title'       => esc_html( __( 'So who can?', 'super-notes' ) ),
					'description' => esc_html( __( 'Define custom rules about who can add, edit and remove notes of this type.', 'super-notes' ) ),
					'type'        => 'rules',
					'data-set'    => 'owners',
					'labels'      => array(
						'allow-if'               => esc_html( __( 'Allow, if:', 'super-notes' ) ),
						'additional-description' => true,
					),
					'dependency'  => array(
						'field'    => 'can_everyone',
						'operator' => '==',
						'value'    => 'no',
					),
				),
			)
		);
	}
}
