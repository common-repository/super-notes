<?php

/**
 * Inline CSS style for custom notes colors
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Custom notes CSS styles class
 */
abstract class WDPSN_CustomNotesCSS
{
    /**
     * Create inline CSS style
     */
    public static function create_inline_css()
    {
        $css = self::get_styles_for_note_types();
        $output = '';
        if ( array() !== $css ) {
            foreach ( $css as $css_selector => $css_attributes ) {
                $output .= $css_selector . '{ ';
                foreach ( $css_attributes as $css_attribute_key => $css_attribute_value ) {
                    $output .= $css_attribute_key . ': ' . $css_attribute_value . '; ';
                }
                $output .= '} ';
            }
        }
        return trim( $output );
    }
    
    /**
     * Get css styles array for note types
     */
    public static function get_styles_for_note_types()
    {
        $key = 'wdpsn_note_types_styles';
        $colors = array();
        $css = array();
        foreach ( WDPSN_DataCache::get_note_types() as $note_type ) {
            if ( isset( $note_type['post_meta'] ) && isset( $note_type['post_meta'][esc_attr( $key )] ) && isset( $note_type['post_meta'][esc_attr( $key )]['color_scheme'] ) && isset( $note_type['post_meta'][esc_attr( $key )]['colors'] ) ) {
                if ( 'custom' === $note_type['post_meta'][esc_attr( $key )]['color_scheme'] ) {
                    $colors[esc_attr( $note_type['id'] )] = $note_type['post_meta'][esc_attr( $key )]['colors'];
                }
            }
        }
        if ( array() !== $colors ) {
            foreach ( $colors as $note_type_id => $color ) {
                $css['.wdpsn-note-container.wdpsn-note-container-id-' . esc_attr( $note_type_id ) . '.wdpsn-note-container--custom'] = array(
                    'color'        => esc_attr( $color['main-color'] ),
                    'border-color' => esc_attr( $color['border-color'] ),
                    'background'   => esc_attr( $color['background-color'] ),
                );
                $css['table.wp-list-table.super-notes_page_wdpsn-all-notes td.note.column-note .wdpsn-note-container-id-' . esc_attr( $note_type_id ) . '.wdpsn-note-container--custom p'] = array(
                    'color' => esc_attr( $color['main-color'] ),
                );
                $css['table.wp-list-table.super-notes_page_wdpsn-all-notes td.note.column-note .wdpsn-note-container-id-' . esc_attr( $note_type_id ) . '.wdpsn-note-container--custom li'] = array(
                    'color' => esc_attr( $color['main-color'] ),
                );
                $css['.wdpsn-note-container.wdpsn-note-container-id-' . esc_attr( $note_type_id ) . '.wdpsn-note-container--custom .wdpsn-note-container__content'] = array(
                    'box-shadow' => '0 5px 15px 0 ' . esc_attr( $color['box-shadow-color'] ),
                );
                $css['.wdpsn-note-container.wdpsn-note-container-id-' . esc_attr( $note_type_id ) . '.wdpsn-note-container--custom .wdpsn-note-container__footer'] = array(
                    'border-bottom-color' => esc_attr( $color['border-bottom-color'] ),
                    'background'          => esc_attr( $color['border-color'] ),
                );
            }
        }
        return $css;
    }

}