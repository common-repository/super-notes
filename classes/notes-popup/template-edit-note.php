<?php

/**
 * Popup template: Edit note
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wdpsn-note-popup__container__content__heading">
	<h1><?php 
echo  esc_html( __( 'Edit note', 'super-notes' ) ) ;
?></h1>
	<?php 

if ( 'notes-list-table' !== $caller_type ) {
    ?>
		<button class="button button-secondary wdpsn-note-popup__cancel-button" data-action="wdpsn-cancel-edit-note"><?php 
    echo  esc_html( __( 'Cancel', 'super-notes' ) ) ;
    ?></button>
		<?php 
}

?>
	<span class="wdpsn-note-popup__close">×</span>
</div>

<?php 
WDPSN_PopupContent::display_element_details( self::$element_data );
?>

<div class="wdpsn-note-popup__container__content__note-editor">

	<input type="hidden" name="wdpsn_edit_note_note_id" value="<?php 
echo  esc_attr( $note_id ) ;
?>" />
	<input type="hidden" name="wdpsn_edit_note_note_type_id" value="<?php 
echo  esc_attr( $note_type_id ) ;
?>" />

	<div class="wdpsn-note-popup__container__content__note-editor__field wdpsn-note-popup__container__content__note-editor__content-field">
		<textarea class="wdpsn_rich_editor" name="wdpsn_edit_note_content"><?php 
echo  esc_html( $note['content'] ) ;
?></textarea>
	</div>

	<?php 
if ( true === WDPSN_Config::get( 'display-additional-notices' ) ) {
    WDPSN_Helper::display_additional_notices();
}
?>

	<button type="button" class="button button-primary button-large" data-action="wdpsn-edit-note" data-caller-type="<?php 
echo  esc_attr( ( false !== $caller_type ? $caller_type : 'popup' ) ) ;
?>"><?php 
echo  esc_html( __( 'Save changes', 'super-notes' ) ) ;
?></button>
</div>
