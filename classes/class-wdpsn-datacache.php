<?php

/**
 * Get data from WordPress and store it in "cache" to avoid getting the same data multiple times
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Plugin data cache class
 */
abstract class WDPSN_DataCache
{
    /**
     * All note types
     *
     * @var mixed Note types array.
     */
    public static  $note_types = false ;
    /**
     * All note tags
     *
     * @var mixed Note tags array.
     */
    public static  $note_tags = false ;
    /**
     * All users array
     *
     * @var mixed Users array.
     */
    public static  $users = false ;
    /**
     * All allowed users
     *
     * @var mixed Allowed users array.
     */
    public static  $allowed_users = false ;
    /**
     * Get all note types
     */
    public static function get_note_types()
    {
        if ( false === self::$note_types ) {
            self::$note_types = self::get_custom_post_types_posts( 'wdpsn_note_types' );
        }
        return self::$note_types;
    }
    
    /**
     * Get note type ID
     */
    public static function get_note_type_id()
    {
        $note_type = self::get_note_types();
        
        if ( array() !== $note_type ) {
            $note_type_id = WDPSN_Helper::array_column( $note_type, 'id' );
            return $note_type_id[0];
        }
        
        return false;
    }
    
    /**
     * Get required data about custom post types
     *
     * @param string $post_type Post type name.
     */
    public static function get_custom_post_types_posts( $post_type )
    {
        $result = array();
        $custom_posts = get_posts( array(
            'numberposts' => self::get_posts_number( $post_type ),
            'post_type'   => esc_attr( $post_type ),
        ) );
        if ( is_array( $custom_posts ) && count( $custom_posts ) > 0 ) {
            foreach ( $custom_posts as $id => $type ) {
                $result[$type->ID] = array(
                    'id'          => $type->ID,
                    'post_title'  => $type->post_title,
                    'post_status' => $type->post_status,
                    'post_meta'   => WDPSN_Helper::get_own_meta_only( get_post_meta( $type->ID ), $post_type ),
                );
            }
        }
        unset( $custom_posts, $id, $type );
        wp_reset_postdata();
        return $result;
    }
    
    /**
     * Get number of posts that can be requested
     *
     * @param string $post_type Post type name.
     */
    public static function get_posts_number( $post_type )
    {
        $result = ( 'wdpsn_single_note' === $post_type ? -1 : 1 );
        return $result;
    }
    
    /**
     * Get all users array
     */
    public static function get_all_users()
    {
        
        if ( false === self::$users ) {
            $users = get_users();
            self::$users = array();
            foreach ( $users as $user ) {
                self::$users[] = array(
                    'id'    => $user->ID,
                    'name'  => $user->display_name,
                    'roles' => $user->roles,
                );
            }
        }
        
        return self::$users;
    }
    
    /**
     * Get an array of allowed users for all note types
     */
    public static function get_allowed_users()
    {
        
        if ( false === self::$allowed_users ) {
            $results = array();
            $elements = array(
                'note-types' => self::get_note_types(),
            );
            foreach ( $elements as $element_key => $element_data ) {
                foreach ( $element_data as $element ) {
                    switch ( $element_key ) {
                        case 'note-types':
                            $result = array(
                                'owners'  => array(),
                                'viewers' => array(),
                            );
                            if ( isset( $element['post_meta'] ) && isset( $element['post_meta']['wdpsn_note_types_owners'] ) ) {
                                $result['owners'] = self::get_element_allowed_users( 'owners', $element['id'], $element['post_meta']['wdpsn_note_types_owners'] );
                            }
                            if ( isset( $element['post_meta'] ) && isset( $element['post_meta']['wdpsn_note_types_viewers'] ) ) {
                                $result['viewers'] = self::get_element_allowed_users(
                                    'viewers',
                                    $element['id'],
                                    $element['post_meta']['wdpsn_note_types_viewers'],
                                    $result['owners']
                                );
                            }
                            break;
                        case 'note-tags':
                            break;
                    }
                    $results[$element['id']] = $result;
                    unset( $result );
                }
            }
            self::$allowed_users = $results;
            unset(
                $element,
                $element_key,
                $element_data,
                $results
            );
        }
        
        return self::$allowed_users;
    }
    
    /**
     * Get allowed users for selected element
     *
     * @param string $type Access type.
     * @param string $note_type_id Note type ID.
     * @param array  $note_type_config Note type config.
     * @param mixed  $owners Owners array.
     */
    public static function get_element_allowed_users(
        $type,
        $note_type_id,
        $note_type_config,
        $owners = false
    )
    {
        if ( false !== $note_type_id && false !== self::$allowed_users ) {
            if ( isset( self::$allowed_users[esc_attr( $note_type_id )] ) ) {
                return self::$allowed_users[esc_attr( $note_type_id )];
            }
        }
        if ( isset( $note_type_config['can_everyone'] ) && 'yes' === $note_type_config['can_everyone'] ) {
            return WDPSN_Helper::array_exclude( self::get_all_users(), 'roles' );
        }
        if ( 'viewers' === $type && isset( $note_type_config['can_owners_only'] ) && 'yes' === $note_type_config['can_owners_only'] ) {
            return $owners;
        }
        if ( isset( $note_type_config['who_can'] ) && isset( $note_type_config['who_can']['rules'] ) ) {
            return WDPSN_Helper::array_exclude( WDPSN_Helper::apply_rules( self::get_all_users(), $note_type_config['who_can']['rules'] ), 'roles' );
        }
    }
    
    /**
     * Get note type title by ID
     *
     * @param string $id Note type ID.
     */
    public static function get_note_type_title_by_id( $id )
    {
        $note_types = self::get_note_types();
        return ( isset( $note_types[esc_attr( $id )] ) ? $note_types[esc_attr( $id )]['post_title'] : WDPSN_Helper::get_deleted_data_notice() );
    }

}