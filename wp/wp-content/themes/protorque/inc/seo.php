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

/**
 * Search result excerpt. Theme pages render from the content files, so their database content can still hold
 * old page-builder markup; use the page's own subtitle or opening paragraph instead, and never print shortcodes.
 */
function pt_search_excerpt() {
	$post = get_post();
	if ( $post && 'page' === $post->post_type && function_exists( 'pt_content_pages' ) ) {
		$pages = pt_content_pages();
		$page  = $pages[ $post->post_name ] ?? null;
		if ( $page ) {
			foreach ( $page['sections'] ?? [] as $sec ) {
				if ( ! empty( $sec['sub'] ) ) {
					return wp_trim_words( wp_strip_all_tags( $sec['sub'] ), 30 );
				}
				if ( ! empty( $sec['paras'][0] ) ) {
					return wp_trim_words( wp_strip_all_tags( $sec['paras'][0] ), 30 );
				}
			}
		}
		$meta = get_post_meta( $post->ID, '_pt_meta_description', true );
		if ( $meta ) {
			return $meta;
		}
	}
	$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : (string) ( $post->post_content ?? '' );
	$text = preg_replace( '/\[\/?[a-zA-Z][^\]]*\]/', ' ', $text ); // any shortcode, registered or not
	return wp_trim_words( wp_strip_all_tags( $text ), 24 );
}

/* Search covers pages, posts and jobs only, and matches on titles and the visible content. */
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_search() ) {
		$q->set( 'post_type', [ 'page', 'post', 'pt_job' ] );
	}
} );

/* Yoast (active on staging): never build a description from old page-builder markup left in the database. */
function pt_clean_description( $desc ) {
	if ( is_singular() && ( '' === trim( (string) $desc ) || false !== strpos( (string) $desc, '[' ) ) ) {
		$meta = get_post_meta( get_queried_object_id(), '_pt_meta_description', true );
		return $meta ? $meta : pt_search_excerpt();
	}
	return $desc;
}
add_filter( 'wpseo_opengraph_desc', 'pt_clean_description', 20 );
add_filter( 'wpseo_metadesc', 'pt_clean_description', 20 );
add_filter( 'wpseo_twitter_description', 'pt_clean_description', 20 );
