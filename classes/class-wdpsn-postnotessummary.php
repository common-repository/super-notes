<?php
/**
 * Display notes holder or notes summary for single post
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', array( 'WDPSN_PostNotesSummary', 'register_meta_box' ) );
add_action( 'wp_ajax_wdpsn_delete_note_through_post_notes_summary', array( 'WDPSN_PostNotesSummary', 'delete_note_through_post_notes_summary' ) );
add_action( 'wp_ajax_wdpsn_refresh_post_notes_summary', array( 'WDPSN_PostNotesSummary', 'refresh_post_notes_summary' ) );
add_action( 'wp_ajax_wdpsn_update_post_notes_summary_mode', array( 'WDPSN_PostNotesSummary', 'update_post_notes_summary_mode' ) );

/**
 * Post notes summary class
 */
abstract class WDPSN_PostNotesSummary {

	/**
	 * Register post notes summary meta box
	 */
	public static function register_meta_box() {

		$post_types = get_post_types( array( 'show_ui' => true ) );

		add_meta_box( 'wdpsn-post-notes-summary', esc_html( __( 'Super Notes', 'super-notes' ) ), array( get_class(), 'render_content' ), $post_types, 'side' );

		foreach ( $post_types as $post_type ) {
			add_filter( 'postbox_classes_' . esc_attr( $post_type ) . '_wdpsn-post-notes-summary', array( get_class(), 'add_metabox_classes' ) );
		}
	}

	/**
	 * Add custom class to custom meta box
	 *
	 * @param array $classes Metabox classes.
	 */
	public static function add_metabox_classes( $classes ) {

		array_push( $classes, 'wdpsn-post-notes-summary' );
		return $classes;
	}

	/**
	 * Identify current location
	 */
	public static function get_current_location() {

		global $post;

		$post_id = false;
		if ( null !== $post && isset( $post->ID ) && null !== $post->ID ) {
			$post_id = $post->ID;
		}

		$result = array(
			'id'   => $post_id,
			'type' => false,
		);

		if ( false !== $result['id'] ) {
			$post_type = get_post_type( $result['id'] );
			if ( false !== $post_type && null !== $post_type ) {
				$result['type'] = $post_type;
			}
		}

		if ( false === $result['id'] && false === $result['type'] ) {
			$current_screen = get_current_screen();
			if ( 'add' === $current_screen->action && isset( $current_screen->post_type ) && ! empty( $current_screen->post_type ) ) {
				$result['type'] = 'new_item';
			}
		}

		return $result;
	}

	/**
	 * Get post notes summary mode
	 */
	public static function get_mode() {

		$mode = get_option( 'wdpsn_post_notes_summary_mode' );
		return false !== $mode ? $mode : 'long';
	}

	/**
	 * Render meta box content
	 *
	 * @param object $post Post data.
	 * @param object $metabox Metabox data.
	 * @param array  $location Location details.
	 * @param array  $messages Messages to display.
	 */
	public static function render_content( $post, $metabox, $location = false, $messages = array() ) {

		// Never used.
		unset( $post, $metabox );

		if ( false === $location ) {
			$location = self::get_current_location();
		}

		if ( false === $location['id'] && 'new_item' === $location['type'] ) {
			WDPSN_SingleNotes::display_placeholder( 'new_item' );
			return;
		}

		if ( false !== $location['id'] && false !== $location['type'] ) {

			?>
			<div class="wdpsn-post-notes-summary__wrapper" data-mode="<?php echo esc_attr( self::get_mode() ); ?>" data-element-id="<?php echo esc_attr( $location['id'] ); ?>" data-element-type="<?php echo esc_attr( $location['type'] ); ?>" data-availability-confirmed="no">

				<div class="wdpsn-post-notes-summary__more-less">
					<span class="wdpsn-post-notes-summary__more-less__dot"></span>
					<span class="wdpsn-post-notes-summary__more-less__dot"></span>
					<span class="wdpsn-post-notes-summary__more-less__dot"></span>
				</div>

				<div class="wdpsn-post-notes-summary-messages"></div>

				<div class="wdpsn-post-notes-summary-note-holder">
					<?php WDPSN_Holder::get_note_holder( $location ); ?>
				</div>

				<div class="wdpsn-post-notes-summary-preview">
					<?php WDPSN_SingleNotes::display_notes_preview( $location, WDPSN_Helper::get_popup_availability( $location['type'], $location['id'] ), false, $messages ); ?>
				</div>

			</div>
			<?php
			return;
		}

		WDPSN_SingleNotes::display_placeholder( 'no_location' );
	}

	/**
	 * Delete note
	 */
	public static function delete_note_through_post_notes_summary() {

		check_ajax_referer( 'delete-note-through-post-notes-summary', 'nonce' );

		$messages = array();
		if ( isset( $_POST['note'] ) && is_array( $_POST['note'] ) ) {

			$data = array(
				'element_type' => isset( $_POST['note']['element_type'] ) ? sanitize_text_field( wp_unslash( $_POST['note']['element_type'] ) ) : '',
				'element_id'   => isset( $_POST['note']['element_id'] ) ? sanitize_text_field( wp_unslash( $_POST['note']['element_id'] ) ) : '',
				'note_type_id' => isset( $_POST['note']['note_type_id'] ) ? sanitize_text_field( wp_unslash( $_POST['note']['note_type_id'] ) ) : '',
				'note_id'      => isset( $_POST['note']['note_id'] ) ? sanitize_text_field( wp_unslash( $_POST['note']['note_id'] ) ) : '',
			);

			$availability = WDPSN_Helper::get_popup_availability( $data['element_type'], $data['element_id'] );
			$note_type_id = isset( $data['note_type_id'] ) ? $data['note_type_id'] : false;

			if ( false !== $note_type_id && true === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) ) {
				if ( true === WDPSN_SingleNotes::delete( $data['note_id'] ) ) {
					$messages[] = array(
						'status'  => 'success',
						'content' => esc_html( __( 'Note successfuly deleted.', 'super-notes' ) ),
					);
				}
			}
		}

		if ( array() === $messages ) {
			$messages[] = array(
				'status'  => 'error',
				'content' => esc_html( __( 'Couldn\'t delete selected note.', 'super-notes' ) ),
			);
		}

		self::render_content(
			false,
			false,
			array(
				'type' => $data['element_type'],
				'id'   => $data['element_id'],
			),
			$messages
		);

		wp_die();
	}

	/**
	 * Refresh post notes summary preview
	 */
	public static function refresh_post_notes_summary() {

		check_ajax_referer( 'refresh-post-notes-summary', 'nonce' );

		$data = array(
			'element_type' => isset( $_POST['element_type'] ) ? sanitize_text_field( wp_unslash( $_POST['element_type'] ) ) : '',
			'element_id'   => isset( $_POST['element_id'] ) ? sanitize_text_field( wp_unslash( $_POST['element_id'] ) ) : '',
		);

		self::render_content(
			false,
			false,
			array(
				'type' => $data['element_type'],
				'id'   => $data['element_id'],
			)
		);

		wp_die();
	}

	/**
	 * Update post notes summary
	 */
	public static function update_post_notes_summary_mode() {

		check_ajax_referer( 'update-post-notes-summary-mode', 'nonce' );

		$new_mode = isset( $_POST['new_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['new_mode'] ) ) : '';

		if ( in_array( $new_mode, array( 'short', 'long' ), true ) ) {
			update_option( 'wdpsn_post_notes_summary_mode', esc_attr( $new_mode ) );
		}

		wp_die();
	}
}
