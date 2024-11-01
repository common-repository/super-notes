<?php
/**
 * Handle backward compatibility
 *
 * @package super_notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', array( 'WDPSN_Compatibility', 'update_if_possible' ) );

/**
 * Backward compatibility class
 */
class WDPSN_Compatibility {

	/**
	 * Update post types and custom meta data to new names
	 */
	public static function update_if_possible() {

		foreach ( array( 'wdpsn-note-types', 'wdpsn-single-note' ) as $post_type ) {

			$posts = self::get_posts( $post_type );

			if ( false === $posts ) {
				continue;
			}

			foreach ( $posts as $single_post ) {

				foreach ( $single_post['meta'] as $meta_key => $meta_value ) {

					delete_post_meta( $single_post['post_id'], $meta_key );

					$new_meta_value = $meta_value[0];

					if ( ! in_array( $meta_key, array( 'wdpsn_parent_type', 'wdpsn_parent_id', 'wdpsn-notify-owners', 'wdpsn-notify-viewers' ), true ) ) {
						$new_meta_value = wp_json_encode( unserialize( $new_meta_value ) );
					}

					add_post_meta( $single_post['post_id'], str_replace( '-', '_', $meta_key ), $new_meta_value );
				}

				set_post_type( $single_post['post_id'], str_replace( '-', '_', $post_type ) );
			}
		}
	}

	/**
	 * Get posts in specific post type
	 *
	 * @param string $post_type Post type key.
	 */
	public static function get_posts( $post_type ) {

		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
			)
		);

		if ( null === $posts || false === $posts || array() === $posts ) {
			return false;
		}

		$result = array();
		foreach ( $posts as $post ) {

			$meta        = get_post_meta( $post->ID );
			$meta_result = array();

			foreach ( $meta as $single_meta_key => $single_meta_value ) {
				if ( strpos( $single_meta_key, 'wdpsn' ) === 0 ) {
					$meta_result[ $single_meta_key ] = $single_meta_value;
				}
			}

			$result[] = array(
				'post_id' => $post->ID,
				'meta'    => $meta_result,
			);
		}

		return $result;
	}
}
