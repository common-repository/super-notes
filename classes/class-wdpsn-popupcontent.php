<?php

/**
 * Create popup contents
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Popup contents class
 */
abstract class WDPSN_PopupContent
{
    /**
     * Display details about element where note will be added
     *
     * @param array $element_data Element data.
     */
    public static function display_element_details( $element_data )
    {
        ?>
		<p class="wdpsn-note-popup__container__content__post-details">
			<?php 
        echo  wp_kses( WDPSN_Helper::get_single_note_parent_name( $element_data['type'], $element_data['id'] ), array(
            'strong' => array(),
        ) ) ;
        ?>
		</p>
		<?php 
    }

}