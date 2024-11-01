<?php
/**
 * Popup template: Notes preview
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'post-notes-summary' !== $caller_type ) {

	?>
	<div class="wdpsn-note-popup__container__content__heading">

		<h1><?php echo esc_html( __( 'Notes preview', 'super-notes' ) ); ?></h1>
		<span class="wdpsn-note-popup__close">×</span>

	</div>
	<?php

	WDPSN_PopupContent::display_element_details( self::$element_data );
}

WDPSN_Helper::display_messages( self::$messages );
WDPSN_SingleNotes::display_notes_preview( self::$element_data, $availability, ( 'post-notes-summary' !== $caller_type ) );
