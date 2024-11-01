<?php

/**
 * Handle users capabilities
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'admin_init', array( 'WDPSN_Capabilities', 'check_caps' ) );
/**
 * Capabilities class
 */
abstract class WDPSN_Capabilities
{
    /**
     * Store version key
     *
     * @var mixed Version key.
     */
    public static  $version_key = false ;
    /**
     * Option key
     *
     * @var string Option key.
     */
    public static  $option_key = 'wdpsn_capabilities_updated' ;
    /**
     * Get plugin version key
     */
    public static function get_version_key()
    {
        
        if ( false === self::$version_key ) {
            $version = 'free';
            $version .= '-' . WDPSN_VERSION;
            self::$version_key = $version;
        }
        
        return self::$version_key;
    }
    
    /**
     * Check if capabilities has been already updated before
     */
    public static function caps_already_updated()
    {
        $status = get_option( self::$option_key );
        return false !== $status && self::get_version_key() === $status;
    }
    
    /**
     * Check if caps has been already updated
     * - if not, start the update
     */
    public static function check_caps()
    {
        if ( true === self::caps_already_updated() ) {
            return;
        }
        self::assign_caps();
        update_option( self::$option_key, self::get_version_key() );
    }
    
    /**
     * Get custom capabilities
     *
     * @param string $cap Specific capability key.
     */
    public static function get_custom_caps( $cap = false )
    {
        $caps = array(
            'manage-plugin'     => array( 'manage_wdpsn_plugin' ),
            'preview-own-notes' => array( 'preview_own_wdpsn_notes' ),
            'single-note'       => array(
            'edit_wdpsn_single_note',
            'read_wdpsn_single_note',
            'delete_wdpsn_single_note',
            'edit_wdpsn_single_notes',
            'edit_others_wdpsn_single_notes',
            'publish_wdpsn_single_notes',
            'read_private_wdpsn_single_notes',
            'delete_wdpsn_single_notes',
            'delete_private_wdpsn_single_notes',
            'delete_published_wdpsn_single_notes',
            'delete_others_wdpsn_single_notes',
            'edit_private_wdpsn_single_notes',
            'edit_published_wdpsn_single_notes',
            'edit_wdpsn_single_notes'
        ),
            'note-type'         => array(
            'edit_wdpsn_note_type',
            'read_wdpsn_note_type',
            'delete_wdpsn_note_type',
            'edit_wdpsn_note_types',
            'edit_others_wdpsn_note_types',
            'publish_wdpsn_note_types',
            'read_private_wdpsn_note_types',
            'delete_wdpsn_note_types',
            'delete_private_wdpsn_note_types',
            'delete_published_wdpsn_note_types',
            'delete_others_wdpsn_note_types',
            'edit_private_wdpsn_note_types',
            'edit_published_wdpsn_note_types',
            'edit_wdpsn_note_types'
        ),
        );
        return ( false !== $cap && isset( $caps[$cap] ) ? $caps[$cap] : $caps );
    }
    
    /**
     * Add capabilities to user roles after plugin activation
     */
    public static function assign_caps()
    {
        global  $wp_roles ;
        $roles = array();
        $roles['administrator'] = get_role( 'administrator' );
        foreach ( self::get_custom_caps() as $cap_key => $caps ) {
            foreach ( $caps as $cap ) {
                $roles['administrator']->add_cap( $cap );
            }
        }
        $all_roles = $wp_roles->roles;
        $users_caps = array_merge( self::get_custom_caps( 'single-note' ), self::get_custom_caps( 'preview-own-notes' ) );
        foreach ( $all_roles as $role => $role_caps ) {
            if ( 'administrator' === $role ) {
                continue;
            }
            $roles[$role] = get_role( $role );
            foreach ( $users_caps as $cap ) {
                $roles[$role]->add_cap( $cap );
            }
        }
    }

}