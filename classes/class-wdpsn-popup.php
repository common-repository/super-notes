<?php

/**
 * Create or preview single note popup
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'init', array( 'WDPSN_Popup', 'init' ) );
add_action( 'wp_ajax_wdpsn_get_popup_notes_preview_template', array( 'WDPSN_Popup', 'get_popup_notes_preview_template' ) );
add_action( 'wp_ajax_wdpsn_open_popup_add_note_form', array( 'WDPSN_Popup', 'open_popup_add_note_form' ) );
add_action( 'wp_ajax_wdpsn_open_popup_edit_note_form', array( 'WDPSN_Popup', 'open_popup_edit_note_form' ) );
add_action( 'wp_ajax_wdpsn_add_note', array( 'WDPSN_Popup', 'add_note' ) );
add_action( 'wp_ajax_wdpsn_edit_note', array( 'WDPSN_Popup', 'edit_note' ) );
add_action( 'wp_ajax_wdpsn_delete_note_through_popup', array( 'WDPSN_Popup', 'delete_note_through_popup' ) );
add_action( 'wp_ajax_wdpsn_get_note_wrapper', array( 'WDPSN_Popup', 'get_note_wrapper' ) );
/**
 * Notes popup class
 */
class WDPSN_Popup
{
    /**
     * Element data
     *
     * @var mixed Element data.
     */
    public static  $element_data = false ;
    /**
     * Message holder
     *
     * @var array Message holder.
     */
    public static  $messages = array() ;
    /**
     * Initialize class
     */
    public static function init()
    {
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/class-wdpsn-popupcontent.php';
    }
    
    /**
     * Get POST value by key
     *
     * @param array  $data POST data.
     * @param string $key Value key.
     */
    public static function get_post_value( $data, $key )
    {
        $result = ( isset( $data[$key] ) ? sanitize_text_field( wp_unslash( $data[$key] ) ) : false );
        $result = ( false !== $result && !empty($result) ? $result : false );
        return $result;
    }
    
    /**
     * Update element data array
     *
     * @param array $data POST data.
     */
    public static function update_element_data( $data )
    {
        
        if ( false === self::$element_data && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $element_type = self::get_post_value( $data, 'element_type' );
            $element_id = self::get_post_value( $data, 'element_id' );
            
            if ( false !== $element_type && false !== $element_id ) {
                self::$element_data = array(
                    'type' => esc_attr( $element_type ),
                    'id'   => esc_attr( $element_id ),
                );
                return true;
            }
        
        }
        
        return false;
    }
    
    /**
     * Refresh & get output for notes preview screen
     *
     * @param string $status Status type.
     * @param array  $availability Notes availability.
     * @param array  $data POST data.
     */
    public static function notes_preview_refresh( $status, $availability, $data )
    {
        $action = ( isset( $data['action'] ) ? sanitize_text_field( wp_unslash( $data['action'] ) ) : false );
        $caller_type = self::get_post_value( $data, 'caller_type' );
        $note_content = ( isset( $data['note_data'] ) && isset( $data['note_data']['content'] ) ? sanitize_text_field( wp_unslash( $data['note_data']['content'] ) ) : false );
        ob_start();
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/' . esc_attr( ( 'success' === $status ? 'template-notes-preview' : 'template-error' ) ) . '.php';
        $html_response = ob_get_contents();
        ob_end_clean();
        $result = array(
            'status'                 => esc_attr( $status ),
            'output'                 => $html_response,
            'notes_count_for_holder' => WDPSN_Helper::count_notes( $availability, self::$element_data ),
        );
        if ( 'wdpsn_edit_note' === $action && 'popup' === $caller_type && 'success' === $status ) {
            if ( false !== $note_content ) {
                $result['updated_note_content_formatted'] = apply_filters( 'wdpsn_note_content', WDPSN_Helper::strip_and_balance_tags( $note_content ) );
            }
        }
        echo  wp_json_encode( $result ) ;
        wp_die();
    }
    
    /**
     * Get popup notes preview template
     */
    public static function get_popup_notes_preview_template()
    {
        check_ajax_referer( 'get-popup-notes-preview-template', 'nonce' );
        
        if ( self::update_element_data( $_POST ) ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            
            if ( true === WDPSN_Helper::has_permission( $availability, 'any' ) ) {
                $caller_type = self::get_post_value( $_POST, 'caller_type' );
                require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-notes-preview.php';
                wp_die();
            }
        
        }
        
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-error.php';
        wp_die();
    }
    
    /**
     * Open "add note" form
     */
    public static function open_popup_add_note_form()
    {
        check_ajax_referer( 'open-popup-add-note-form', 'nonce' );
        
        if ( self::update_element_data( $_POST ) ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            
            if ( true === WDPSN_Helper::has_permission( $availability, 'own' ) ) {
                $caller_type = self::get_post_value( $_POST, 'caller_type' );
                require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-add-note.php';
                wp_die();
            }
        
        }
        
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-error.php';
        wp_die();
    }
    
    /**
     * Open "edit note" form
     */
    public static function open_popup_edit_note_form()
    {
        check_ajax_referer( 'open-popup-edit-note-form', 'nonce' );
        $note_data = ( isset( $_POST['note_data'] ) && is_array( $_POST['note_data'] ) ? wdpsn_sanitize_array( wp_unslash( $_POST['note_data'] ) ) : false );
        
        if ( self::update_element_data( $_POST ) && false !== $note_data ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            $note_type_id = self::get_post_value( $note_data, 'note_type_id' );
            $note_id = self::get_post_value( $note_data, 'note_id' );
            
            if ( false !== $note_type_id && WDPSN_Helper::is_note_type_id_valid( $note_type_id ) && true === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) && false !== $note_id ) {
                $note = WDPSN_SingleNotes::get_single_note( $note_id );
                $caller_type = self::get_post_value( $_POST, 'caller_type' );
                $notify_data = array(
                    'owners'  => ( isset( $note['data'] ) && isset( $note['data']['notify_owners'] ) && in_array( $note['data']['notify_owners'], array( 'yes', 'no' ), true ) ? $note['data']['notify_owners'] : 'no' ),
                    'viewers' => ( isset( $note['data'] ) && isset( $note['data']['notify_viewers'] ) && in_array( $note['data']['notify_viewers'], array( 'yes', 'no' ), true ) ? $note['data']['notify_viewers'] : 'no' ),
                );
                require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-edit-note.php';
                wp_die();
            }
        
        }
        
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/notes-popup/template-error.php';
        wp_die();
    }
    
    /**
     * Add note
     */
    public static function add_note()
    {
        check_ajax_referer( 'add-note', 'nonce' );
        $note_data = ( isset( $_POST['note_data'] ) && is_array( $_POST['note_data'] ) ? wdpsn_sanitize_array( wp_unslash( $_POST['note_data'] ) ) : false );
        
        if ( self::update_element_data( $_POST ) && false !== $note_data ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            $note_type_id = WDPSN_DataCache::get_note_type_id();
            
            if ( false !== $note_type_id && WDPSN_Helper::is_note_type_id_valid( $note_type_id ) && true === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) ) {
                $note_id = WDPSN_SingleNotes::add( $note_type_id, self::$element_data, $note_data );
                
                if ( false !== $note_id ) {
                    self::$messages[] = array(
                        'status'  => 'success',
                        'content' => esc_html( __( 'Note added successfuly.', 'super-notes' ) ),
                    );
                    self::notes_preview_refresh( 'success', $availability, $_POST );
                    wp_die();
                }
            
            }
        
        }
        
        self::notes_preview_refresh( 'error', $availability, $_POST );
        wp_die();
    }
    
    /**
     * Edit note
     */
    public static function edit_note()
    {
        check_ajax_referer( 'edit-note', 'nonce' );
        $note_data = ( isset( $_POST['note_data'] ) && is_array( $_POST['note_data'] ) ? wdpsn_sanitize_array( wp_unslash( $_POST['note_data'] ) ) : false );
        
        if ( self::update_element_data( $_POST ) && false !== $note_data ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            $note_type_id = self::get_post_value( $note_data, 'note_type_id' );
            
            if ( false !== $note_type_id && WDPSN_Helper::is_note_type_id_valid( $note_type_id ) && true === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) ) {
                $updated = WDPSN_SingleNotes::edit( $note_data );
                $caller_type = self::get_post_value( $_POST, 'caller_type' );
                if ( false !== $updated ) {
                    switch ( $caller_type ) {
                        case 'notes-list-table':
                            echo  wp_json_encode( array(
                                'status' => 'success',
                            ) ) ;
                            wp_die();
                            break;
                        default:
                            self::$messages[] = array(
                                'status'  => 'success',
                                'content' => esc_html( __( 'Note updated successfuly.', 'super-notes' ) ),
                            );
                            self::notes_preview_refresh( 'success', $availability, $_POST );
                            wp_die();
                            break;
                    }
                }
            }
        
        }
        
        self::notes_preview_refresh( 'error', $availability, $_POST );
        wp_die();
    }
    
    /**
     * Delete note
     */
    public static function delete_note_through_popup()
    {
        check_ajax_referer( 'delete-note-through-popup', 'nonce' );
        $note_data = ( isset( $_POST['note_data'] ) && is_array( $_POST['note_data'] ) ? wdpsn_sanitize_array( wp_unslash( $_POST['note_data'] ) ) : false );
        
        if ( self::update_element_data( $_POST ) && false !== $note_data ) {
            $availability = WDPSN_Helper::get_popup_availability( self::$element_data['type'], self::$element_data['id'] );
            $note_type_id = self::get_post_value( $note_data, 'note_type_id' );
            $note_id = self::get_post_value( $note_data, 'note_id' );
            
            if ( false !== $note_type_id && WDPSN_Helper::is_note_type_id_valid( $note_type_id ) && true === WDPSN_Helper::has_permission( $availability, 'own', $note_type_id ) && false !== $note_id ) {
                $deletion_status = WDPSN_SingleNotes::delete( $note_id );
                
                if ( true === $deletion_status ) {
                    self::$messages[] = array(
                        'status'  => 'success',
                        'content' => esc_html( __( 'Note successfuly deleted.', 'super-notes' ) ),
                    );
                    self::notes_preview_refresh( 'success', $availability, $_POST );
                    wp_die();
                }
            
            }
        
        }
        
        self::notes_preview_refresh( 'error', $availability, $_POST );
        wp_die();
    }
    
    /**
     * Get note wrapper
     */
    public static function get_note_wrapper()
    {
        check_ajax_referer( 'get-note-wrapper', 'nonce' );
        $note_id = ( isset( $_POST['note_id'] ) ? sanitize_text_field( wp_unslash( $_POST['note_id'] ) ) : false );
        if ( false === $note_id ) {
            wp_die();
        }
        $note_data = WDPSN_SingleNotes::get_single_note( $note_id );
        $note_types = WDPSN_DataCache::get_note_types();
        $note_type = ( isset( $note_types[$note_data['note_type_id']] ) ? $note_types[$note_data['note_type_id']] : false );
        if ( false === $note_type ) {
            wp_die();
        }
        $meta = WDPSN_SingleNotes::get_single_note_meta( $note_data['id'] );
        $note = array(
            'id'           => $note_data['id'],
            'note_type'    => $note_type['post_title'],
            'note_type_id' => $note_data['note_type_id'],
            'parent_type'  => $note_data['data']['parent_type'],
            'parent_id'    => $note_data['data']['parent_id'],
            'content'      => $note_data['content'],
            'style'        => WDPSN_Helper::get_single_note_style( $note_types, $note_data['note_type_id'] ),
        );
        $availability = WDPSN_Helper::get_popup_availability( $note['parent_type'], $note['parent_id'] );
        WDPSN_SingleNotes::display_note(
            $note,
            $availability,
            false,
            true
        );
        wp_die();
    }

}