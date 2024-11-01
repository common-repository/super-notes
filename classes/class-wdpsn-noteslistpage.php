<?php
/**
 * Notes list page
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', array( 'WDPSN_NotesListPage', 'register_menu_item' ) );
add_action( 'wp_ajax_wdpsn_delete_notes_through_table', array( 'WDPSN_NotesListPage', 'delete_notes_through_table' ) );

/**
 * Create and render notes list page
 */
abstract class WDPSN_NotesListPage {

	/**
	 * Register menu item
	 */
	public static function register_menu_item() {

		$submenu_page = add_submenu_page(
			'wdpsn',
			esc_html( __( 'All Notes', 'super-notes' ) ),
			esc_html( __( 'All Notes', 'super-notes' ) ),
			'preview_own_wdpsn_notes',
			'wdpsn-all-notes',
			array( get_class(), 'render_list_page' )
		);

		require_once dirname( WDPSN_MAIN_FILE ) . '/classes/class-wdpsn-noteslisttable.php';
	}

	/**
	 * Load custom scripts for notes list page
	 */
	public static function load_scripts() {

		add_action( 'admin_enqueue_scripts', array( get_class(), 'enqueue_scripts' ) );
	}

	/**
	 * Render list page
	 */
	public static function render_list_page() {

		$table        = new WDPSN_NotesListTable();
		$current_page = str_replace( 'super-notes_page_', '', get_current_screen()->base );

		$table->prepare_items();

		?>
		<div class="wrap">

			<h1 class="wp-heading-inline"><?php echo esc_html( __( 'All Notes', 'super-notes' ) ); ?></h1>
			<hr class="wp-header-end" />

			<?php self::display_custom_messages(); ?>

			<form id="wdpsn-all-notes" method="get">

				<input type="hidden" name="page" value="<?php echo esc_attr( $current_page ); ?>" />
				<?php $table->display(); ?>

			</form>

		</div>
		<?php
	}

	/**
	 * Get array of all custom notes
	 */
	public static function get_all_notes() {

		$all_notes = get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'wdpsn_single_note',
			)
		);

		$note_types    = WDPSN_DataCache::get_note_types();
		$allowed_users = WDPSN_DataCache::get_allowed_users();
		$user_id       = get_current_user_id();
		$results       = array();
		$user          = wp_get_current_user();

		if ( false !== $all_notes && null !== $all_notes && is_array( $all_notes ) && array() !== $all_notes ) {

			foreach ( $all_notes as $single_note ) {

				$user_is_allowed = isset( $allowed_users[ esc_attr( $single_note->post_parent ) ] ) && in_array( $user_id, WDPSN_Helper::array_column( $allowed_users[ esc_attr( $single_note->post_parent ) ]['viewers'], 'id' ), true );
				$show_deleted    = ! isset( $allowed_users[ esc_attr( $single_note->post_parent ) ] ) && array_intersect( array( 'administrator' ), $user->roles );

				if ( $user_is_allowed || $show_deleted ) {

					$result = array(
						'ID'              => (int) $single_note->ID,
						'content'         => $single_note->post_content,
						'content_escaped' => wp_strip_all_tags( $single_note->post_content ),
						'note_type_id'    => $single_note->post_parent,
						'note_type'       => WDPSN_DataCache::get_note_type_title_by_id( $single_note->post_parent ),
						'added_by'        => WDPSN_Helper::get_user_name_by_id( $single_note->post_author ),
						'style'           => WDPSN_Helper::get_single_note_style( $note_types, $single_note->post_parent ),
					);

					$result             = array_merge( $result, WDPSN_SingleNotes::get_single_note_meta( $single_note->ID ) );
					$result['added_to'] = wp_kses(
						WDPSN_Helper::get_single_note_parent_name( $result['parent_type'], $result['parent_id'] ),
						array(
							'strong' => array(),
							'span'   => array(
								'class' => array(),
							),
						)
					);

					$results[] = $result;
					unset( $result );
				}
			}
		}

		unset( $all_notes, $single_note );
		wp_reset_postdata();

		return $results;
	}

	/**
	 * Get the number of notes displayed per page
	 */
	public static function notes_per_page() {

		return 20;
	}

	/**
	 * Delete single notes through notes list table
	 */
	public static function delete_notes_through_table() {

		check_ajax_referer( 'delete-notes-through-table', 'nonce' );

		if ( ! isset( $_POST['notes'] ) ) {
			wp_die();
		}

		$results = array();
		foreach ( wdpsn_sanitize_array( wp_unslash( $_POST['notes'] ) ) as $note ) {

			$note_id      = isset( $note['note_data'] ) && isset( $note['note_data']['note_id'] ) ? $note['note_data']['note_id'] : false;
			$note_type_id = isset( $note['note_data'] ) && isset( $note['note_data']['note_type_id'] ) ? $note['note_data']['note_type_id'] : false;

			if ( false === $note_id || false === $note_type_id || ! isset( $note['element_type'] ) || ! isset( $note['element_id'] ) ) {
				continue;
			}

			$availability         = WDPSN_Helper::get_popup_availability( $note['element_type'], $note['element_id'], false, false );
			$is_note_type_deleted = 'deleted' === WDPSN_Helper::get_single_note_style( WDPSN_DataCache::get_note_types(), $note_type_id );

			if ( false === $is_note_type_deleted && false === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) ) {
				$results[ $note_id ] = false;
			} else {
				$results[ $note_id ] = WDPSN_SingleNotes::delete( $note_id );
			}
		}

		$redirect_config = array(
			'paged'       => isset( $_POST['url_path'] ) && isset( $_POST['url_path']['paged'] ) ? sanitize_text_field( wp_unslash( $_POST['url_path']['paged'] ) ) : '',
			'http_host'   => isset( $_POST['url_path'] ) && isset( $_POST['url_path']['http_host'] ) ? sanitize_text_field( wp_unslash( $_POST['url_path']['http_host'] ) ) : '',
			'request_uri' => isset( $_POST['url_path'] ) && isset( $_POST['url_path']['request_uri'] ) ? sanitize_text_field( wp_unslash( $_POST['url_path']['request_uri'] ) ) : '',
		);

		echo wp_json_encode( array( 'redirect_url' => self::create_redirect_url( $results, $redirect_config ) ) );
		wp_die();
	}

	/**
	 * Create redirect URL
	 *
	 * @param array $data Deleted note data.
	 * @param array $config Redirect config.
	 */
	public static function create_redirect_url( $data, $config ) {

		$redirect_url     = '';
		$current_page_num = $config['paged'];
		$total_pages      = ceil( count( self::get_all_notes() ) / self::notes_per_page() );

		if ( $current_page_num > 1 && $current_page_num > $total_pages ) {
			$redirect_url = add_query_arg( 'paged', $total_pages, remove_query_arg( wp_removable_query_args(), set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $config['http_host'] . $config['request_uri'] ) ) );
		} else {
			$redirect_url = remove_query_arg( wp_removable_query_args(), set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $config['http_host'] . $config['request_uri'] ) );
		}

		$redirect_url = remove_query_arg( 'wdpsn-note-updated', $redirect_url );
		$redirect_url = add_query_arg( 'wdpsn-delete-notes-result', self::render_notes_deletion_query_arg( $data ), $redirect_url );

		return wp_nonce_url( $redirect_url, 'yes', 'wdpsn_note_deleted' );
	}

	/**
	 * Return array of values prepared to be displayed in URL
	 *
	 * @param array $data Deleted note data.
	 */
	public static function render_notes_deletion_query_arg( $data ) {

		return str_replace( '"', '', str_replace( '{', '', str_replace( '}', '', wp_json_encode( $data ) ) ) );
	}

	/**
	 * Display custom messages
	 */
	public static function display_custom_messages() {

		if ( ( ! isset( $_GET['wdpsn_note_deleted'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wdpsn_note_deleted'] ) ), 'yes' ) ) && ( ! isset( $_GET['amp;wdpsn_note_deleted'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['amp;wdpsn_note_deleted'] ) ), 'yes' ) ) ) {
			return;
		}

		if ( isset( $_GET['wdpsn-delete-notes-result'] ) || isset( $_GET['amp;wdpsn-delete-notes-result'] ) ) {

			$results = sanitize_text_field( wp_unslash( isset( $_GET['wdpsn-delete-notes-result'] ) ? $_GET['wdpsn-delete-notes-result'] : $_GET['amp;wdpsn-delete-notes-result'] ) );
			if ( ! empty( $results ) ) {

				$results         = explode( ',', $results );
				$removed_counter = 0;
				$errors          = array();

				foreach ( $results as $result ) {

					$result = explode( ':', $result );

					if ( 'true' === $result[1] ) {
						$removed_counter++;
					} else {
						$errors[] = $result[0];
					}
				}

				if ( $removed_counter > 0 ) {

					?>
					<div id="message" class="updated notice notice-success is-dismissible">

						<p><?php echo esc_html( $removed_counter . ' ' . ( 1 === $removed_counter ? __( 'note removed correctly.', 'super-notes' ) : __( 'notes removed correctly.', 'super-notes' ) ) ); ?></p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text"><?php echo esc_html( __( 'Dismiss this notice.', 'super-notes' ) ); ?></span>
						</button>

					</div>
					<?php
				}

				if ( count( $errors ) > 0 ) {

					?>
					<div id="message" class="notice notice-error is-dismissible">

						<p><?php echo esc_html( __( 'Couldn\'t remove following notes:', 'super-notes' ) ) . ' ' . esc_html( implode( ', ', $errors ) ); ?></p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text"><?php echo esc_html( __( 'Dismiss this notice.', 'super-notes' ) ); ?></span>
						</button>

					</div>
					<?php
				}
			}
		}
	}
}
