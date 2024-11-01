<?php

/**
 * Manage single notes
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Single notes class
 */
abstract class WDPSN_SingleNotes
{
    /**
     * Add new note
     *
     * @param string $note_type_id Note Type ID.
     * @param array  $parent Note parent.
     * @param array  $data Note data.
     */
    public static function add( $note_type_id, $parent, $data )
    {
        $note_input_data = array(
            'post_content' => WDPSN_Helper::strip_and_balance_tags( $data['content'] ),
            'post_parent'  => esc_attr( $note_type_id ),
            'post_status'  => 'publish',
            'post_type'    => 'wdpsn_single_note',
            'meta_input'   => array(
            'wdpsn_parent_type' => esc_attr( $parent['type'] ),
            'wdpsn_parent_id'   => esc_attr( $parent['id'] ),
        ),
        );
        $note_id = wp_insert_post( $note_input_data );
        if ( is_wp_error( $note_id ) ) {
            return false;
        }
        unset( $note_input_data );
        $record_message = '.';
        WDPSN_History::add_record( $note_id, '{user} ' . esc_html( __( 'created this note', 'super-notes' ) ) . $record_message );
        return $note_id;
    }
    
    /**
     * Edit note
     *
     * @param array $data Note data.
     */
    public static function edit( $data )
    {
        $note_origin = self::get_single_note( $data['note_id'] );
        $note_input_data = array(
            'ID'           => $data['note_id'],
            'post_content' => WDPSN_Helper::strip_and_balance_tags( $data['content'] ),
        );
        $note_id = wp_update_post( $note_input_data );
        if ( is_wp_error( $note_id ) ) {
            return false;
        }
        unset( $note_input_data );
        $final_message = '';
        $messages = array();
        $changed = array(
            'content' => WDPSN_Helper::strip_and_balance_tags( $data['content'] ) !== $note_origin['content'],
        );
        if ( true === $changed['content'] ) {
            $messages[] = 'content';
        }
        
        if ( array() === $messages ) {
            $final_message = '{user} ' . esc_html( __( 'updated this note.', 'super-notes' ) );
        } else {
            $base = esc_html( __( 'updated', 'super-notes' ) ) . ' ';
            $final_message = '{user} ' . $base . self::join_messages( $messages ) . ' ' . esc_html( __( 'for this note.', 'super-notes' ) );
        }
        
        WDPSN_History::add_record( $note_id, $final_message );
        return true;
    }
    
    /**
     * Delete single note
     *
     * @param string $note_id Note ID.
     */
    public static function delete( $note_id )
    {
        $note_data = self::get_single_note( $note_id );
        $result = wp_delete_post( $note_id, true );
        return ( false === $result ? false : true );
    }
    
    /**
     * Join messages
     *
     * @param array $messages Messages array.
     */
    public static function join_messages( $messages )
    {
        if ( 1 === count( $messages ) ) {
            return $messages[0];
        }
        $commas_count = count( $messages ) - 1;
        $last_message = $messages[$commas_count];
        $is_and = false !== strpos( $last_message, esc_html( __( 'and', 'super-notes' ) ) );
        $output = '';
        for ( $i = 0 ;  $i < $commas_count ;  $i++ ) {
            $output .= $messages[$i] . ', ';
        }
        $output .= (( $is_and ? '' : esc_html( __( 'and', 'super-notes' ) ) . ' ' )) . $last_message;
        return $output;
    }
    
    /**
     * Get all single notes data
     *
     * @param array $parent Note parent.
     */
    public static function get_notes( $parent )
    {
        $notes = get_posts( array(
            'post_type'      => 'wdpsn_single_note',
            'posts_per_page' => -1,
        ) );
        $result = array();
        $note_types = WDPSN_DataCache::get_note_types();
        foreach ( $notes as $single_note ) {
            $note = array(
                'id'           => $single_note->ID,
                'content'      => $single_note->post_content,
                'note_type_id' => wp_get_post_parent_id( $single_note->ID ),
            );
            $meta = self::get_single_note_meta( $single_note->ID );
            
            if ( $parent['type'] === $meta['parent_type'] && (int) $parent['id'] === (int) $meta['parent_id'] ) {
                $note['note_type'] = ( isset( $note_types[$note['note_type_id']] ) ? $note_types[$note['note_type_id']]['post_title'] : '' );
                $note['style'] = WDPSN_Helper::get_single_note_style( $note_types, $note['note_type_id'] );
                $result[] = $note;
                unset( $note, $meta );
            }
        
        }
        wp_reset_postdata();
        return $result;
    }
    
    /**
     * Get single note data
     *
     * @param string $note_id Note ID.
     */
    public static function get_single_note( $note_id )
    {
        $note = get_post( $note_id );
        if ( false === $note || null === $note ) {
            return false;
        }
        return array(
            'id'           => $note->ID,
            'content'      => $note->post_content,
            'note_type_id' => $note->post_parent,
            'data'         => self::get_single_note_meta( $note_id ),
        );
    }
    
    /**
     * Get single note meta data
     *
     * @param string $note_id Note ID.
     */
    public static function get_single_note_meta( $note_id )
    {
        $note_meta = get_post_meta( $note_id );
        $result = array(
            'parent_type' => ( isset( $note_meta['wdpsn_parent_type'] ) && isset( $note_meta['wdpsn_parent_type'][0] ) ? $note_meta['wdpsn_parent_type'][0] : false ),
            'parent_id'   => ( isset( $note_meta['wdpsn_parent_id'] ) && isset( $note_meta['wdpsn_parent_id'][0] ) ? $note_meta['wdpsn_parent_id'][0] : false ),
        );
        return $result;
    }
    
    /**
     * Display single note
     *
     * @param array   $note Note data.
     * @param array   $availability Availability details.
     * @param boolean $return Whether return or echo the result.
     * @param boolean $display_assignment_data Whether display assignment data or not.
     */
    public static function display_note(
        $note,
        $availability,
        $return = false,
        $display_assignment_data = false
    )
    {
        
        if ( isset( $note['ID'] ) ) {
            $note['id'] = $note['ID'];
            unset( $note['ID'] );
        }
        
        if ( true === $return ) {
            ob_start();
        }
        $atts = array(
            'class'             => 'wdpsn-note-container wdpsn-note-container-id-' . esc_attr( $note['note_type_id'] ) . ' wdpsn-note-container--' . esc_attr( $note['style'] ) . ' wdpsn-note-container--wrapped',
            'data-note-id'      => esc_attr( $note['id'] ),
            'data-note-type-id' => esc_attr( $note['note_type_id'] ),
        );
        
        if ( true === $display_assignment_data ) {
            $atts['data-assigned-to-element-id'] = esc_attr( $note['parent_id'] );
            $atts['data-assigned-to-element-type'] = esc_attr( $note['parent_type'] );
        }
        
        $kses_atts = array(
            'div' => array(
            'class'                         => array(),
            'data-note-id'                  => array(),
            'data-note-type-id'             => array(),
            'data-assigned-to-element-id'   => array(),
            'data-assigned-to-element-type' => array(),
        ),
        );
        ?>
		<div class="wdpsn-note-wrapper" data-note-id="<?php 
        echo  esc_attr( $note['id'] ) ;
        ?>">

			<?php 
        echo  wp_kses( WDPSN_Helper::display_atts( $atts, 'div' ), $kses_atts ) ;
        ?>

				<div class="wdpsn-note-container__content">

					<div class="wdpsn-note-container__content__wrapper">
						<?php 
        echo  wp_kses_post( apply_filters( 'wdpsn_note_content', $note['content'] ) ) ;
        ?>
					</div>

					<?php 
        ?>

				</div>

				<div class="wdpsn-note-container__footer">

					<span class="wdpsn-note-container__footer__note-type"><?php 
        echo  wp_kses( $note['note_type'], array(
            'span' => array(
            'class' => array(),
        ),
        ) ) ;
        ?></span>

					<?php 
        
        if ( 'deleted' === $note['style'] || true === WDPSN_Helper::has_permission( $availability, 'own', $note['note_type_id'] ) ) {
            ?>
						<span class="wdpsn-note-container__footer__note-options">

							<?php 
            
            if ( 'deleted' !== $note['style'] ) {
                ?>
								<span data-action="edit"><?php 
                echo  esc_html( __( 'Edit', 'super-notes' ) ) ;
                ?></span>
								<?php 
            }
            
            ?>

							<span data-action="delete"><?php 
            echo  esc_html( __( 'Delete', 'super-notes' ) ) ;
            ?></span>

						</span>
						<?php 
        }
        
        ?>

				</div>

			</div>

			<div class="wdpsn-note-history">

				<?php 
        $records = WDPSN_History::get_records( $note['id'] );
        krsort( $records );
        foreach ( $records as $record_date => $record_content ) {
            ?>
					<div class="wdpsn-note-history__record">
						<span class="wdpsn-note-history__record__date"><?php 
            echo  esc_html( WDPSN_History::prepare( 'date', $record_date ) ) ;
            ?></span>
						<span class="wdpsn-note-history__record__content">
							<?php 
            echo  wp_kses( WDPSN_History::prepare( 'content', $record_content ), array(
                'strong' => array(),
                'span'   => array(
                'class' => array(),
            ),
            ) ) ;
            ?>
						</span>
					</div>
					<?php 
        }
        ?>

			</div>

		</div>
		<?php 
        
        if ( true === $return ) {
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        }
    
    }
    
    /**
     * Display preview of notes assigned to location
     *
     * @param array   $location Note location.
     * @param array   $availability Availability details.
     * @param boolean $primary_button Whether present primary or secondary button.
     * @param array   $messages Additional messages to be displayed.
     */
    public static function display_notes_preview(
        $location,
        $availability,
        $primary_button = false,
        $messages = array()
    )
    {
        if ( is_array( $messages ) && array() !== $messages ) {
            WDPSN_Helper::display_messages( $messages );
        }
        $notes = self::get_notes( $location );
        $any_displayed = false;
        if ( is_array( $notes ) && count( $notes ) > 0 ) {
            foreach ( $notes as $note ) {
                if ( false === WDPSN_Helper::has_permission( $availability, 'view', $note['note_type_id'] ) ) {
                    continue;
                }
                $note = array_merge( $note, array(
                    'parent_id'   => $location['id'],
                    'parent_type' => $location['type'],
                ) );
                self::display_note(
                    $note,
                    $availability,
                    false,
                    true !== $primary_button
                );
                if ( false === $any_displayed ) {
                    $any_displayed = true;
                }
            }
        }
        if ( false === $any_displayed ) {
            self::display_placeholder( 'no_notes_yet' );
        }
        
        if ( WDPSN_Helper::has_permission( $availability, 'own' ) ) {
            ?>
			<button type="button" class="button button-<?php 
            echo  esc_attr( ( true === $primary_button ? 'primary' : 'secondary' ) ) ;
            ?> button-large" data-action="wdpsn-open-add-note-form">
				<?php 
            echo  esc_html( __( 'Add new note', 'super-notes' ) ) ;
            ?>
			</button>
			<?php 
        }
    
    }
    
    /**
     * Display placeholder for meta box
     *
     * @param string $reason Placeholder reason.
     */
    public static function display_placeholder( $reason )
    {
        ?>
		<div class="wdpsn-empty-placeholder"><p>
		<?php 
        switch ( $reason ) {
            case 'no_location':
                echo  esc_html( __( 'Couldn\'t find location identifier.', 'super-notes' ) ) ;
                break;
            case 'unavailable':
                echo  esc_html( __( 'Notes are not available for this element.', 'super-notes' ) ) ;
                break;
            case 'no_notes_yet':
                echo  esc_html( __( 'There\'s no any notes yet.', 'super-notes' ) ) ;
                break;
            case 'new_item':
                $post_type = get_post_type();
                $success = false;
                
                if ( false !== $post_type ) {
                    $post_type = get_post_type_object( $post_type );
                    
                    if ( null !== $post_type ) {
                        /* Translators: Post type singular name. */
                        echo  esc_html( sprintf( __( 'Save or publish this %s to manage notes.', 'super-notes' ), $post_type->labels->singular_name ) ) ;
                        $success = true;
                    }
                
                }
                
                if ( false === $success ) {
                    echo  esc_html( __( 'Save or publish to manage notes.', 'super-notes' ) ) ;
                }
                break;
        }
        ?>
		</p></div>
		<?php 
    }

}