<?php
/**
 * Search results.
 */
get_header();
?>
<main id="main" class="pt-plain pt-search">
	<div class="pt-container" style="max-width:var(--pt-container)">
		<p class="pt-eyebrow pt-eyebrow--red">Search</p>
		<h1><?php printf( 'Results for &ldquo;%s&rdquo;', esc_html( get_search_query() ) ); ?></h1>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="pt-form" style="padding:32px 0 0">
			<label for="pt-search-q" class="screen-reader-text">Search</label>
			<input id="pt-search-q" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search ProTorque" style="max-width:480px">
		</form>
		<?php if ( have_posts() ) : ?>
		<ul class="pt-postgrid">
			<?php while ( have_posts() ) : the_post(); ?>
			<li class="pt-post">
				<p class="pt-post__date"><?php echo esc_html( get_post_type() === 'post' ? get_the_date( 'F j, Y' ) : ucfirst( get_post_type() === 'pt_job' ? 'Job' : get_post_type() ) ); ?></p>
				<h3 class="pt-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<p class="pt-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 24 ) ); ?></p>
			</li>
			<?php endwhile; ?>
		</ul>
		<nav class="pt-pagination" aria-label="Result pages"><?php echo paginate_links( [ 'prev_text' => 'Previous', 'next_text' => 'Next' ] ); ?></nav>
		<?php else : ?>
		<p class="pt-prose">Nothing matched that search. Try a service name such as casing running, CATM, or midstream construction, or <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">get in touch</a>.</p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
