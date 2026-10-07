<?php
/**
 * News & Resources: featured (latest) post, then the grid with pagination, then the newsletter form.
 */
$pt_page = pt_content_page( 'news' );
$img  = get_template_directory_uri() . '/assets/img';
$hero = $pt_page['sections'][0] ?? [ 'title' => 'News & Resources', 'sub' => '' ];
$news = null;
foreach ( $pt_page['sections'] ?? [] as $s ) { if ( $s['kind'] === 'form' ) { $news = $s; } }
get_header();
?>
<main id="main" class="pt-content pt-newspage">

	<section class="pt-pagehero pt-pagehero--sub">
		<img src="<?php echo esc_url( "$img/heroes/news.jpg" ); ?>" alt="" width="1440" height="810">
		<div class="pt-pagehero__scrim"></div>
		<div class="pt-pagehero__text">
			<h1 class="pt-pagehero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
			<?php if ( ! empty( $hero['sub'] ) ) : ?><p class="pt-pagehero__sub"><?php echo esc_html( $hero['sub'] ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="pt-news">
		<div class="pt-container">
			<?php if ( have_posts() ) : ?>
				<?php if ( ! is_paged() ) : the_post(); ?>
				<p class="pt-eyebrow pt-eyebrow--red">Featured</p>
				<article class="pt-featured" style="margin-top:24px">
					<a class="pt-featured__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large' ); else : ?><img src="<?php echo esc_url( "$img/content/casing-running-card.jpg" ); ?>" alt=""><?php endif; ?>
					</a>
					<div class="pt-featured__body">
						<p class="pt-post__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
						<h2 class="pt-featured__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
						<p><a class="pt-svc__link" href="<?php the_permalink(); ?>">Read the Article</a></p>
					</div>
				</article>
				<?php endif; ?>
				<div class="pt-center" style="margin-top:96px">
					<p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $news['eyebrow'] ?? 'Latest' ); ?></p>
					<h2 class="pt-h2"><?php echo esc_html( $news['title'] ?? 'Latest from ProTorque' ); ?></h2>
				</div>
				<ul class="pt-postgrid">
					<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
				</ul>
				<nav class="pt-pagination" aria-label="News pages"><?php echo paginate_links( [ 'prev_text' => 'Previous', 'next_text' => 'Next' ] ); ?></nav>
			<?php else : ?>
				<p class="pt-lede">No news posts yet.</p>
			<?php endif; ?>
		</div>
	</section>

	<section class="pt-form pt-section--black pt-newsletter">
		<div class="pt-container">
			<div class="pt-form__inner">
				<p class="pt-eyebrow pt-eyebrow--yellow">Stay Informed</p>
				<h2 class="pt-h2 pt-h2--left">Want to stay informed?</h2>
				<p><?php echo esc_html( $news['paras'][0] ?? 'Subscribe for ProTorque insights, news, and case studies.' ); ?></p>
				<?php pt_form( 'Newsletter Signup' ); ?>
			</div>
		</div>
	</section>

</main>
<?php get_footer(); ?>
