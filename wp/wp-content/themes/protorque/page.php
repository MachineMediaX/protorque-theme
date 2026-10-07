<?php
/**
 * Default page template (Privacy Policy, Terms, anything without a dedicated template).
 */
get_header();
the_post();
?>
<main id="main" class="pt-plain">
	<div class="pt-container">
		<h1><?php the_title(); ?></h1>
		<div class="pt-prose"><?php the_content(); ?></div>
	</div>
</main>
<?php get_footer(); ?>
