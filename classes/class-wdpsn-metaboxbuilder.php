<?php

/**
 * Parent for custom metaboxes
 *
 * @package super_notes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
add_action( 'init', array( 'WDPSN_MetaBoxBuilder', 'init' ) );
/**
 * Metabox parent class
 */
abstract class WDPSN_MetaBoxBuilder
{
    /**
     * Metabox options key
     *
     * @var string Metabox key.
     */
    public  $metabox_key = '' ;
    /**
     * Metabox title
     *
     * @var string Metabox title.
     */
    public  $metabox_title = '' ;
    /**
     * Depended fields tracker
     *
     * @var array Depended fields tracker.
     */
    public  $depended_fields_tracker = array() ;
    /**
     * Custom variables to add to JavaScript
     *
     * @var array Variables to add to JS.
     */
    public static  $js_localize = array() ;
    /**
     * Get custom post types array
     */
    public static function get_cpt_array()
    {
        return array( 'wdpsn_note_types', 'wdpsn_note_tag' );
    }
    
    /**
     * Register metaboxes
     */
    public static function init()
    {
        add_action( 'wp_ajax_wdpsn_update_additional_description', array( get_class(), 'update_additional_description' ) );
        add_action( 'post_submitbox_start', array( get_class(), 'register_nonce' ) );
        foreach ( WDPSN_PostTypes::get_custom_post_meta_options() as $metabox ) {
            require_once dirname( WDPSN_MAIN_FILE ) . '/classes/class-wdpsn-' . esc_attr( str_replace( '_', '', $metabox ) ) . 'metabox.php';
            $class = WDPSN_Helper::generate_class_name( $metabox, 'WDPSN_', 'MetaBox' );
            $object = new $class();
            add_action( 'add_meta_boxes', array( $object, 'add_metabox' ) );
            add_action( 'save_post', array( $object, 'update_metabox' ) );
            unset( $class, $object );
        }
    }
    
    /**
     * Add custom nonce to be checked by metaboxes, for custom post types
     *
     * @param object $post Currently edited post.
     */
    public static function register_nonce( $post )
    {
        if ( in_array( $post->post_type, self::get_cpt_array(), true ) ) {
            wp_nonce_field( $post->post_type, 'wdpsn_nonce' );
        }
    }
    
    /**
     * Validate data insertion
     *
     * @param mixed $data Data to be validated.
     */
    public function validate_insertion( $data )
    {
        if ( isset( $data['can_everyone'] ) && 'yes' === $data['can_everyone'] ) {
            unset(
                $data['who_can'],
                $data['can_owners_only'],
                $data['can_anyone_known'],
                $data['known_users']
            );
        }
        if ( isset( $data['can_owners_only'] ) && 'yes' === $data['can_owners_only'] ) {
            unset( $data['who_can'] );
        }
        if ( isset( $data['everywhere'] ) && 'yes' === $data['everywhere'] ) {
            unset( $data['custom_rules'] );
        }
        if ( isset( $data['can_anyone_known'] ) ) {
            switch ( $data['can_anyone_known'] ) {
                case 'yes':
                    unset( $data['who_can'] );
                    break;
                case 'no':
                    unset( $data['known_users'] );
                    break;
            }
        }
        return $this->secure_input( $data );
    }
    
    /**
     * Secure input
     *
     * @param mixed $data Data to be escaped.
     */
    public function secure_input( $data )
    {
        
        if ( !is_array( $data ) ) {
            return esc_attr( $data );
        } else {
            foreach ( $data as $key => $value ) {
                $data[$key] = $this->secure_input( $value );
            }
        }
        
        return $data;
    }
    
    /**
     * Get localization strings for metaboxes
     */
    public static function get_localized_strings()
    {
        return self::$js_localize;
    }
    
    /**
     * Get field name
     *
     * @param string $group Group name.
     * @param string $key Key name.
     */
    private function get_field_name( $group, $key )
    {
        return $group . '[' . esc_attr( $key ) . ']';
    }
    
    /**
     * Get field value
     *
     * @param string $key Field key.
     * @param array  $values Possible values.
     * @param mixed  $default Default value.
     */
    private function get_field_value( $key, $values, $default )
    {
        if ( '' === $values || false === $values || null === $values || !is_array( $values ) ) {
            return $default;
        }
        return ( isset( $values[esc_attr( $key )] ) ? $values[esc_attr( $key )] : $default );
    }
    
    /**
     * Render metabox fields
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $fields Fields to be rendered.
     */
    public function render_fields( $group, $values, $fields )
    {
        if ( !is_array( $values ) ) {
            $values = json_decode( $values, true );
        }
        foreach ( $fields as $field ) {
            ?>
			<div class="<?php 
            echo  esc_attr( $this->get_single_field_class( $field, $fields, $values ) ) ;
            ?>" data-field="<?php 
            echo  esc_attr( $field['field'] ) ;
            ?>" data-dependency-rules='<?php 
            echo  ( isset( $field['dependency'] ) && is_array( $field['dependency'] ) ? wp_json_encode( $field['dependency'], JSON_HEX_APOS ) : '' ) ;
            ?>'>

				<div class="wdpsn-meta-box-single-field__description">
					<h4><?php 
            echo  esc_html( $field['title'], 'super-notes' ) ;
            ?></h4>
					<p><?php 
            echo  esc_html( $field['description'], 'super-notes' ) ;
            ?></p>
				</div>

				<div class="wdpsn-meta-box-single-field__input">
					<?php 
            switch ( $field['type'] ) {
                case 'select':
                    $this->render_select_field( $group, $values, $field );
                    break;
                case 'yes-no':
                    $this->render_yes_no_field( $group, $values, $field );
                    break;
                case 'rules':
                    $this->render_custom_rules_field( $group, $values, $field );
                    break;
                case 'note_type_colors':
                    $this->render_note_type_colors_field( $group, $values, $field );
                    break;
                case 'colorpicker':
                    $this->render_colorpicker_field( $group, $values, $field );
                    break;
                case 'tag_preview':
                    break;
            }
            ?>
				</div>

			</div>
			<?php 
        }
    }
    
    /**
     * Render select field
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $field Field to be rendered.
     */
    public function render_select_field( $group, $values, $field )
    {
        global  $post ;
        $post_id = '';
        if ( null !== $post && isset( $post->ID ) ) {
            $post_id = $post->ID;
        }
        $value = $this->get_field_value( $field['field'], $values, $field['default'] );
        ?>
		<div class="wdpsn-meta-box-single-field__input__select">

			<select name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>">
				<?php 
        foreach ( $field['values'] as $option_value => $option_name ) {
            ?>
					<option value="<?php 
            echo  esc_html( $option_value ) ;
            ?>" <?php 
            selected( $value, $option_value );
            ?>><?php 
            echo  esc_html( $option_name ) ;
            ?></option>
					<?php 
        }
        ?>
			</select>

		</div>
		<?php 
        
        if ( 'wdpsn_note_types_styles' === $group && 'color_scheme' === $field['field'] ) {
            ?>
			<div class="wdpsn-note-container<?php 
            echo  esc_attr( ( '' !== $post_id ? ' wdpsn-note-container-id-' . $post_id : '' ) ) ;
            ?> wdpsn-note-container--preview wdpsn-note-container--<?php 
            echo  esc_attr( $value ) ;
            ?>">
				<div class="wdpsn-note-container__content">
					<div class="wdpsn-note-container__content__wrapper">
						<p><?php 
            echo  esc_html( __( 'This is an example preview - all single notes in this type will be styled in this colors. You can choose from four built-in different color schemes, or create new, custom ones with colorpicker below (choose the "Custom" option first in the field above).', 'super-notes' ) ) ;
            ?></p>
					</div>
				</div>
			</div>
			<?php 
        }
    
    }
    
    /**
     * Render "yes / no" field
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $field Field to be rendered.
     */
    public function render_yes_no_field( $group, $values, $field )
    {
        $value = $this->get_field_value( $field['field'], $values, $field['default'] );
        $data_set = str_replace( 'wdpsn_note_types_', '', $this->metabox_key );
        ?>
		<div class="wdpsn-meta-box-single-field__input__yes-no" data-value="<?php 
        echo  esc_attr( $value ) ;
        ?>" data-set="<?php 
        echo  esc_attr( $data_set ) ;
        ?>">

			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>" value="<?php 
        echo  esc_attr( $value ) ;
        ?>" />

			<div class="wdpsn-meta-box-single-field__input__yes-no__handle"></div>
			<div class="wdpsn-meta-box-single-field__input__yes-no__yes-text"><?php 
        echo  esc_html( $field['labels']['yes'] ) ;
        ?></div>
			<div class="wdpsn-meta-box-single-field__input__yes-no__no-text"><?php 
        echo  esc_html( $field['labels']['no'] ) ;
        ?></div>

		</div>
		<?php 
        
        if ( isset( $field['additional-description'] ) && !empty($field['additional-description']) ) {
            ?>
			<div class="wdpsn-meta-box-single-field__input__additional-description">
				<p><?php 
            echo  wp_kses( $field['additional-description'], array(
                'span' => array(),
            ) ) ;
            ?></p>
			</div>
			<?php 
        }
    
    }
    
    /**
     * Render custom rules field
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $field Field to be rendered.
     */
    public function render_custom_rules_field( $group, $values, $field )
    {
        $field_values = ( isset( $values[esc_attr( $field['field'] )] ) ? $values[esc_attr( $field['field'] )] : false );
        $field_name = $this->get_field_name( $group, $field['field'] );
        ?>
		<div class="wdpsn-meta-box-single-field__input__rules" data-set="<?php 
        echo  esc_attr( $field['data-set'] ) ;
        ?>" data-field-name="<?php 
        echo  esc_attr( $field_name ) ;
        ?>">

			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[next-rule-id]" value="<?php 
        echo  esc_attr( $this->get_field_value( 'next-rule-id', $field_values, 2 ) ) ;
        ?>" />

			<h4><?php 
        echo  esc_html( $field['labels']['allow-if'] ) ;
        ?></h4>
			<div class="wdpsn-meta-box-single-field__input__rules__rows-groups">

				<?php 
        
        if ( !isset( $field_values['rules'] ) || !is_array( $field_values['rules'] ) || count( $field_values['rules'] ) < 1 ) {
            ?>
					<div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group">
						<div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group__container">
							<?php 
            $this->render_single_rule_field(
                $field_name . '[rules]',
                '1',
                false,
                $field['data-set']
            );
            ?>
						</div>
						<button class="button button-secondary" type="button" data-rule-type="and"><?php 
            echo  esc_html( __( 'And', 'super-notes' ) ) ;
            ?></button>
					</div>
					<?php 
        } else {
            $is_first_group = true;
            foreach ( WDPSN_Helper::prepare_rules_groups( $field_values['rules'] ) as $rule_group ) {
                
                if ( true === $is_first_group ) {
                    $is_first_group = false;
                } else {
                    ?>
							<p><?php 
                    echo  esc_html( __( 'or', 'super-notes' ) ) ;
                    ?></p>
							<?php 
                }
                
                ?>
						<div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group<?php 
                echo  esc_attr( ( count( $rule_group ) > 1 ? ' wdpsn-meta-box-single-field__input__rules__rows-groups__group--multiple' : '' ) ) ;
                ?>"><div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group__container">
						<?php 
                foreach ( $rule_group as $rule_id => $rule_data ) {
                    $this->render_single_rule_field(
                        $field_name . '[rules]',
                        $rule_id,
                        $rule_data,
                        $field['data-set']
                    );
                }
                ?>
						</div><button class="button button-secondary" type="button" data-rule-type="and"><?php 
                echo  esc_html( __( 'And', 'super-notes' ) ) ;
                ?></button></div>
						<?php 
            }
        }
        
        ?>
			</div>

			<div class="wdpsn-meta-box-single-field__input__rules__buttons">
				<button class="button button-secondary" type="button" data-rule-type="and"><?php 
        echo  esc_html( __( 'And', 'super-notes' ) ) ;
        ?></button>
				<button class="button button-secondary" type="button" data-rule-type="or"><?php 
        echo  esc_html( __( 'Or', 'super-notes' ) ) ;
        ?></button>
			</div>

		</div>
		<?php 
        
        if ( isset( $field['labels']['additional-description'] ) && true === $field['labels']['additional-description'] ) {
            ?>
			<div class="wdpsn-meta-box-single-field__input__additional-description">
				<p><?php 
            echo  esc_html( __( 'Calculating...', 'super-notes' ) ) ;
            ?></p>
			</div>
			<?php 
        }
    
    }
    
    /**
     * Render note type colors field
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $field Field to be rendered.
     */
    public function render_note_type_colors_field( $group, $values, $field )
    {
        $values = $this->get_field_value( $field['field'], $values, array(
            'main-color'          => '#e73a30',
            'border-color'        => '#f8c8c5',
            'border-bottom-color' => '#f7bab7',
            'background-color'    => '#fce9e8',
            'box-shadow-color'    => 'rgba(248,200,197,0.25)',
        ) );
        ?>
		<div class="wdpsn-meta-box-single-field__input__colorpicker">

			<input type="text" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[main-color]" value="<?php 
        echo  esc_attr( $values['main-color'] ) ;
        ?>" class="wdpsn-meta-box-single-field__input__colorpicker__field" />

			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[border-color]" value="<?php 
        echo  esc_attr( $values['border-color'] ) ;
        ?>" />
			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[border-bottom-color]" value="<?php 
        echo  esc_attr( $values['border-bottom-color'] ) ;
        ?>" />
			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[background-color]" value="<?php 
        echo  esc_attr( $values['background-color'] ) ;
        ?>" />
			<input type="hidden" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>[box-shadow-color]" value="<?php 
        echo  esc_attr( $values['box-shadow-color'] ) ;
        ?>" />

		</div>
		<?php 
    }
    
    /**
     * Render colorpicker field
     *
     * @param string $group Group name.
     * @param array  $values Fields values.
     * @param array  $field Field to be rendered.
     */
    public function render_colorpicker_field( $group, $values, $field )
    {
        $value = $this->get_field_value( $field['field'], $values, $field['default'] );
        ?>
		<div class="wdpsn-meta-box-single-field__input__colorpicker">
			<input type="text" name="<?php 
        echo  esc_attr( $this->get_field_name( $group, $field['field'] ) ) ;
        ?>" value="<?php 
        echo  esc_attr( $value ) ;
        ?>" class="wdpsn-meta-box-single-field__input__colorpicker__field" />
		</div>
		<?php 
    }
    
    /**
     * Prepare input container attributes and manage dependencies
     *
     * @param array $field Field to be displayed.
     * @param array $fields Other fields.
     * @param array $values Fields values.
     */
    public function get_single_field_class( $field, $fields, $values )
    {
        $classes = array( 'wdpsn-meta-box-single-field' );
        
        if ( isset( $field['dependency'] ) ) {
            $passed = false;
            
            if ( is_array( $values ) && isset( $values[esc_attr( $field['dependency']['field'] )] ) ) {
                
                if ( '==' === $field['dependency']['operator'] ) {
                    $passed = $values[esc_attr( $field['dependency']['field'] )] === $field['dependency']['value'];
                } else {
                    $passed = $values[esc_attr( $field['dependency']['field'] )] !== $field['dependency']['value'];
                }
            
            } else {
                foreach ( $fields as $single_field ) {
                    if ( $single_field['field'] === $field['dependency']['field'] ) {
                        
                        if ( '==' === $field['dependency']['operator'] ) {
                            $passed = $single_field['default'] === $field['dependency']['value'];
                        } else {
                            $passed = $single_field['default'] !== $field['dependency']['value'];
                        }
                    
                    }
                }
            }
            
            $passed = ( true === $passed ? ( isset( $this->depended_fields_tracker[esc_attr( $field['dependency']['field'] )] ) ? $this->depended_fields_tracker[esc_attr( $field['dependency']['field'] )] : true ) : false );
            $this->depended_fields_tracker[esc_attr( $field['field'] )] = $passed;
            if ( false === $passed ) {
                $classes[] = 'wdpsn-meta-box-single-field--dependency-hidden';
            }
        }
        
        return implode( ' ', $classes );
    }
    
    /**
     * Render single rule field
     *
     * @param array   $field Field to be displayed.
     * @param integer $rule_id Rule ID.
     * @param mixed   $rule_data Rule data.
     * @param string  $dataset Data set.
     */
    public function render_single_rule_field(
        $field,
        $rule_id,
        $rule_data = false,
        $dataset
    )
    {
        if ( false === $rule_data ) {
            $rule_data = array(
                'rule-condition' => '',
                'rule-operator'  => '',
                'rule-value'     => '',
            );
        }
        ?>
		<div class="wdpsn-meta-box-single-field__input__rules__row">
			<div class="wdpsn-meta-box-single-field__input__rules__row__col">
				<?php 
        
        if ( isset( $rule_data['rule-type'] ) ) {
            ?>
					<input type="hidden" name="<?php 
            echo  esc_attr( $field ) ;
            ?>[<?php 
            echo  esc_attr( $rule_id ) ;
            ?>][rule-type]" value="<?php 
            echo  esc_attr( $rule_data['rule-type'] ) ;
            ?>" />
					<?php 
        }
        
        ?>
				<select name="<?php 
        echo  esc_attr( $field ) ;
        ?>[<?php 
        echo  esc_attr( $rule_id ) ;
        ?>][rule-condition]">
					<?php 
        $this->render_single_rule_field_subfield_options( 'condition', $dataset, $rule_data['rule-condition'] );
        ?>
				</select>
			</div>

			<div class="wdpsn-meta-box-single-field__input__rules__row__col">
				<select name="<?php 
        echo  esc_attr( $field ) ;
        ?>[<?php 
        echo  esc_attr( $rule_id ) ;
        ?>][rule-operator]">
					<?php 
        $this->render_single_rule_field_subfield_options( 'operator', $dataset, $rule_data['rule-operator'] );
        ?>
				</select>
			</div>

			<div class="wdpsn-meta-box-single-field__input__rules__row__col">
				<select name="<?php 
        echo  esc_attr( $field ) ;
        ?>[<?php 
        echo  esc_attr( $rule_id ) ;
        ?>][rule-value]" data-selected="<?php 
        echo  esc_attr( $rule_data['rule-value'] ) ;
        ?>">
					<option value="<?php 
        echo  esc_attr( $rule_data['rule-value'] ) ;
        ?>"><?php 
        echo  esc_html( __( 'Please wait...', 'super-notes' ) ) ;
        ?></option>
				</select>
				<span class="wdpsn-meta-box-single-field__input__rules__row__remove">×</span>
			</div>
		</div>
		<?php 
    }
    
    /**
     * Render single rule field sub-field options
     *
     * @param string $subfield Subfield type.
     * @param string $dataset Data set.
     * @param string $selected Selected value.
     */
    public function render_single_rule_field_subfield_options( $subfield, $dataset, $selected )
    {
        switch ( $subfield ) {
            case 'condition':
                switch ( $dataset ) {
                    case 'owners':
                    case 'viewers':
                    case 'tag_moderators':
                        ?>
						<option value="user-role" <?php 
                        selected( $selected, 'user-role' );
                        ?>><?php 
                        echo  esc_html( __( 'User role', 'super-notes' ) ) ;
                        ?></option>
						<option value="user" <?php 
                        selected( $selected, 'user' );
                        ?>><?php 
                        echo  esc_html( __( 'User', 'super-notes' ) ) ;
                        ?></option>
						<?php 
                        break;
                    case 'locations':
                        ?>
						<option value="post-type" <?php 
                        selected( $selected, 'post-type' );
                        ?>><?php 
                        echo  esc_html( __( 'Post type', 'super-notes' ) ) ;
                        ?></option>
						<optgroup label="<?php 
                        echo  esc_html( __( 'Posts', 'super-notes' ) ) ;
                        ?>">
							<option value="post" <?php 
                        selected( $selected, 'post' );
                        ?>><?php 
                        echo  esc_html( __( 'Post', 'super-notes' ) ) ;
                        ?></option>
							<option value="post-status" <?php 
                        selected( $selected, 'post-status' );
                        ?>><?php 
                        echo  esc_html( __( 'Post status', 'super-notes' ) ) ;
                        ?></option>
							<option value="post-format" <?php 
                        selected( $selected, 'post-format' );
                        ?>><?php 
                        echo  esc_html( __( 'Post format', 'super-notes' ) ) ;
                        ?></option>
							<option value="post-taxonomy" <?php 
                        selected( $selected, 'post-taxonomy' );
                        ?>><?php 
                        echo  esc_html( __( 'Post taxonomy', 'super-notes' ) ) ;
                        ?></option>
							<option value="post-author" <?php 
                        selected( $selected, 'post-author' );
                        ?>><?php 
                        echo  esc_html( __( 'Post author', 'super-notes' ) ) ;
                        ?></option>
							<option value="post-author-role" <?php 
                        selected( $selected, 'post-author-role' );
                        ?>><?php 
                        echo  esc_html( __( 'Post author role', 'super-notes' ) ) ;
                        ?></option>
						</optgroup>
						<optgroup label="<?php 
                        echo  esc_html( __( 'Pages', 'super-notes' ) ) ;
                        ?>">
							<option value="page" <?php 
                        selected( $selected, 'page' );
                        ?>><?php 
                        echo  esc_html( __( 'Page', 'super-notes' ) ) ;
                        ?></option>
							<option value="page-template" <?php 
                        selected( $selected, 'page-template' );
                        ?>><?php 
                        echo  esc_html( __( 'Page template', 'super-notes' ) ) ;
                        ?></option>
							<option value="page-type" <?php 
                        selected( $selected, 'page-type' );
                        ?>><?php 
                        echo  esc_html( __( 'Page type', 'super-notes' ) ) ;
                        ?></option>
							<option value="page-ancestor" <?php 
                        selected( $selected, 'page-ancestor' );
                        ?>><?php 
                        echo  esc_html( __( 'Page ancestor', 'super-notes' ) ) ;
                        ?></option>
							<option value="page-parent" <?php 
                        selected( $selected, 'page-parent' );
                        ?>><?php 
                        echo  esc_html( __( 'Page parent', 'super-notes' ) ) ;
                        ?></option>
						</optgroup>
						<optgroup label="<?php 
                        echo  esc_html( __( 'Other', 'super-notes' ) ) ;
                        ?>">
							<option value="taxonomy-term" <?php 
                        selected( $selected, 'taxonomy-term' );
                        ?>><?php 
                        echo  esc_html( __( 'Taxonomy term', 'super-notes' ) ) ;
                        ?></option>
							<option value="user-role" <?php 
                        selected( $selected, 'user-role' );
                        ?>><?php 
                        echo  esc_html( __( 'User role', 'super-notes' ) ) ;
                        ?></option>
							<option value="user" <?php 
                        selected( $selected, 'user' );
                        ?>><?php 
                        echo  esc_html( __( 'User', 'super-notes' ) ) ;
                        ?></option>
							<option value="plugin" <?php 
                        selected( $selected, 'plugin' );
                        ?>><?php 
                        echo  esc_html( __( 'Plugin', 'super-notes' ) ) ;
                        ?></option>
							<option value="special-location" <?php 
                        selected( $selected, 'special-location' );
                        ?>><?php 
                        echo  esc_html( __( 'Special location', 'super-notes' ) ) ;
                        ?></option>
						</optgroup>
						<?php 
                        break;
                }
                break;
            case 'operator':
                ?>
				<option value="==" <?php 
                selected( $selected, '==' );
                ?>><?php 
                echo  esc_html( __( 'is equal to', 'super-notes' ) ) ;
                ?></option>
				<option value="!=" <?php 
                selected( $selected, '!=' );
                ?>><?php 
                echo  esc_html( __( 'is not equal to', 'super-notes' ) ) ;
                ?></option>
				<?php 
                break;
        }
    }
    
    /**
     * Get json data required by rendered fields
     */
    public static function get_json_data_for_dynamic_fields()
    {
        return 'window.wdpsnNoteTypesDataJSON = ' . wp_json_encode( WDPSN_Helper::get_possible_locations() ) . ';';
    }
    
    /**
     * Ajax call: get additional description content
     */
    public static function update_additional_description()
    {
        check_ajax_referer( 'update-additional-description', 'nonce' );
        
        if ( !isset( $_POST['rules'] ) || !isset( $_POST['rules']['owners'] ) && !isset( $_POST['rules']['viewers'] ) && !isset( $_POST['rules']['tag_moderators'] ) || !isset( $_POST['messages'] ) ) {
            echo  wp_json_encode( array(
                'status' => 'error',
            ) ) ;
            wp_die();
        }
        
        $results = array(
            'status' => 'ok',
            'result' => array(),
        );
        foreach ( array( 'owners', 'viewers', 'tag_moderators' ) as $role ) {
            if ( !isset( $_POST['rules'][$role] ) ) {
                continue;
            }
            $rule = wdpsn_sanitize_array( wp_unslash( $_POST['rules'][esc_attr( $role )] ) );
            $users = WDPSN_DataCache::get_element_allowed_users(
                $role,
                false,
                $rule,
                ( 'viewers' === $role ? $results['result']['owners']['users'] : false )
            );
            $users_count = count( $users );
            $role = str_replace( '-', '_', $role );
            $m_noone = wp_kses( wp_unslash( $_POST['messages'][esc_attr( $role ) . '_no_one'] ), array(
                'span' => array(),
            ) );
            $m_one = wp_kses( wp_unslash( $_POST['messages'][esc_attr( $role ) . '_one'] ), array(
                'span' => array(),
            ) );
            $m_multiple = wp_kses( wp_unslash( $_POST['messages'][esc_attr( $role ) . '_multiple'] ), array(
                'span' => array(),
            ) );
            $results['result'][esc_attr( $role )] = array(
                'message' => '<p>' . wp_kses( ( 0 === $users_count ? $m_noone : (( 1 === $users_count ? $m_one : sprintf( $m_multiple, $users_count ) )) ), array(
                'span' => '',
            ) ) . '</p>',
                'users'   => $users,
            );
        }
        echo  wp_json_encode( $results ) ;
        wp_die();
    }

}