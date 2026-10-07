<?php
/**
 * Single news post.
 */
get_header();
the_post();
?>
<main id="main" class="pt-content pt-single">
	<div class="pt-container">
		<header class="pt-single__head">
			<p class="pt-post__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
			<h1 class="pt-single__title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?><div class="pt-single__hero"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
		<div class="pt-single__body pt-prose" style="margin-top:56px"><?php the_content(); ?></div>
		<div class="pt-single__body"><a class="pt-single__back" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/news/' ) ); ?>">&larr; Back to News &amp; Resources</a></div>
	</div>
</main>
<section class="pt-ctabar pt-section--black">
	<div class="pt-ctabar__inner">
		<h2 class="pt-ctabar__title">Talk to ProTorque about your next program.</h2>
		<p class="pt-ctabar__text">Tubular running, drilling verification, midstream services, and engineered equipment across Canada, the USA, and South America.</p>
		<div class="pt-ctabar__actions"><a class="pt-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Get in Touch</a></div>
	</div>
</section>
<?php get_footer(); ?>
