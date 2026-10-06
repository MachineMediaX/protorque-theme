<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ( defined( 'PT_GTM_ID' ) && PT_GTM_ID ) : ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( PT_GTM_ID ); ?>');</script>
<!-- End Google Tag Manager -->
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( defined( 'PT_GTM_ID' ) && PT_GTM_ID ) : ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( PT_GTM_ID ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>
<a class="pt-skip" href="#main"><?php esc_html_e( 'Skip to content', 'protorque' ); ?></a>

<header class="pt-header" id="top">
	<div class="pt-header__inner">
		<a class="pt-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'ProTorque home', 'protorque' ); ?>">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logos/ProTorque_Logo_Black_Red.png' ); ?>" alt="ProTorque" width="296" height="70">
		</a>

		<nav class="pt-nav" aria-label="<?php esc_attr_e( 'Primary', 'protorque' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'pt-nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'pt_primary_nav_fallback',
			] );
			?>
		</nav>

		<div class="pt-header__actions">
			<a class="pt-header__quote" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'protorque' ); ?></a>
			<button class="pt-header__search" type="button" aria-expanded="false" aria-controls="pt-search" aria-label="<?php esc_attr_e( 'Search', 'protorque' ); ?>">
				<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7"/><path d="M16 16l5 5"/></svg>
			</button>
			<button class="pt-header__menu" type="button" aria-expanded="false" aria-controls="pt-mobile-nav" aria-label="<?php esc_attr_e( 'Menu', 'protorque' ); ?>">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
	<div class="pt-search" id="pt-search" hidden>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="pt-search-field"><?php esc_html_e( 'Search ProTorque', 'protorque' ); ?></label>
			<input id="pt-search-field" type="search" name="s" placeholder="<?php esc_attr_e( 'Search', 'protorque' ); ?>" value="<?php echo get_search_query(); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'protorque' ); ?></button>
		</form>
	</div>
</header>
