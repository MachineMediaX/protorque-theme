<?php
/**
 * Fallback template (archives without a dedicated template).
 */
get_header();
?>
<main id="main" class="pt-plain">
	<div class="pt-container" style="max-width:var(--pt-container)">
		<h1><?php echo is_home() ? 'News' : wp_strip_all_tags( get_the_archive_title() ); ?></h1>
		<?php if ( have_posts() ) : ?>
		<ul class="pt-postgrid">
			<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
		</ul>
		<nav class="pt-pagination" aria-label="Pages"><?php echo paginate_links( [ 'prev_text' => 'Previous', 'next_text' => 'Next' ] ); ?></nav>
		<?php else : ?>
		<p class="pt-prose">Nothing here yet.</p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
