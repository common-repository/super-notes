<?php

/**
 * Popup template: Add new note
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wdpsn-note-popup__container__content__heading">
	<h1><?php 
echo  esc_html( __( 'Add new note', 'super-notes' ) ) ;
?></h1>
	<button class="button button-secondary wdpsn-note-popup__cancel-button" data-action="wdpsn-cancel-add-note"><?php 
echo  esc_html( __( 'Cancel', 'super-notes' ) ) ;
?></button>
	<span class="wdpsn-note-popup__close">×</span>
</div>

<?php 
WDPSN_PopupContent::display_element_details( self::$element_data );
?>

<div class="wdpsn-note-popup__container__content__note-editor">

	<div class="wdpsn-note-popup__container__content__note-editor__field wdpsn-note-popup__container__content__note-editor__content-field">
		<textarea class="wdpsn_rich_editor" name="wdpsn_add_note_content"></textarea>
	</div>

	<?php 
if ( true === WDPSN_Config::get( 'display-additional-notices' ) ) {
    WDPSN_Helper::display_additional_notices();
}
?>
	<button type="button" class="button button-primary button-large" data-action="wdpsn-add-note" data-caller-type="<?php 
echo  esc_attr( ( false !== $caller_type ? $caller_type : 'popup' ) ) ;
?>"><?php 
echo  esc_html( __( 'Add note', 'super-notes' ) ) ;
?></button>

</div>
