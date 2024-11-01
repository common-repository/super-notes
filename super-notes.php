<?php

/**
 * Plugin Name: Super Notes
 * Plugin URI: http://wedoplugins.com/plugins/super-notes/
 * Description: Create notes and assign it to selected users and locations. Add admin notes to posts, pages, plugins etc., and add permissions to notes for users.
 * Author: We Do Plugins
 * Author URI: http://wedoplugins.com/
 * Version: 1.2.1
 * License: GPL2+
 * Text Domain: super-notes
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !is_admin() ) {
    return;
}

if ( !function_exists( 'wdpsn_fs' ) ) {
    define( 'WDPSN_MAIN_FILE', __FILE__ );
    /**
     * Freemius integration
     */
    function wdpsn_fs()
    {
        global  $wdpsn_fs ;
        
        if ( !isset( $wdpsn_fs ) ) {
            require_once dirname( WDPSN_MAIN_FILE ) . '/freemius/start.php';
            $wdpsn_fs = fs_dynamic_init( array(
                'id'             => '2412',
                'slug'           => 'super-notes',
                'type'           => 'plugin',
                'public_key'     => 'pk_79c12570432c2ec6f82153cda707c',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'wdpsn',
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wdpsn_fs;
    }
    
    wdpsn_fs();
    do_action( 'wdpsn_fs_loaded' );
    require_once dirname( WDPSN_MAIN_FILE ) . '/classes/class-wdpsn-config.php';
    WDPSN_Config::load_classes();
    register_activation_hook( WDPSN_MAIN_FILE, array( 'WDPSN_Config', 'activation_hook' ) );
}
