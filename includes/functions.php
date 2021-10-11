<?php

/**
 * Gets post types that taxonomy is registered to.
 *
 * @since 1.1.0
 *
 * @return array
 */
function maidt_get_post_types() {
	static $post_types = null;

	if ( ! is_null( $post_types ) ) {
		return $post_types;
	}

	$option     = get_option( 'mai_display_taxonomy' );
	$post_types = $option && isset( $option['post_types'] ) ? (array) $option['post_types'] : [ 'post' ];
	$post_types = (array) apply_filters( 'mai_display_taxonomy_post_types', $post_types );

	return array_map( 'esc_attr', $post_types );
}
