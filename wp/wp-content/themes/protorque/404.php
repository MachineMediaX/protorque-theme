<?php
/**
 * 404.
 */
get_header();
?>
<main id="main" class="pt-pagehero pt-pagehero--sub pt-pagehero--plain" style="min-height:60vh">
	<div class="pt-pagehero__text">
		<p class="pt-eyebrow pt-eyebrow--yellow">Error 404</p>
		<h1 class="pt-pagehero__title">Page Not Found</h1>
		<p class="pt-pagehero__sub">The page you are looking for has moved or does not exist. Head back to the homepage or explore our services.</p>
		<p style="margin-top:36px;display:flex;gap:16px;justify-content:center;flex-wrap:wrap"><a class="pt-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to Homepage</a><a class="pt-btn pt-btn--black" style="border:1px solid #444" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Get in Touch</a></p>
	</div>
</main>
<?php get_footer(); ?>
