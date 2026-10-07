<?php
/**
 * Template Name: Content Page (landing, service, equipment)
 *
 * Renders the page from inc/content/pages.json by slug: service landings (Template 1),
 * service detail pages (Template 2) and equipment pages (Template 3).
 */
$pt_page = pt_content_page( get_post_field( 'post_name', get_queried_object_id() ) );
get_header();

if ( ! $pt_page ) :
	?>
	<main id="main" class="pt-plain"><div class="pt-container"><h1 class="pt-h2"><?php the_title(); ?></h1><p>No content set for this page yet.</p></div></main>
	<?php
	get_footer();
	return;
endif;

pt_render_page( $pt_page );
get_footer();
