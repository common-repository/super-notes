<?php
/**
 * Holder keeps the place for quick notes preview and popup opener
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', array( 'WDPSN_Holder', 'init' ) );
add_action( 'wp_ajax_wdpsn_get_note_holders', array( 'WDPSN_Holder', 'get_note_holders' ) );
add_action( 'admin_bar_menu', array( 'WDPSN_Holder', 'custom_toolbar_element' ), 999 );

/**
 * Holder class
 */
abstract class WDPSN_Holder {

	/**
	 * Initialize class
	 */
	public static function init() {

		$hooks = array(
			array( 'posts', false, false, 2 ),
			array( 'pages', false, false, 2 ),
			array( 'plugins', false, false, 2 ),
			array( 'edit-category', 'category', 'term_category', 3 ),
			array( 'edit-post_tag', 'post_tag', 'term_tag', 3 ),
			array( 'users', false, false, 3 ),
			array( 'edit-comments', 'comments', 'comments', 2 ),
			array( 'edit-deprecated_log', 'deprecated_log', 'posts', 2 ),
			array( 'media', false, false, 2 ),
		);

		foreach ( $hooks as $hook ) {

			add_filter( 'manage_' . esc_attr( $hook[0] ) . '_columns', array( get_class(), 'column_heading' ), 10 );
			add_action( 'manage_' . esc_attr( false !== $hook[1] ? $hook[1] : $hook[0] ) . '_custom_column', array( get_class(), 'column_content_' . esc_attr( false !== $hook[2] ? $hook[2] : $hook[0] ) ), 10, esc_attr( $hook[3] ) );
		}
	}

	/**
	 * Add column heading
	 *
	 * @param array $columns Table columns.
	 */
	public static function column_heading( $columns ) {

		return array_merge( $columns, array( 'wdpsn-holder' => esc_html( __( 'Notes', 'super-notes' ) ) ) );
	}

	/**
	 * Display column content
	 *
	 * @param integer $element_id Element ID.
	 * @param string  $element_type Element type.
	 * @param boolean $echo Display or return output.
	 */
	public static function get_column_content( $element_id, $element_type, $echo = true ) {

		$output = '<span class="wdpsn-holder-element-loader" data-element-type="' . esc_attr( $element_type ) . '" data-element-id="' . esc_attr( $element_id ) . '"></span>';

		if ( true === $echo ) {
			echo wp_kses(
				$output,
				array(
					'span' => array(
						'class'             => array(),
						'data-element-type' => array(),
						'data-element-id'   => array(),
					),
				)
			);
			return;
		} else {
			return( $output );
		}
	}

	/**
	 * Handle posts table custom column content
	 *
	 * @param string  $column_name Column name.
	 * @param integer $element_id Element ID.
	 */
	public static function column_content_posts( $column_name, $element_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			self::get_column_content( $element_id, 'post' );
		}
	}

	/**
	 * Handle pages table custom column content
	 *
	 * @param string  $column_name Column name.
	 * @param integer $element_id Element ID.
	 */
	public static function column_content_pages( $column_name, $element_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			self::get_column_content( $element_id, 'page' );
		}
	}

	/**
	 * Handle comments table custom column content
	 *
	 * @param string  $column_name Column name.
	 * @param integer $element_id Element ID.
	 */
	public static function column_content_comments( $column_name, $element_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			self::get_column_content( $element_id, 'comment' );
		}
	}

	/**
	 * Handle plugins table custom column content
	 *
	 * @param string  $column_name Column name.
	 * @param integer $plugin_slug Plugin slug.
	 */
	public static function column_content_plugins( $column_name, $plugin_slug ) {

		if ( 'wdpsn-holder' === $column_name ) {
			self::get_column_content( $plugin_slug, 'plugin' );
		}
	}

	/**
	 * Handle users table custom column content
	 *
	 * @param string  $content Column content.
	 * @param string  $column_name Column name.
	 * @param integer $user_id User ID.
	 */
	public static function column_content_users( $content, $column_name, $user_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			$content = self::get_column_content( $user_id, 'user', false );
		}
		return $content;
	}

	/**
	 * Handle users table custom column content
	 *
	 * @param string  $column_name Column name.
	 * @param integer $element_id Element ID.
	 */
	public static function column_content_media( $column_name, $element_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			self::get_column_content( $element_id, 'attachment' );
		}
	}

	/**
	 * Handle category terms table custom column content
	 *
	 * @param string  $content Column content.
	 * @param string  $column_name Column name.
	 * @param integer $term_id Term ID.
	 */
	public static function column_content_term_category( $content, $column_name, $term_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			$content = self::get_column_content( $term_id, 'category', false );
		}
		return $content;
	}

	/**
	 * Handle tags terms table custom column content
	 *
	 * @param string  $content Column content.
	 * @param string  $column_name Column name.
	 * @param integer $term_id Term ID.
	 */
	public static function column_content_term_tag( $content, $column_name, $term_id ) {

		if ( 'wdpsn-holder' === $column_name ) {
			$content = self::get_column_content( $term_id, 'post_tag', false );
		}
		return $content;
	}

	/**
	 * Add holder for global notes to admin bar
	 *
	 * @param object $wp_admin_bar Admin bar object.
	 */
	public static function custom_toolbar_element( $wp_admin_bar ) {
		$wp_admin_bar->add_node(
			array(
				'id'   => 'wdpsn-holder-element',
				'meta' => array(
					'class' => 'wdpsn-holder-element',
					'html'  => self::get_column_content( 'global', 'global', false ),
				),
			)
		);
	}

	/**
	 * Get note holder if possible
	 *
	 * @param mixed   $element_data Element data.
	 * @param boolean $is_ajax Whether is AJAX call or not.
	 */
	public static function get_note_holder( $element_data = '', $is_ajax = false ) {

		if ( ! is_array( $element_data ) || ! isset( $element_data['type'] ) || ! isset( $element_data['id'] ) || false === $element_data['type'] || false === $element_data['id'] ) {
			return;
		}

		$availability       = WDPSN_Helper::get_popup_availability( $element_data['type'], $element_data['id'] );
		$has_any_permission = WDPSN_Helper::has_permission( $availability, 'any' );
		$notes_count        = WDPSN_Helper::count_notes( $availability, $element_data );

		if ( 'global' === $element_data['type'] ) {

			if ( true === $is_ajax ) {
				return wp_json_encode(
					array(
						'has_any_permission' => $has_any_permission,
						'notes_count'        => $notes_count,
					)
				);
			}

			return;
		}

		if ( false === $has_any_permission ) {

			if ( true === $is_ajax ) {
				return '<span aria-hidden="true">—</span>';
			}

			WDPSN_SingleNotes::display_placeholder( 'unavailable' );
			return;
		}

		if ( true === $is_ajax ) {
			ob_start();
		}

		?>
		<div class="column-comments">
			<div class="post-com-count-wrapper wdpsn-holder-element" data-element-type="<?php echo esc_attr( $element_data['type'] ); ?>" data-element-id="<?php echo esc_attr( $element_data['id'] ); ?>">

				<span class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true"><?php echo esc_html( __( 'Show', 'super-notes' ) ); ?></span></span>

				<?php
				if ( $notes_count > 0 ) {
					?>
					<span class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true"><?php echo esc_html( $notes_count ); ?></span></span>
					<?php
				}
				?>

			</div>
		</div>
		<?php

		if ( true === $is_ajax ) {

			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}

	/**
	 * Get multiple note holders at once
	 */
	public static function get_note_holders() {

		check_ajax_referer( 'get-note-holders', 'nonce' );

		if ( ! isset( $_POST['elements'] ) || ! is_array( $_POST['elements'] ) ) {
			wp_die( 'error' );
		}

		$elements = wdpsn_sanitize_array( wp_unslash( $_POST['elements'] ) );
		$results  = array();

		foreach ( $elements as $element ) {

			$results[] = array(
				'type'   => $element['type'],
				'id'     => $element['id'],
				'holder' => self::get_note_holder( $element, true ),
			);
		}

		echo wp_json_encode( $results );
		wp_die();
	}
}
