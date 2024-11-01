<?php
/**
 * Popup template: Error message
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wdpsn-note-popup__container__content__heading">

	<h1><?php echo esc_html( __( 'Something went wrong!', 'super-notes' ) ); ?></h1>
	<span class="wdpsn-note-popup__close">×</span>

</div>

<p><?php echo esc_html( __( 'Unexpecter error appeared, please try again later.', 'super-notes' ) ); ?></p>
