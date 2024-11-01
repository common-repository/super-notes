<?php

/**
 * Plugin configuration class
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'init', array( 'WDPSN_Config', 'init' ) );
add_action( 'admin_enqueue_scripts', array( 'WDPSN_Config', 'enqueue_scripts' ) );
/**
 * Plugin configuration class
 */
abstract class WDPSN_Config
{
    /**
     * Store plugin data
     *
     * @var array Plugin data.
     */
    public static  $plugin_data = array() ;
    /**
     * Initialize plugin
     */
    public static function init()
    {
        load_plugin_textdomain( 'super-notes', false, basename( dirname( WDPSN_MAIN_FILE ) ) . '/languages' );
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        self::$plugin_data = array_change_key_case( get_plugin_data( WDPSN_MAIN_FILE, false, false ), CASE_LOWER );
        self::$plugin_data['display-additional-notices'] = true;
        define( 'WDPSN_VERSION', self::get( 'version' ) );
    }
    
    /**
     * Load required global classes
     */
    public static function load_classes()
    {
        $classes = array(
            '/classes/class-wdpsn-capabilities.php',
            '/classes/class-wdpsn-welcomepage.php',
            '/classes/class-wdpsn-posttypes.php',
            '/classes/class-wdpsn-helper.php',
            '/classes/class-wdpsn-metaboxbuilder.php',
            '/classes/class-wdpsn-datacache.php',
            '/classes/class-wdpsn-singlenotes.php',
            '/classes/class-wdpsn-customnotescss.php',
            '/classes/class-wdpsn-holder.php',
            '/classes/class-wdpsn-popup.php',
            '/classes/class-wdpsn-noteslistpage.php',
            '/classes/class-wdpsn-postnotessummary.php',
            '/classes/class-wdpsn-history.php',
            '/classes/class-wdpsn-compatibility.php'
        );
        foreach ( $classes as $class ) {
            require_once dirname( WDPSN_MAIN_FILE ) . esc_attr( $class );
        }
    }
    
    /**
     * Return single value from plugin data
     *
     * @param string $key Plugin data option key.
     */
    public static function get( $key )
    {
        return ( isset( self::$plugin_data[$key] ) ? self::$plugin_data[$key] : false );
    }
    
    /**
     * Enqueue custom scripts and styles
     */
    public static function enqueue_scripts()
    {
        if ( !class_exists( '_WP_Editors' ) ) {
            require_once ABSPATH . WPINC . '/class-wp-editor.php';
        }
        _WP_Editors::editor_settings( 'wdpsn-editor', _WP_Editors::parse_settings( 'wdpsn-editor', array() ) );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style(
            'wdpsn-main-stylesheet',
            plugin_dir_url( WDPSN_MAIN_FILE ) . 'assets/css/main.min.css',
            array(),
            WDPSN_VERSION
        );
        wp_add_inline_style( 'wdpsn-main-stylesheet', WDPSN_CustomNotesCSS::create_inline_css() );
        wp_enqueue_script(
            'wdpsn-main-script',
            plugin_dir_url( WDPSN_MAIN_FILE ) . 'assets/js/main.min.js',
            array( 'jquery', 'wp-color-picker' ),
            WDPSN_VERSION,
            true
        );
        wp_localize_script( 'wdpsn-main-script', 'wdpsnData', self::get_localize_strings() );
        if ( in_array( get_post_type(), WDPSN_MetaBoxBuilder::get_cpt_array(), true ) ) {
            wp_add_inline_script( 'wdpsn-main-script', WDPSN_MetaBoxBuilder::get_json_data_for_dynamic_fields() );
        }
    }
    
    /**
     * Get strings for script localize
     */
    public static function get_localize_strings()
    {
        $strings = array(
            'ajax_url'                                => admin_url( 'admin-ajax.php' ),
            'includes_url'                            => includes_url(),
            'nonce_get_note_holders'                  => wp_create_nonce( 'get-note-holders' ),
            'nonce_install_dummy_data'                => wp_create_nonce( 'install-dummy-data' ),
            'nonce_delete_notes_through_table'        => wp_create_nonce( 'delete-notes-through-table' ),
            'nonce_delete_note_through_notes_summary' => wp_create_nonce( 'delete-note-through-post-notes-summary' ),
            'nonce_update_post_notes_summary_mode'    => wp_create_nonce( 'update-post-notes-summary-mode' ),
            'nonce_get_popup_notes_preview_template'  => wp_create_nonce( 'get-popup-notes-preview-template' ),
            'nonce_open_popup_add_note_form'          => wp_create_nonce( 'open-popup-add-note-form' ),
            'nonce_open_popup_edit_note_form'         => wp_create_nonce( 'open-popup-edit-note-form' ),
            'nonce_add_note'                          => wp_create_nonce( 'add-note' ),
            'nonce_edit_note'                         => wp_create_nonce( 'edit-note' ),
            'nonce_delete_note_through_popup'         => wp_create_nonce( 'delete-note-through-popup' ),
            'nonce_get_note_wrapper'                  => wp_create_nonce( 'get-note-wrapper' ),
            'nonce_refresh_post_notes_summary'        => wp_create_nonce( 'refresh-post-notes-summary' ),
            'nonce_update_desc'                       => wp_create_nonce( 'update-additional-description' ),
            'plugin_name'                             => esc_html( __( 'Super Notes', 'super-notes' ) ),
            'global_notes'                            => esc_html( __( 'Global notes', 'super-notes' ) ),
            'please_wait'                             => esc_html( __( 'Please wait...', 'super-notes' ) ),
            'please_confirm_deletion'                 => esc_html( __( 'Please confirm deletion', 'super-notes' ) ),
            'please_select_the_action'                => esc_html( __( 'Please select the action', 'super-notes' ) ),
            'apply'                                   => esc_html( __( 'Apply', 'super-notes' ) ),
            'yes_overwrite_current_note_type'         => esc_html( __( 'Yes, overwrite current Note Type', 'super-notes' ) ),
            'dummy_notice_top'                        => esc_html( __( 'Since you use free plugin version, you can use only one Note Type on your website, and there\'s already one Note Type added.', 'super-notes' ) ),
            'dummy_notice_bottom'                     => esc_html( __( 'While installing dummy data, this Note Type will be overwritten by the default one - please confirm if you want to continue.', 'super-notes' ) ),
            'install_now'                             => esc_html( __( 'Install now', 'super-notes' ) ),
            'install_again'                           => esc_html( __( 'Install again', 'super-notes' ) ),
            'something_went_wrong_please_try_again'   => esc_html( __( 'Something went wrong, please try again.', 'super-notes' ) ),
            'dummy_data_correctly_installed'          => esc_html( __( 'Dummy data correctly installed!', 'super-notes' ) ),
            'text'                                    => esc_html( __( 'Text', 'super-notes' ) ),
            'visual'                                  => esc_html( __( 'Visual', 'super-notes' ) ),
            'loading'                                 => esc_html( __( 'Loading...', 'super-notes' ) ),
            'please_wait_a_second'                    => esc_html( __( 'Please wait a second', 'super-notes' ) ),
            'something_went_wrong'                    => esc_html( __( 'Something went wrong!', 'super-notes' ) ),
            'unexpected_error'                        => esc_html( __( 'An unexpected error occurred, please try again.', 'super-notes' ) ),
            'unexpected_error_try_again'              => esc_html( __( 'Unexpecter error appeared, please try again later.', 'super-notes' ) ),
            'note_successfully_updated'               => esc_html( __( 'Note successfully updated!', 'super-notes' ) ),
            'there_is_no_any_notes_yet'               => esc_html( __( 'There\'s no any notes yet.', 'super-notes' ) ),
            'note_has_been_removed'                   => esc_html( __( 'Note has been removed.', 'super-notes' ) ),
            'please_wait_option'                      => esc_html( __( '-- Please wait... --', 'super-notes' ) ),
            'user_role'                               => esc_html( __( 'User role', 'super-notes' ) ),
            'user'                                    => esc_html( __( 'User', 'super-notes' ) ),
            'is_equal_to'                             => esc_html( __( 'is equal to', 'super-notes' ) ),
            'is_not_equal_to'                         => esc_html( __( 'is not equal to', 'super-notes' ) ),
            'and'                                     => esc_html( __( 'And', 'super-notes' ) ),
            'or'                                      => esc_html( __( 'or', 'super-notes' ) ),
            'post_type'                               => esc_html( __( 'Post type', 'super-notes' ) ),
            'posts'                                   => esc_html( __( 'Posts', 'super-notes' ) ),
            'post'                                    => esc_html( __( 'Post', 'super-notes' ) ),
            'post_status'                             => esc_html( __( 'Post status', 'super-notes' ) ),
            'post_format'                             => esc_html( __( 'Post format', 'super-notes' ) ),
            'post_taxonomy'                           => esc_html( __( 'Post taxonomy', 'super-notes' ) ),
            'post_author'                             => esc_html( __( 'Post author', 'super-notes' ) ),
            'post_author_role'                        => esc_html( __( 'Post author role', 'super-notes' ) ),
            'pages'                                   => esc_html( __( 'Pages', 'super-notes' ) ),
            'page'                                    => esc_html( __( 'Page', 'super-notes' ) ),
            'page_template'                           => esc_html( __( 'Page template', 'super-notes' ) ),
            'page_type'                               => esc_html( __( 'Page type', 'super-notes' ) ),
            'page_ancestor'                           => esc_html( __( 'Page ancestor', 'super-notes' ) ),
            'page_parent'                             => esc_html( __( 'Page parent', 'super-notes' ) ),
            'other'                                   => esc_html( __( 'Other', 'super-notes' ) ),
            'taxonomy_term'                           => esc_html( __( 'Taxonomy term', 'super-notes' ) ),
            'plugin'                                  => esc_html( __( 'Plugin', 'super-notes' ) ),
            'special_location'                        => esc_html( __( 'Special location', 'super-notes' ) ),
            'calculating'                             => esc_html( __( 'Calculating...', 'super-notes' ) ),
            'http_host'                               => ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' ),
            'request_uri'                             => ( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ),
            'add_extras'                              => 'no',
        );
        return array_merge( $strings, WDPSN_MetaBoxBuilder::get_localized_strings() );
    }
    
    /**
     * Plugin activation hook
     */
    public static function activation_hook()
    {
        require_once dirname( WDPSN_MAIN_FILE ) . '/classes/class-wdpsn-capabilities.php';
        WDPSN_Capabilities::assign_caps();
    }

}