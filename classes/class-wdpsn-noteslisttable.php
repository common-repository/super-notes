<?php
/**
 * Notes list table
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extend WP_List_Table class
 */
class WDPSN_NotesListTable extends WP_List_Table {

	/**
	 * Store availability data
	 *
	 * @var array Availability data.
	 */
	public static $availability = array();

	/**
	 * Set up a constructor that references the parent constructor
	 */
	public function __construct() {

		global $page;
		parent::__construct(
			array(
				'singular' => 'wdpsn_single_note',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Render default content result for columns
	 *
	 * @param array  $item Rendered item.
	 * @param string $column_name Column name.
	 */
	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
	}

	/**
	 * Display "note" column
	 *
	 * @param array $item Rendered item.
	 */
	public function column_note( $item ) {

		return WDPSN_SingleNotes::display_note( $item, $this->get_availability( $item ), true, true );
	}

	/**
	 * Display "note_id" column
	 *
	 * @param array $item Rendered item.
	 */
	public function column_note_id( $item ) {

		return isset( $item['ID'] ) ? $item['ID'] : '';
	}

	/**
	 * Display checkbox column
	 *
	 * @param array $item Rendered item.
	 */
	public function column_cb( $item ) {

		$has_permission = 'deleted' === $item['style'] || WDPSN_Helper::has_permission( $this->get_availability( $item ), 'own', $item['note_type_id'] );
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" ' . ( false === $has_permission ? 'disabled="disabled"' : '' ) . ' />',
			$this->_args['singular'],
			$item['ID']
		);
	}

	/**
	 * Get table columns and titles
	 */
	public function get_columns() {

		return array(
			'cb'        => '<input type="checkbox" />',
			'note_id'   => esc_html( __( 'Note ID', 'super-notes' ) ),
			'note'      => esc_html( __( 'Note', 'super-notes' ) ),
			'note_type' => esc_html( __( 'Note type', 'super-notes' ) ),
			'added_to'  => esc_html( __( 'Added where?', 'super-notes' ) ),
			'added_by'  => esc_html( __( 'Added by', 'super-notes' ) ),
		);
	}

	/**
	 * Get sortable columns array
	 */
	public function get_sortable_columns() {

		return array(
			'note_id'   => array( 'ID', false ),
			'note'      => array( 'content_escaped', false ),
			'note_type' => array( 'note_type', false ),
			'added_to'  => array( 'added_to', false ),
			'added_by'  => array( 'added_by', false ),
		);
	}

	/**
	 * Get bulk actions array
	 */
	public function get_bulk_actions() {

		return array(
			'delete' => esc_html( __( 'Delete', 'super-notes' ) ),
		);
	}

	/**
	 * Get availability data
	 *
	 * @param array $item Rendered item.
	 */
	public function get_availability( $item ) {

		$availability_key = esc_attr( $item['parent_type'] ) . '.' . esc_attr( $item['parent_id'] );

		if ( ! isset( self::$availability[ $availability_key ] ) ) {
			self::$availability[ $availability_key ] = WDPSN_Helper::get_popup_availability( $item['parent_type'], $item['parent_id'], false, false );
		}

		return self::$availability[ $availability_key ];
	}

	/**
	 * Preapare items to display in table
	 */
	public function prepare_items() {

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);

		$per_page = WDPSN_NotesListPage::notes_per_page();
		$data     = self::search_filter( WDPSN_NotesListPage::get_all_notes() );

		usort(
			$data,
			function( $a, $b ) {

				$orderby = WDPSN_Helper::get_url_arg( 'orderby', 'ID' );
				$order   = WDPSN_Helper::get_url_arg( 'order', 'asc' );

				if ( 'ID' === $orderby ) {
					$result = $a[ $orderby ] < $b[ $orderby ] ? -1 : ( $a[ $orderby ] > $b[ $orderby ] ? 1 : 0 );
				} else {
					$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
				}

				return 'asc' === $order ? $result : -$result;
			}
		);

		$total_items = count( $data );
		$data        = array_slice( $data, ( ( $this->get_pagenum() - 1 ) * $per_page ), $per_page );

		$this->items = $data;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Filter notes array if we search for one note only
	 *
	 * @param array $notes Notes to be filtered.
	 */
	public static function search_filter( $notes ) {

		$search = WDPSN_Helper::get_url_arg( 'wdpsn-note-id', false );
		if ( false === $search ) {
			return $notes;
		}

		foreach ( $notes as $note ) {

			if ( $note['ID'] === (int) $search ) {
				return array( $note );
			}
		}

		return array();
	}
}
