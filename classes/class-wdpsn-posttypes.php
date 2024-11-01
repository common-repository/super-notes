<?php

/**
 * Register and manage custom post types
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'init', array( 'WDPSN_PostTypes', 'register_post_types' ) );
add_action( 'current_screen', array( 'WDPSN_PostTypes', 'admin_redirections' ) );
/**
 * Custom post types class
 */
abstract class WDPSN_PostTypes
{
    /**
     * Register custom post types
     */
    public static function register_post_types()
    {
        $note_types_label = esc_html( __( 'Note Type', 'super-notes' ) );
        register_post_type( 'wdpsn_note_types', array(
            'supports'        => array( 'title' ),
            'public'          => false,
            'show_in_menu'    => 'wdpsn',
            'show_ui'         => true,
            'has_archive'     => false,
            'menu_icon'       => 'dashicons-layout',
            'menu_position'   => 100,
            'labels'          => array(
            'name'               => esc_html( $note_types_label ),
            'singular_name'      => esc_html( __( 'Note Type', 'super-notes' ) ),
            'menu_name'          => esc_html( __( 'Notes', 'super-notes' ) ),
            'name_admin_bar'     => esc_html( $note_types_label ),
            'add_new'            => esc_html( __( 'Add new Note Type', 'super-notes' ) ),
            'add_new_item'       => esc_html( __( 'Add New Note Type', 'super-notes' ) ),
            'new_item'           => esc_html( __( 'New Note Type', 'super-notes' ) ),
            'edit_item'          => esc_html( __( 'Edit Note Type', 'super-notes' ) ),
            'view_item'          => esc_html( __( 'View Note Type', 'super-notes' ) ),
            'all_items'          => esc_html( __( 'Note Types', 'super-notes' ) ),
            'search_items'       => esc_html( __( 'Search Note Types', 'super-notes' ) ),
            'not_found'          => esc_html( __( 'No Note Types found.', 'super-notes' ) ),
            'not_found_in_trash' => esc_html( __( 'No Note Types found in Trash.', 'super-notes' ) ),
        ),
            'map_meta_cap'    => true,
            'capability_type' => 'wdpsn_note_type',
        ) );
        register_post_type( 'wdpsn_single_note', array(
            'supports'        => array( 'title' ),
            'public'          => false,
            'show_in_menu'    => false,
            'show_ui'         => false,
            'has_archive'     => false,
            'map_meta_cap'    => true,
            'capability_type' => 'wdpsn_single_note',
        ) );
        add_filter( 'post_updated_messages', array( get_class(), 'post_updated_messages' ) );
        add_action( 'add_meta_boxes', array( get_class(), 'remove_slug_metabox' ) );
    }
    
    /**
     * Custom messages for own post types
     *
     * @param array $messages Default messages array.
     */
    public static function post_updated_messages( $messages )
    {
        $post = get_post();
        $messages['wdpsn_note_types'] = array(
            '',
            esc_html( __( 'Note Type updated.', 'super-notes' ) ),
            esc_html( __( 'Custom field updated.', 'super-notes' ) ),
            esc_html( __( 'Custom field deleted.', 'super-notes' ) ),
            esc_html( __( 'Note Type updated.', 'super-notes' ) ),
            false,
            esc_html( __( 'Note Type published.', 'super-notes' ) ),
            esc_html( __( 'Note Type saved.', 'super-notes' ) ),
            esc_html( __( 'Note Type submitted.', 'super-notes' ) ),
            /* Translators: Note Type scheduled date. */
            esc_html( sprintf( __( 'Note Type scheduled for: <strong>%1$s</strong>.', 'super-notes' ), date_i18n( __( 'M j, Y @ G:i', 'super-notes' ), strtotime( $post->post_date ) ) ) ),
            esc_html( __( 'Note Type draft updated.', 'super-notes' ) ),
        );
        return $messages;
    }
    
    /**
     * Get custom post meta options
     *
     * @param string $slug Additional slug to add before options.
     */
    public static function get_custom_post_meta_options( $slug = '' )
    {
        $result = array(
            esc_attr( $slug ) . 'owners',
            esc_attr( $slug ) . 'viewers',
            esc_attr( $slug ) . 'location',
            esc_attr( $slug ) . 'styles'
        );
        return $result;
    }
    
    /**
     * Remove "slug" metabox
     */
    public static function remove_slug_metabox()
    {
        remove_meta_box( 'slugdiv', 'wdpsn_note_types', 'normal' );
    }
    
    /**
     * Handle admin redirections
     */
    public static function admin_redirections()
    {
        global  $pagenow ;
        $current_screen = get_current_screen();
        $note_type_id = WDPSN_DataCache::get_note_type_id();
        
        if ( 'edit-wdpsn_note_types' === $current_screen->id ) {
            wp_safe_redirect( admin_url( ( false === $note_type_id ? '/post-new.php?post_type=wdpsn_note_types' : '/post.php?post=' . esc_attr( $note_type_id ) . '&action=edit' ) ), 301 );
            exit;
        }
        
        
        if ( 'post-new.php' === $pagenow && 'wdpsn_note_types' === $current_screen->id && false !== $note_type_id ) {
            wp_safe_redirect( admin_url( '/post.php?post=' . esc_attr( $note_type_id ) . '&action=edit' ), 301 );
            exit;
        }
    
    }

}