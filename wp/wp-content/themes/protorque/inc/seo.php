<?php
/**
 * Per-post SEO fields without a plugin: _pt_seo_title overrides the <title>, _pt_meta_description (or the excerpt on posts) becomes the meta description.
 * Set per post in Custom Fields, or by a content migration.
 */
add_filter( 'pre_get_document_title', function ( $title ) {
	if ( is_singular() ) {
		$t = get_post_meta( get_queried_object_id(), '_pt_seo_title', true );
		if ( $t ) {
			return $t . ' | ProTorque';
		}
	}
	return $title;
} );
add_action( 'wp_head', function () {
	if ( ! is_singular() ) {
		return;
	}
	$id   = get_queried_object_id();
	$desc = get_post_meta( $id, '_pt_meta_description', true );
	if ( ! $desc && is_singular( 'post' ) ) {
		$desc = get_the_excerpt( $id );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
	}
}, 1 );
