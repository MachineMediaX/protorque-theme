<?php
/**
 * Template Name: Service Detail (Template 2)
 *
 * Hero, overview on black, "What the service includes" checklist on the red gradient, four-step process,
 * Why This Matters on black with the pull quote in a card, connected services, Get in Touch bar with phone.
 * Content comes from inc/service-content.php keyed by the page slug.
 */
$img  = get_template_directory_uri() . '/assets/img';
$home = home_url( '/' );
$all  = include get_template_directory() . '/inc/service-content.php';
$slug = get_post_field( 'post_name', get_queried_object_id() );
$c    = $all[ $slug ] ?? null;
get_header();

if ( ! $c ) :
	?>
	<main id="main" class="pt-container" style="padding:120px 0"><h1 class="pt-h2"><?php the_title(); ?></h1><p>No content set for this service yet.</p></main>
	<?php
	get_footer();
	return;
endif;
?>
<main id="main" class="pt-service">

	<section class="pt-pagehero pt-pagehero--sub">
		<img src="<?php echo esc_url( "$img/{$c['hero']}" ); ?>" alt="" width="1440" height="750">
		<div class="pt-pagehero__scrim"></div>
		<div class="pt-pagehero__text">
			<h1 class="pt-pagehero__title"><?php echo esc_html( $c['title'] ); ?></h1>
			<p class="pt-pagehero__sub"><?php echo esc_html( $c['subhead'] ); ?></p>
			<a class="pt-btn" href="<?php echo esc_url( $home . 'contact-us/' ); ?>">Get in Touch</a>
		</div>
	</section>

	<section class="pt-svc-overview pt-section--black">
		<div class="pt-container">
			<div class="pt-svc-overview__inner">
				<p class="pt-eyebrow pt-eyebrow--yellow">Overview</p>
				<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $c['overview']['h2'] ); ?></h2>
				<?php foreach ( $c['overview']['text'] as $p ) : ?><p><?php echo $p; ?></p><?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="pt-includes pt-section--red">
		<div class="pt-container">
			<h2 class="pt-h2 pt-center">What the service includes</h2>
			<ul class="pt-checklist">
				<?php foreach ( $c['includes'] as $item ) : ?>
				<li><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"/></svg><span><?php echo esc_html( $item ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="pt-process">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">How We Do It</p>
				<h2 class="pt-h2">Our process</h2>
			</div>
			<ol class="pt-principles__grid pt-process__grid">
				<?php foreach ( $c['process'] as $i => [ $step, $text ] ) : ?>
				<li class="pt-principle pt-step">
					<p class="pt-step__eyebrow">Step <?php echo (int) $i + 1; ?></p>
					<h3><?php echo esc_html( $step ); ?></h3>
					<p><?php echo esc_html( $text ); ?></p>
				</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="pt-why pt-section--black">
		<div class="pt-container pt-why__grid">
			<div class="pt-why__text">
				<p class="pt-eyebrow pt-eyebrow--yellow">Why This Matters</p>
				<h2 class="pt-h2 pt-h2--left">Why this matters</h2>
				<?php foreach ( $c['why']['text'] as $p ) : ?><p><?php echo $p; ?></p><?php endforeach; ?>
			</div>
			<blockquote class="pt-quote">
				<p><?php echo esc_html( $c['why']['quote'] ); ?></p>
			</blockquote>
		</div>
	</section>

	<section class="pt-related">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">Connected Services</p>
				<h2 class="pt-h2">Connected services</h2>
			</div>
			<ul class="pt-related__grid">
				<?php foreach ( $c['related'] as [ $title, $text, $path, $cta ] ) : ?>
				<li class="pt-svc pt-svc--light">
					<h3 class="pt-svc__title"><?php echo $title; ?></h3>
					<p class="pt-svc__body"><?php echo esc_html( $text ); ?></p>
					<a class="pt-svc__link" href="<?php echo esc_url( $home . $path ); ?>"><?php echo $cta; ?></a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="pt-ctabar pt-section--black">
		<div class="pt-ctabar__inner">
			<h2 class="pt-ctabar__title"><?php echo esc_html( $c['cta']['h2'] ); ?></h2>
			<p class="pt-ctabar__text"><?php echo esc_html( $c['cta']['text'] ); ?></p>
			<div class="pt-ctabar__actions">
				<a class="pt-btn" href="<?php echo esc_url( $home . 'contact-us/' ); ?>">Get in Touch</a>
				<a class="pt-ctabar__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $c['cta']['phone'] ) ); ?>"><?php echo esc_html( $c['cta']['phone'] ); ?></a>
			</div>
		</div>
	</section>

</main>
<?php get_footer(); ?>
