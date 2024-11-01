<?php
/**
 * Custom meta box for notes tags: configure rules about viewers for notes of this type
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rules about viewers
 */
class WDPSN_ViewersMetaBox extends WDPSN_MetaBoxBuilder {

	/**
	 * Initialize meta box
	 */
	public function configure_metabox() {

		$this->metabox_key   = 'wdpsn_note_types_viewers';
		$this->metabox_title = esc_html( __( 'Viewers - who can view notes of this type?', 'super-notes' ) );

		self::$js_localize = array_merge(
			self::$js_localize,
			array(
				'viewers_no_one'   => wp_kses( __( 'No-one will be able to view notes of this type.', 'super-notes' ), array( 'span' => array() ) ),
				'viewers_one'      => wp_kses( __( '<span>1 user</span> will be able to view notes of this type.', 'super-notes' ), array( 'span' => array() ) ),

				/* Translators: Number of users with availability granted. */
				'viewers_multiple' => wp_kses( __( '<span>%d users</span> will be able to view notes of this type.', 'super-notes' ), array( 'span' => array() ) ),
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
					'description' => esc_html( __( 'Can everyone see notes of this type?', 'super-notes' ) ),
					'type'        => 'yes-no',
					'default'     => 'yes',
					'labels'      => array(
						'yes' => esc_html( __( 'Yes, everyone can see notes of this type', 'super-notes' ) ),
						'no'  => esc_html( __( 'No, not everyone can see notes of this type', 'super-notes' ) ),
					),
				),
				array(
					'field'                  => 'can_owners_only',
					'title'                  => esc_html( __( 'So maybe only Owners can?', 'super-notes' ) ),
					'description'            => esc_html( __( 'Owners are defined above. Should they be the only viewers of notes of this type?', 'super-notes' ) ),
					'additional-description' => wp_kses( __( '<strong>This mean:</strong> if one user (one of Owners defined above) add a note, then all other Owners can see this note (not only the user who created this note).', 'super-notes' ), array( 'strong' => array() ) ),
					'type'                   => 'yes-no',
					'default'                => 'yes',
					'labels'                 => array(
						'yes' => esc_html( __( 'Yes, only Owners can see notes of this type', 'super-notes' ) ),
						'no'  => esc_html( __( 'No, I want to define custom rules', 'super-notes' ) ),
					),
					'dependency'             => array(
						'field'    => 'can_everyone',
						'operator' => '==',
						'value'    => 'no',
					),
				),
				array(
					'field'       => 'who_can',
					'title'       => esc_html( __( 'Ok, so who can?', 'super-notes' ) ),
					'description' => esc_html( __( 'Define custom rules about who can see notes of this type.', 'super-notes' ) ),
					'type'        => 'rules',
					'data-set'    => 'viewers',
					'labels'      => array(
						'allow-if'               => esc_html( __( 'Allow, if:', 'super-notes' ) ),
						'additional-description' => true,
					),
					'dependency'  => array(
						'field'    => 'can_owners_only',
						'operator' => '==',
						'value'    => 'no',
					),
				),
			)
		);
	}
}
