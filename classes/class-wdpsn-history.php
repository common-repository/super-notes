<?php

/**
 * Track note history
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Notes history class
 */
abstract class WDPSN_History
{
    /**
     * Get post meta history key
     *
     * @var string Meta key.
     */
    public static  $meta_key = 'wdpsn_history' ;
    /**
     * Add new history record
     *
     * @param string $note_id Note ID.
     * @param string $message Message.
     */
    public static function add_record( $note_id, $message )
    {
        $message = str_replace( '{user}', self::get_current_user_data(), $message );
        $history = self::get_records( $note_id );
        $history[current_time( 'timestamp', true )] = $message;
        update_post_meta( $note_id, self::$meta_key, wp_json_encode( $history ) );
    }
    
    /**
     * Get history records for selected note
     *
     * @param string $note_id Note ID.
     */
    public static function get_records( $note_id )
    {
        $records = get_post_meta( $note_id, self::$meta_key, true );
        $empty = false === $records || null === $records || '' === $records;
        return ( $empty ? array() : json_decode( $records, true ) );
    }
    
    /**
     * Get user data
     */
    public static function get_current_user_data()
    {
        $user = wp_get_current_user();
        return '{user:' . esc_attr( $user->ID ) . '}';
    }
    
    /**
     * Prepare output
     *
     * @param string $type Prepared type.
     * @param string $data Output data.
     */
    public static function prepare( $type, $data )
    {
        $result = '';
        switch ( $type ) {
            case 'date':
                $result = human_time_diff( $data, current_time( 'timestamp', true ) ) . ' ' . esc_html( __( 'ago', 'super-notes' ) );
                break;
            case 'content':
                $result = $data;
                $matched = preg_match_all( '/{(.*?)}/', $result, $match );
                
                if ( 0 !== $matched && false !== $matched ) {
                    $matched_count = count( $match[0] );
                    for ( $i = 0 ;  $i < $matched_count ;  $i++ ) {
                        $dynamic_result = '<span class="wdpsn-data-deleted">' . esc_html( __( 'Deleted', 'super-notes' ) ) . '</span>';
                        $dynamic_data_found = false;
                        $dynamic_data = explode( ':', $match[1][$i] );
                        switch ( $dynamic_data[0] ) {
                            case 'user':
                                $user = get_user_by( 'id', $dynamic_data[1] );
                                
                                if ( false !== $user ) {
                                    $dynamic_result = $user->display_name;
                                    $dynamic_data_found = true;
                                }
                                
                                break;
                            case 'tag':
                                break;
                        }
                        if ( true === $dynamic_data_found ) {
                            $dynamic_result = '<strong>' . esc_html( $dynamic_result ) . '</strong>';
                        }
                        $result = str_replace( $match[0][$i], $dynamic_result, $result );
                    }
                }
                
                break;
        }
        return $result;
    }

}