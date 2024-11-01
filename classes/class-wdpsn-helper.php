<?php

/**
 * Helper functions used by other plugin elements
 *
 * @package super_notes
 */
add_action( 'init', array( 'WDPSN_Helper', 'register_notes_content_filter' ) );
/**
 * Helper class
 */
abstract class WDPSN_Helper
{
    /**
     * Get plugin URI
     *
     * @param string $plugin_file Plugin file.
     */
    public static function get_plugin_data( $plugin_file )
    {
        $path = str_replace( 'super-notes/', '', plugin_dir_path( WDPSN_MAIN_FILE ) );
        return get_plugin_data( $path . $plugin_file );
    }
    
    /**
     * Prepare rules groups and walk for each
     *
     * @param array $rows Group rows.
     */
    public static function prepare_rules_groups( $rows )
    {
        $groups = array();
        $current_group_id = 0;
        foreach ( $rows as $row_id => $row_data ) {
            
            if ( isset( $row_data['rule-type'] ) && 'and' === $row_data['rule-type'] ) {
                $groups[esc_attr( $current_group_id )][esc_attr( $row_id )] = $row_data;
            } else {
                $current_group_id++;
                $groups[esc_attr( $current_group_id )][esc_attr( $row_id )] = $row_data;
            }
        
        }
        return $groups;
    }
    
    /**
     * Flatten given array
     *
     * @param string $type Array data type.
     * @param array  $array Data array.
     * @param string $add_before Additional text to add before array element.
     */
    public static function flatten_array( $type, $array, $add_before = '' )
    {
        $result = array();
        switch ( $type ) {
            case 'user':
                foreach ( $array as $element ) {
                    $result[esc_attr( $element['id'] )] = esc_html( $element['name'] );
                }
                break;
            case 'post-type':
                foreach ( $array as $element ) {
                    $result[esc_attr( $element->name )] = esc_html( $element->label ) . ' (' . esc_html( __( 'post type:', 'super-notes' ) ) . ' &quot;' . esc_html( $element->name ) . '&quot;)';
                }
                break;
            case 'taxonomy-term':
                foreach ( $array as $element ) {
                    if ( 'link_category' === $element->name ) {
                        continue;
                    }
                    $result[esc_attr( $element->name )] = esc_html( $element->label );
                }
                break;
            case 'post-format':
                if ( !is_array( $array ) || !isset( $array[0] ) || !is_array( $array[0] ) ) {
                    return array();
                }
                foreach ( $array[0] as $element ) {
                    $result[esc_attr( $element )] = get_post_format_string( $element );
                }
                break;
            case 'page':
                foreach ( $array as $element ) {
                    $result[esc_attr( $element['page']['id'] )] = esc_html( ( !empty($add_before) ? $add_before . ' ' : '' ) ) . $element['page']['title'];
                    if ( is_array( $element['childs'] ) && count( $element['childs'] ) > 0 ) {
                        foreach ( self::flatten_array( 'page', $element['childs'], $add_before . '-' ) as $child_key => $child_name ) {
                            $result[$child_key] = $child_name;
                        }
                    }
                }
                break;
        }
        return $result;
    }
    
    /**
     * Get detailed data about possible note location
     */
    public static function get_possible_locations()
    {
        global  $wp_roles ;
        $user_roles = ( null === $wp_roles ? new WP_Roles() : $wp_roles );
        $post_types = self::flatten_array( 'post-type', get_post_types( array(
            'show_ui' => true,
        ), 'objects' ) );
        $all_user_roles = $user_roles->get_names();
        $all_users = self::flatten_array( 'user', WDPSN_DataCache::get_all_users() );
        return self::update_empty_arrays( array(
            'user-role'        => $all_user_roles,
            'user'             => $all_users,
            'post-type'        => $post_types,
            'post'             => self::get_all_posts( array_keys( $post_types ) ),
            'post-status'      => array_merge(
            get_post_statuses(),
            array(
            'password-protected' => esc_html( __( 'Password protected', 'super-notes' ) ),
        ),
            array(
            'scheduled' => esc_html( __( 'Scheduled', 'super-notes' ) ),
        ),
            array(
            'sticky' => esc_html( __( 'Sticky', 'super-notes' ) ),
        ),
            array(
            'in_trash' => esc_html( __( 'In trash', 'super-notes' ) ),
        )
        ),
            'post-format'      => self::flatten_array( 'post-format', get_theme_support( 'post-formats' ) ),
            'post-taxonomy'    => self::get_taxonomies(),
            'post-author'      => $all_users,
            'post-author-role' => $all_user_roles,
            'page'             => self::flatten_array( 'page', self::get_all_pages() ),
            'page-template'    => self::get_page_templates(),
            'page-type'        => array(
            'front-page'     => esc_html( __( 'Front page', 'super-notes' ) ),
            'posts-page'     => esc_html( __( 'Posts page', 'super-notes' ) ),
            'top-level-page' => esc_html( __( 'Top level page', 'super-notes' ) ),
            'parent-page'    => esc_html( __( 'Parent page (has children)', 'super-notes' ) ),
            'child-page'     => esc_html( __( 'Child page (has parent)', 'super-notes' ) ),
        ),
            'page-ancestor'    => self::flatten_array( 'page', self::get_all_pages() ),
            'page-parent'      => self::flatten_array( 'page', self::get_all_pages() ),
            'taxonomy-term'    => self::flatten_array( 'taxonomy-term', get_taxonomies( array(
            'show_ui' => true,
        ), 'objects' ) ),
            'plugin'           => self::get_all_plugins(),
            'special-location' => array(
            'global' => esc_html( __( 'Global', 'super-notes' ) ),
            'plugin' => esc_html( __( 'Plugins page', 'super-notes' ) ),
        ),
        ) );
    }
    
    /**
     * Get all posts from all registered posts types
     *
     * @param array $post_types Registered post types.
     */
    public static function get_all_posts( $post_types )
    {
        $results = array();
        foreach ( $post_types as $post_type ) {
            if ( 'page' === $post_type ) {
                continue;
            }
            $all_posts = get_posts( array(
                'numberposts' => -1,
                'post_type'   => $post_type,
            ) );
            
            if ( count( $all_posts ) > 0 ) {
                $posts_group = array();
                foreach ( $all_posts as $single_post ) {
                    $posts_group[esc_attr( $single_post->ID )] = $single_post->post_title . (( 'attachment' === $post_type ? ' (id: ' . esc_html( $single_post->ID ) . ')' : '' ));
                }
                $results[esc_attr( $post_type )] = $posts_group;
            }
            
            unset( $all_posts, $posts_group, $single_post );
            wp_reset_postdata();
        }
        return $results;
    }
    
    /**
     * Get all pages and respect parent and child relations
     *
     * @param string $post_parent Post parent.
     */
    public static function get_all_pages( $post_parent = '' )
    {
        $pages = get_posts( array(
            'numberposts' => -1,
            'post_type'   => 'page',
            'post_parent' => $post_parent,
        ) );
        $results = array();
        foreach ( $pages as $page ) {
            if ( $post_parent !== $page->post_parent ) {
                continue;
            }
            $results[] = array(
                'page'   => array(
                'id'    => $page->ID,
                'title' => $page->post_title,
            ),
                'childs' => self::get_all_pages( $page->ID ),
            );
        }
        unset( $pages, $page );
        wp_reset_postdata();
        return $results;
    }
    
    /**
     * Get all registered taxonomies and its values
     */
    public static function get_taxonomies()
    {
        $taxonomies = get_taxonomies( array(
            'name'    => true,
            'show_ui' => true,
        ), 'object' );
        $results = array();
        foreach ( $taxonomies as $taxonomy ) {
            if ( 'link_category' === $taxonomy->name ) {
                continue;
            }
            $group = array();
            $terms = get_terms( array(
                'taxonomy'   => esc_attr( $taxonomy->name ),
                'hide_empty' => false,
            ) );
            foreach ( $terms as $term ) {
                $group[esc_attr( $term->term_id )] = $term->name;
            }
            $results[esc_attr( $taxonomy->name )] = $group;
        }
        return $results;
    }
    
    /**
     * Get all page templates
     */
    public static function get_page_templates()
    {
        $templates = get_page_templates();
        $results = array(
            'default' => esc_html( __( 'Default template', 'super-notes' ) ),
        );
        foreach ( $templates as $template_name => $template_filename ) {
            $results[esc_attr( $template_filename )] = $template_name;
        }
        return $results;
    }
    
    /**
     * Get all plugins
     */
    public static function get_all_plugins()
    {
        $plugins = get_plugins();
        $results = array();
        
        if ( is_array( $plugins ) && array() !== $plugins ) {
            foreach ( $plugins as $plugin_key => $plugin_data ) {
                $results[esc_attr( $plugin_key )] = $plugin_data['Name'];
            }
            unset( $plugins, $plugin_key, $plugin_data );
        }
        
        return $results;
    }
    
    /**
     * Update empty arrays
     *
     * @param array $array Multi-dimensional array to be updated.
     */
    public static function update_empty_arrays( $array )
    {
        foreach ( $array as $row_key => $row_data ) {
            if ( is_array( $row_data ) && 0 === count( $row_data ) ) {
                $array[$row_key] = array(
                    '' => esc_html( __( 'None found...', 'super-notes' ) ),
                );
            }
        }
        return $array;
    }
    
    /**
     * Get meta data created by this plugin only
     *
     * @param array  $meta Meta data.
     * @param string $post_type Post type.
     */
    public static function get_own_meta_only( $meta, $post_type )
    {
        $meta_keys = WDPSN_PostTypes::get_custom_post_meta_options( $post_type . '_' );
        $results = array();
        foreach ( $meta as $single_meta_key => $single_meta_data ) {
            if ( in_array( $single_meta_key, $meta_keys, true ) ) {
                $results[$single_meta_key] = json_decode( $single_meta_data[0], true );
            }
        }
        return $results;
    }
    
    /**
     * Exclude unwanted keys from array
     *
     * @param array $array Array to exclude from.
     * @param mixed $keys Keys to be removed.
     */
    public static function array_exclude( $array, $keys )
    {
        if ( is_string( $keys ) ) {
            $keys = array( $keys );
        }
        foreach ( $array as $item_id => $item ) {
            $array[$item_id] = array_diff_key( $item, array_flip( $keys ) );
        }
        unset( $keys, $item_id, $item );
        return $array;
    }
    
    /**
     * Get only array elements that match the rules
     *
     * @param array $data Element data.
     * @param array $rules Rules to be applied.
     */
    public static function apply_rules( $data, $rules )
    {
        $results = array();
        $rules_groups = self::prepare_rules_groups( $rules );
        foreach ( $rules_groups as $rules_group ) {
            $group_result = array();
            $is_first_rule = true;
            foreach ( $rules_group as $rule ) {
                $exclude_from = ( $is_first_rule ? $data : $group_result );
                if ( array() === $exclude_from ) {
                    break;
                }
                if ( false === $is_first_rule ) {
                    $group_result = array();
                }
                foreach ( $exclude_from as $single_element_key => $single_element_data ) {
                    switch ( $rule['rule-condition'] ) {
                        case 'user':
                            if ( '==' === $rule['rule-operator'] && $single_element_data['id'] === (int) $rule['rule-value'] ) {
                                $group_result[] = $single_element_data;
                            }
                            if ( '!=' === $rule['rule-operator'] && $single_element_data['id'] !== (int) $rule['rule-value'] ) {
                                $group_result[] = $single_element_data;
                            }
                            break;
                        case 'user-role':
                            
                            if ( '==' === $rule['rule-operator'] && in_array( $rule['rule-value'], $single_element_data['roles'], true ) ) {
                                $group_result[] = $single_element_data;
                            } elseif ( '!=' === $rule['rule-operator'] && !in_array( $rule['rule-value'], $single_element_data['roles'], true ) ) {
                                $group_result[] = $single_element_data;
                            }
                            
                            break;
                    }
                }
                if ( true === $is_first_rule ) {
                    $is_first_rule = false;
                }
            }
            $results = array_merge( $results, $group_result );
            unset( $group_result );
        }
        return self::remove_duplicates( $results );
    }
    
    /**
     * Remove duplicates from array of elements that match rule
     *
     * @param array $results Array to remove duplicates from.
     */
    public static function remove_duplicates( $results )
    {
        $unique = array();
        foreach ( $results as $result ) {
            if ( !in_array( $result['id'], self::array_column( $unique, 'id' ), true ) ) {
                $unique[] = $result;
            }
        }
        unset( $results, $result );
        return $unique;
    }
    
    /**
     * Return the values from a single column in the input array
     *
     * @param array  $array Input array.
     * @param string $column Column key.
     */
    public static function array_column( $array, $column )
    {
        if ( !is_array( $array ) ) {
            return array();
        }
        if ( function_exists( 'array_column' ) ) {
            return array_column( $array, $column );
        }
        if ( array() === $array ) {
            return array();
        }
        $results = array();
        foreach ( $array as $element ) {
            $results[] = $element[esc_attr( $column )];
        }
        return $results;
    }
    
    /**
     * Check if current user can view or own notes
     *
     * @param string $user_id User ID.
     * @param string $note_type_id Note Type ID.
     */
    public static function get_user_permissions( $user_id, $note_type_id )
    {
        $permissions = WDPSN_DataCache::get_allowed_users();
        $owners = $permissions[$note_type_id]['owners'];
        $viewers = $permissions[$note_type_id]['viewers'];
        return array(
            'own'  => in_array( $user_id, self::array_column( $owners, 'id' ), true ),
            'view' => in_array( $user_id, self::array_column( $viewers, 'id' ), true ),
        );
    }
    
    /**
     * Check if given location is available for given element
     *
     * @param string $element_type Element type.
     * @param string $element_id Element ID.
     * @param array  $location Element location.
     */
    public static function is_location_available_for_element( $element_type, $element_id, $location )
    {
        if ( 'yes' === $location['everywhere'] ) {
            return true;
        }
        $terms_ids = false;
        $non_posts_locations = array(
            'plugin',
            'user',
            'category',
            'post_tag',
            'global'
        );
        
        if ( isset( $location['custom_rules'] ) && isset( $location['custom_rules']['rules'] ) ) {
            $rules_groups = self::prepare_rules_groups( $location['custom_rules']['rules'] );
            foreach ( $rules_groups as $rules_group ) {
                $rules_group_result = true;
                foreach ( $rules_group as $rule ) {
                    $passed = true;
                    $passed_type = true;
                    switch ( $rule['rule-condition'] ) {
                        // Only users with specific role.
                        case 'user-role':
                            
                            if ( 'user' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $user_data = get_userdata( $element_id );
                                $passed = in_array( $rule['rule-value'], $user_data->roles, true );
                            }
                            
                            break;
                            // Only single selected user.
                        // Only single selected user.
                        case 'user':
                            
                            if ( 'user' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $passed = $rule['rule-value'] === $element_id;
                            }
                            
                            break;
                            // Only for posts with specific type.
                        // Only for posts with specific type.
                        case 'post-type':
                            
                            if ( in_array( $element_type, array_merge( $non_posts_locations ), true ) ) {
                                $passed_type = false;
                            } else {
                                $passed = get_post_type( $element_id ) === $rule['rule-value'];
                            }
                            
                            break;
                            // Only for specific page.
                        // Only for specific page.
                        case 'page':
                            
                            if ( 'page' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $passed = $rule['rule-value'] === $element_id;
                            }
                            
                            break;
                            // Only for specific post.
                        // Only for specific post.
                        case 'post':
                            
                            if ( in_array( $element_type, array_merge( $non_posts_locations, array( 'page' ) ), true ) ) {
                                $passed_type = false;
                            } else {
                                $passed = $rule['rule-value'] === $element_id;
                            }
                            
                            break;
                            // Only for posts with specific status.
                        // Only for posts with specific status.
                        case 'post-status':
                            
                            if ( in_array( $element_type, $non_posts_locations, true ) ) {
                                $passed_type = false;
                            } else {
                                $post = get_post( $element_id );
                                switch ( $rule['rule-value'] ) {
                                    case 'password-protected':
                                        $passed = isset( $post->post_password ) && !empty($post->post_password);
                                        break;
                                    case 'scheduled':
                                        $passed = 'future' === get_post_status( $element_id );
                                        if ( false === $passed ) {
                                            if ( current_time( 'timestamp' ) < strtotime( $post->post_date ) ) {
                                                $passed = true;
                                            }
                                        }
                                        break;
                                    case 'sticky':
                                        $passed = is_sticky( $element_id );
                                        break;
                                    default:
                                        $passed = get_post_status( $element_id ) === (( 'in_trash' === $rule['rule-value'] ? 'trash' : $rule['rule-value'] ));
                                        break;
                                }
                                unset( $post );
                            }
                            
                            break;
                            // Only posts with specific format.
                        // Only posts with specific format.
                        case 'post-format':
                            
                            if ( 'post' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $passed = get_post_format( $element_id ) === $rule['rule-value'];
                            }
                            
                            break;
                            // Only for posts with specific taxonomy.
                        // Only for posts with specific taxonomy.
                        case 'post-taxonomy':
                            
                            if ( false === $terms_ids ) {
                                $terms = array(
                                    'category' => get_the_terms( $element_id, 'category' ),
                                    'post_tag' => get_the_terms( $element_id, 'post_tag' ),
                                );
                                foreach ( $terms as $term_key => $term_data ) {
                                    
                                    if ( !empty($terms[esc_attr( $term_key )]) && !is_wp_error( $terms[esc_attr( $term_key )] ) ) {
                                        if ( false === $terms_ids ) {
                                            $terms_ids = array();
                                        }
                                        foreach ( $terms[esc_attr( $term_key )] as $term ) {
                                            $terms_ids[] = (string) $term->term_id;
                                        }
                                    }
                                
                                }
                            }
                            
                            
                            if ( false === $terms_ids ) {
                                $passed_type = false;
                            } else {
                                $passed = in_array( $rule['rule-value'], $terms_ids, true );
                            }
                            
                            unset( $terms, $term_key, $term_data );
                            break;
                            // Only for posts created by specific author.
                        // Only for posts created by specific author.
                        case 'post-author':
                            
                            if ( in_array( $element_type, $non_posts_locations, true ) ) {
                                $passed_type = false;
                            } else {
                                $passed = self::get_post_author_id( $element_id ) === (string) $rule['rule-value'];
                            }
                            
                            break;
                            // Only for posts created by authors with specific role.
                        // Only for posts created by authors with specific role.
                        case 'post-author-role':
                            
                            if ( in_array( $element_type, $non_posts_locations, true ) ) {
                                $passed_type = false;
                            } else {
                                $user_data = get_userdata( self::get_post_author_id( $element_id ) );
                                $passed = in_array( $rule['rule-value'], $user_data->roles, true );
                            }
                            
                            break;
                            // Only for pages with specific template.
                        // Only for pages with specific template.
                        case 'page-template':
                            
                            if ( 'page' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $passed = get_page_template_slug( $element_id ) === $rule['rule-value'];
                            }
                            
                            break;
                            // Only for pages with specific type.
                        // Only for pages with specific type.
                        case 'page-type':
                            
                            if ( 'page' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                switch ( $rule['rule-value'] ) {
                                    case 'front-page':
                                        $passed = get_option( 'page_on_front' ) === $element_id;
                                        break;
                                    case 'posts-page':
                                        $passed = get_option( 'page_for_posts' ) === $element_id;
                                        break;
                                    case 'top-level-page':
                                        $passed = false === wp_get_post_parent_id( $element_id );
                                        break;
                                    case 'parent-page':
                                        $childs = get_pages( array(
                                            'child_of' => $element_id,
                                        ) );
                                        $passed = is_array( $childs ) && count( $childs ) > 0;
                                        unset( $childs );
                                        break;
                                    case 'child-page':
                                        $passed = false !== wp_get_post_parent_id( $element_id );
                                        break;
                                }
                            }
                            
                            break;
                            // Only for pages with specific ancestor.
                        // Only for pages with specific ancestor.
                        case 'page-ancestor':
                            
                            if ( 'page' !== $element_type ) {
                                $passed_type = false;
                            } else {
                                $passed = in_array( (int) $rule['rule-value'], get_post_ancestors( $element_id ), true );
                            }
                            
                            break;
                            // Only for pages with specific parent.
                        // Only for pages with specific parent.
                        case 'page-parent':
                            
                            if ( 'page' !== $element_type ) {
                                $rules_group_result = false;
                            } else {
                                $passed = wp_get_post_parent_id( $element_id ) === (int) $rule['rule-value'];
                            }
                            
                            break;
                            // Only for specific taxonomy term.
                        // Only for specific taxonomy term.
                        case 'taxonomy-term':
                            
                            if ( !in_array( $element_type, array( 'category', 'post_tag' ), true ) ) {
                                $rules_group_result = false;
                            } else {
                                $passed = $element_id === $rule['rule-value'];
                            }
                            
                            break;
                            // Only for special location.
                        // Only for special location.
                        case 'special-location':
                            
                            if ( !in_array( $element_type, array( 'global', 'plugin' ), true ) ) {
                                $rules_group_result = false;
                            } else {
                                $passed = $element_type === $rule['rule-value'];
                            }
                            
                            break;
                            // Only for specific plugin.
                        // Only for specific plugin.
                        case 'plugin':
                            
                            if ( 'plugin' !== $element_type ) {
                                $rules_group_result = false;
                            } else {
                                $passed = $element_id === $rule['rule-value'];
                            }
                            
                            break;
                    }
                    if ( false === $passed_type || '==' === $rule['rule-operator'] && false === $passed || '!=' === $rule['rule-operator'] && true === $passed ) {
                        $rules_group_result = false;
                    }
                }
                if ( true === $rules_group_result ) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if notes popup is available for selected element and current user
     *
     * @param string  $element_type Element type.
     * @param string  $element_id Element ID.
     * @param mixed   $user_id User ID.
     * @param boolean $check_location Whether to check location or not.
     */
    public static function get_popup_availability(
        $element_type,
        $element_id,
        $user_id = false,
        $check_location = true
    )
    {
        if ( false === $user_id ) {
            $user_id = get_current_user_id();
        }
        $available = array();
        foreach ( WDPSN_DataCache::get_note_types() as $note_type ) {
            $user_permissions = self::get_user_permissions( $user_id, $note_type['id'] );
            if ( true === $user_permissions['view'] || true === $user_permissions['own'] ) {
                
                if ( true === $check_location ) {
                    if ( isset( $note_type['post_meta'] ) && isset( $note_type['post_meta']['wdpsn_note_types_location'] ) ) {
                        if ( self::is_location_available_for_element( $element_type, $element_id, $note_type['post_meta']['wdpsn_note_types_location'] ) ) {
                            $available[$note_type['id']] = $user_permissions;
                        }
                    }
                } else {
                    $available[$note_type['id']] = $user_permissions;
                }
            
            }
        }
        return $available;
    }
    
    /**
     * Check current user permissions
     *
     * @param array  $availability Availability data.
     * @param string $permission Permission type.
     * @param string $notes_type_id Notes type ID.
     */
    public static function has_permission( $availability, $permission, $notes_type_id = 'any' )
    {
        
        if ( 'any' !== $notes_type_id ) {
            if ( !isset( $availability[esc_attr( $notes_type_id )] ) || !is_array( $availability[esc_attr( $notes_type_id )] ) ) {
                return false;
            }
            switch ( $permission ) {
                case 'own':
                case 'view':
                    return $availability[esc_attr( $notes_type_id )][esc_attr( $permission )];
                case 'any':
                    return true === $availability[esc_attr( $notes_type_id )]['own'] || true === $availability[esc_attr( $notes_type_id )]['view'];
            }
            return false;
        }
        
        foreach ( $availability as $availability_data ) {
            if ( 'own' === $permission && true === $availability_data['own'] ) {
                return true;
            }
            if ( 'view' === $permission && true === $availability_data['view'] ) {
                return true;
            }
            if ( 'any' === $permission && (true === $availability_data['view'] || true === $availability_data['own']) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Count notes available for selected parent
     *
     * @param array $availability Availability data.
     * @param array $parent Parent data.
     */
    public static function count_notes( $availability, $parent )
    {
        $notes = WDPSN_SingleNotes::get_notes( $parent );
        $notes_count = 0;
        foreach ( $notes as $note ) {
            if ( 'deleted' === $note['style'] && empty($note['note_type']) ) {
                continue;
            }
            if ( isset( $availability[$note['note_type_id']] ) && true === $availability[$note['note_type_id']]['view'] ) {
                $notes_count++;
            }
        }
        return $notes_count;
    }
    
    /**
     * Get post author ID
     *
     * @param string $post_id Post ID.
     */
    public static function get_post_author_id( $post_id )
    {
        $post = get_post( $post_id );
        return ( null !== $post ? $post->post_author : false );
    }
    
    /**
     * Get single note "parent" nice name
     *
     * @param string $element_type Element type.
     * @param string $element_id Element ID.
     */
    public static function get_single_note_parent_name( $element_type, $element_id )
    {
        if ( 'global' === $element_type ) {
            return wp_kses( __( 'For <strong>Global</strong> location', 'super-notes' ), array(
                'strong' => array(),
            ) );
        }
        $output = false;
        switch ( $element_type ) {
            case 'wdpsn_note_types':
            case 'wdpsn_single_note':
            case 'wdpsn_note_tag':
            case 'post':
                $post_type = get_post_type_object( get_post_type( $element_id ) );
                if ( null !== $post_type ) {
                    $output = array(
                        'heading' => esc_html( __( 'For', 'super-notes' ) ) . ' ' . esc_html( $post_type->labels->singular_name ) . ':',
                        'title'   => get_the_title( $element_id ),
                    );
                }
                unset( $post_type );
                break;
            case 'page':
                $output = array(
                    'heading' => esc_html( __( 'For Page:', 'super-notes' ) ),
                    'title'   => get_the_title( $element_id ),
                );
                break;
            case 'user':
                $user_data = get_userdata( $element_id );
                if ( isset( $user_data->display_name ) ) {
                    $output = array(
                        'heading' => esc_html( __( 'For User:', 'super-notes' ) ),
                        'title'   => $user_data->display_name,
                    );
                }
                unset( $user_data );
                break;
            case 'category':
                $output = array(
                    'heading' => esc_html( __( 'For Category:', 'super-notes' ) ),
                    'title'   => get_cat_name( $element_id ),
                );
                break;
            case 'post_tag':
                $term = get_term( $element_id );
                if ( isset( $term->name ) ) {
                    $output = array(
                        'heading' => esc_html( __( 'For Tag:', 'super-notes' ) ),
                        'title'   => $term->name,
                    );
                }
                unset( $term );
                break;
            case 'plugin':
                $plugin_data = self::get_plugin_data( $element_id );
                if ( isset( $plugin_data['Name'] ) && !empty($plugin_data['Name']) ) {
                    $output = array(
                        'heading' => esc_html( __( 'For Plugin:', 'super-notes' ) ),
                        'title'   => $plugin_data['Name'],
                    );
                }
                unset( $plugin_data );
                break;
            case 'attachment':
                $output = array(
                    'heading' => esc_html( __( 'For Attachment:', 'super-notes' ) ),
                    'title'   => get_the_title( $element_id ),
                );
                break;
        }
        return ( false !== $output ? '<strong>' . esc_html( $output['heading'] ) . '</strong> ' . esc_html( $output['title'] ) : self::get_deleted_data_notice() );
    }
    
    /**
     * Get single note style
     *
     * @param array  $note_types Note types.
     * @param string $note_type_id Note type ID.
     */
    public static function get_single_note_style( $note_types, $note_type_id )
    {
        return ( isset( $note_types[$note_type_id] ) && isset( $note_types[$note_type_id]['post_meta']['wdpsn_note_types_styles']['color_scheme'] ) ? $note_types[$note_type_id]['post_meta']['wdpsn_note_types_styles']['color_scheme'] : 'deleted' );
    }
    
    /**
     * Get the "deleted" data notice
     */
    public static function get_deleted_data_notice()
    {
        return '<span class="wdpsn-data-deleted">' . esc_html( __( 'Deleted', 'super-notes' ) ) . '</span>';
    }
    
    /**
     * Get the user name by ID
     *
     * @param string $id User ID.
     */
    public static function get_user_name_by_id( $id )
    {
        return ( false !== get_userdata( $id ) ? get_the_author_meta( 'display_name', esc_attr( $id ) ) : self::get_deleted_data_notice() );
    }
    
    /**
     * Display messages
     *
     * @param array $messages Messages array.
     */
    public static function display_messages( $messages )
    {
        if ( !is_array( $messages ) || array() === $messages ) {
            return;
        }
        foreach ( $messages as $message ) {
            ?>
			<div class="notice notice-<?php 
            echo  esc_attr( $message['status'] ) ;
            ?>">
				<p><?php 
            echo  esc_html( $message['content'] ) ;
            ?></p>
			</div>
			<?php 
        }
    }
    
    /**
     * Generate class name
     *
     * @param string $name Base name.
     * @param string $before Additional text before.
     * @param string $after Additional text after.
     */
    public static function generate_class_name( $name, $before = '', $after = '' )
    {
        $name = ucfirst( $name );
        
        if ( false !== strpos( $name, '_' ) ) {
            $parts = explode( '_', $name );
            $result = '';
            foreach ( $parts as $part ) {
                $result .= ucfirst( $part );
            }
            $name = $result;
        }
        
        return $before . $name . $after;
    }
    
    /**
     * Check if note type id is valid
     *
     * @param string $note_type_id Note Type ID.
     */
    public static function is_note_type_id_valid( $note_type_id )
    {
        return WDPSN_DataCache::get_note_type_id() === (int) $note_type_id;
    }
    
    /**
     * Create custom filter for notes content
     */
    public static function register_notes_content_filter()
    {
        add_filter( 'wdpsn_note_content', 'wptexturize' );
        add_filter( 'wdpsn_note_content', 'wpautop' );
        add_filter( 'wdpsn_note_content', 'shortcode_unautop' );
        add_filter( 'wdpsn_note_content', 'capital_P_dangit' );
        add_filter( 'wdpsn_note_content', 'convert_smilies' );
    }
    
    /**
     * Display additional notices
     */
    public static function display_additional_notices()
    {
        ?>
		<div class="wdpsn-note-wrapper">
			<div class="wdpsn-note-container wdpsn-note-container--go-premium">
				<div class="wdpsn-note-container__content">
					<div class="wdpsn-note-container__content__wrapper">
						<h5><?php 
        echo  esc_html( __( 'Unlock more features!', 'super-notes' ) ) ;
        ?></h5>
						<p><?php 
        echo  esc_html( __( 'Upgrade to premium version to add tags to notes, create unlimited Note Types, get premium support and enable Owners and Viewers notifications about notes changes.', 'super-notes' ) ) ;
        ?></p>
						<p><a href="<?php 
        echo  esc_url( get_admin_url( null, 'admin.php?page=wdpsn-pricing' ) ) ;
        ?>" class="button button-secondary"><?php 
        echo  esc_html( __( 'Upgrade now!', 'super-notes' ) ) ;
        ?></a></p>
					</div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    /**
     * Get creation year
     *
     * @param string $start Year to start counting from.
     */
    public static function get_creation_year( $start )
    {
        $year = current_time( 'Y' );
        return ( $start === $year ? $start : $start . ' - ' . $year );
    }
    
    /**
     * Display attributes in output
     *
     * @param array  $atts Attributes to be displayed.
     * @param string $element HTML element type.
     */
    public static function display_atts( $atts, $element )
    {
        if ( !is_array( $atts ) ) {
            return '';
        }
        $output = '';
        foreach ( $atts as $attr_key => $attr_value ) {
            $output .= ' ' . $attr_key . '="' . esc_attr( $attr_value ) . '"';
        }
        return '<' . $element . $output . '>';
    }
    
    /**
     * Strip unwanted and force balance tags
     *
     * @param string $content Content to be protected.
     */
    public static function strip_and_balance_tags( $content )
    {
        return force_balance_tags( strip_tags( $content, '<p><a><img><del><ins><code><pre><ul><ol><li><blockquote><b><strong><em><i>' ) );
    }
    
    /**
     * Get single argument from the URL
     *
     * @param string $key URL argument key.
     * @param mixed  $default Default value.
     */
    public static function get_url_arg( $key, $default )
    {
        $query = ( isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : false );
        
        if ( false === $query ) {
            $uri = ( isset( $_SERVER['REQUEST_URI'] ) ? home_url( '/' ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false );
            if ( false === $uri ) {
                return false;
            }
            $query = wp_parse_url( $uri, PHP_URL_QUERY );
            if ( null === $query ) {
                return false;
            }
        }
        
        parse_str( $query, $args );
        return ( isset( $args[$key] ) && !empty($args[$key]) ? sanitize_text_field( wp_unslash( $args[$key] ) ) : $default );
    }

}
/**
 * Sanitize arrays
 *
 * @param mixed $data Data to be sanitized.
 * @param mixed $key Data element key.
 */
function wdpsn_sanitize_array( $data, $key = false )
{
    
    if ( !is_array( $data ) ) {
        if ( 'content' === $key ) {
            return WDPSN_Helper::strip_and_balance_tags( $data );
        }
        return sanitize_text_field( wp_unslash( $data ) );
    }
    
    foreach ( $data as $key => $value ) {
        $data[$key] = wdpsn_sanitize_array( $value, $key );
    }
    return $data;
}
