<?php
/**
 * Homepage. Built to the approved comp (Sam Benesh, Sept 29, 2026).
 * Copy is final per the Development Brief; stats and client logos are placeholders pending the client.
 */
$img = get_template_directory_uri() . '/assets/img';
$home = home_url( '/' );
get_header();
?>
<main id="main">

	<!-- Hero -->
	<section class="pt-hero">
		<?php if ( file_exists( get_template_directory() . '/assets/video/hero.mp4' ) ) : ?>
		<video class="pt-hero__video" autoplay muted loop playsinline poster="<?php echo esc_url( "$img/placeholder/hero-poster.jpg" ); ?>">
			<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/video/hero.mp4' ); ?>" type="video/mp4">
		</video>
		<?php else : ?>
		<img class="pt-hero__video" src="<?php echo esc_url( "$img/placeholder/hero-poster.jpg" ); ?>" alt="" width="1440" height="750">
		<?php endif; ?>
		<div class="pt-hero__scrim"></div>
		<h1 class="pt-hero__title">Total Rig &amp;<br>Midstream<br>Solutions</h1>
	</section>

	<!-- Mission statement -->
	<section class="pt-mission">
		<p class="pt-mission__text">ProTorque is an energy services company providing integrated <br><span>Tubular Running Services</span> and <span>Midstream Construction</span> <br>solutions to operators across North and South America.</p>
	</section>

	<!-- Two-photo band -->
	<section class="pt-band" aria-label="Service areas">
		<a class="pt-band__item" href="<?php echo esc_url( $home . 'tubular-running-services/' ); ?>">
			<img src="<?php echo esc_url( "$img/home/band-trs.jpg" ); ?>" srcset="<?php echo esc_url( "$img/home/band-trs@2x.jpg" ); ?> 2x" alt="" width="718" height="527">
			<span class="pt-btn">Tubular Running Services</span>
		</a>
		<a class="pt-band__item" href="<?php echo esc_url( $home . 'midstream-services/' ); ?>">
			<img src="<?php echo esc_url( "$img/home/band-midstream.jpg" ); ?>" srcset="<?php echo esc_url( "$img/home/band-midstream@2x.jpg" ); ?> 2x" alt="" width="723" height="527">
			<span class="pt-btn">Midstream Solutions</span>
		</a>
	</section>

	<!-- Who We Are -->
	<section class="pt-who pt-section--charcoal">
		<div class="pt-container pt-center">
			<p class="pt-eyebrow">Who We Are</p>
			<h2 class="pt-h2">Industry Knowledge Meets Innovation</h2>
			<p class="pt-lede">Since 2004, ProTorque has delivered oilfield services backed by experienced crews and field-proven methods. We put new technology to work where it improves safety and efficiency on the job. Operating across North and South America.</p>
			<a class="pt-btn" href="<?php echo esc_url( $home . 'about/' ); ?>">Read Our Story</a>
		</div>
	</section>

	<!-- Core Capabilities -->
	<section class="pt-caps pt-section--red">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow">Core Capabilities</p>
				<h2 class="pt-h2">Field Expertise Across Every Stage of Operations</h2>
				<p class="pt-lede">Four pillars. One standard of execution. ProTorque brings together tubular running services, drilling verification and monitoring, midstream construction and maintenance, and equipment, run by the same crews to the same procedures on every job.</p>
			</div>

			<div class="pt-cards">
				<?php
				$cards = [
					[
						'title' => 'Tubular Running<br>Services',
						'eyebrow' => 'Tubular Running Services',
						'headline' => 'Casing operations done right on every connection.',
						'body' => 'Experienced crews and engineered tools, with disciplined preparation from thread cleaning and inspection through final make-up. ProTorque keeps casing programs predictable and on schedule.',
						'cta' => 'Explore Tubular Running Services',
						'url' => $home . 'tubular-running-services/',
						'img' => 'card-trs',
						'open' => true,
					],
					[
						'title' => 'Midstream<br>Construction<br>&amp; Maintenance',
						'eyebrow' => 'Midstream Construction &amp; Maintenance',
						'headline' => 'Plant, pipeline and facilities work, built and maintained to spec.',
						'body' => 'Midstream construction, plant and pipeline maintenance, electrical and instrumentation, mechanical and facilities support, environmental and water management, and right-of-way work.',
						'cta' => 'Explore Midstream Services',
						'url' => $home . 'midstream-services/',
						'img' => 'card-midstream',
					],
					[
						'title' => 'Drilling<br>Verification<br>&amp; Monitoring',
						'eyebrow' => 'Drilling Verification &amp; Monitoring',
						'headline' => 'Trust the numbers on every connection.',
						'body' => 'Hook load and top drive torque verification, iron roughneck torque testing, CATM for power tongs and top drives, and real-time drilling data.',
						'cta' => 'Explore Drilling Services',
						'url' => $home . 'drilling-services/',
						'img' => 'card-drilling',
					],
					[
						'title' => 'Equipment<br>&amp; Innovation',
						'eyebrow' => 'Equipment &amp; Innovation',
						'headline' => 'Engineered equipment that takes risk off the rig floor.',
						'body' => 'RCD Press, CRT Tether Ring, Mobile BHA Frame, Mobile Bucking Frame and Electric Hydraulic Power Units, provided by ProStar, the dedicated equipment division.',
						'cta' => 'Explore Equipment &amp; Innovation',
						'url' => $home . 'equipment-and-innovation/',
						'img' => 'card-equipment',
					],
				];
				foreach ( $cards as $i => $c ) :
					$open = ! empty( $c['open'] );
					?>
				<article class="pt-card<?php echo $open ? ' is-open' : ''; ?>">
					<img class="pt-card__img" src="<?php echo esc_url( "$img/home/{$c['img']}.jpg" ); ?>" srcset="<?php echo esc_url( "$img/home/{$c['img']}@2x.jpg" ); ?> 2x" alt="" loading="lazy">
					<h3 class="pt-card__title"><?php echo $c['title']; ?></h3>
					<button class="pt-card__toggle" type="button" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( wp_strip_all_tags( 'Open ' . $c['eyebrow'] ) ); ?>">
						<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3v18M3 12h18"/></svg>
					</button>
					<div class="pt-card__panel">
						<p class="pt-card__eyebrow"><?php echo $c['eyebrow']; ?></p>
						<p class="pt-card__headline"><?php echo esc_html( $c['headline'] ); ?></p>
						<p class="pt-card__body"><?php echo esc_html( $c['body'] ); ?></p>
						<a class="pt-card__link" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo $c['cta']; ?></a>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- 2025 Operations Statistics (placeholder values pending client) -->
	<section class="pt-stats pt-section--black">
		<div class="pt-container">
			<p class="pt-eyebrow">2025 Operations Statistics</p>
			<dl class="pt-stats__grid">
				<div><dd>0</dd><dt>TRIR</dt></div>
				<div><dd>1,601,172</dd><dt>Kilometres driven</dt></div>
				<div><dd>1,235,681</dd><dt>Miles driven</dt></div>
				<div><dd>236,189</dd><dt>Total Man Hours</dt></div>
			</dl>
		</div>
	</section>

	<!-- Built to Lead -->
	<section class="pt-photoband">
		<img src="<?php echo esc_url( "$img/home/built-to-lead.jpg" ); ?>" srcset="<?php echo esc_url( "$img/home/built-to-lead@2x.jpg" ); ?> 2x" alt="" width="1440" height="658" loading="lazy">
		<h2 class="pt-photoband__title">Built to Lead</h2>
	</section>

	<!-- Our Clients (placeholder set pending client selection) -->
	<section class="pt-clients">
		<div class="pt-container pt-center">
			<h2 class="pt-eyebrow pt-eyebrow--dark">Our Clients</h2>
			<ul class="pt-logos">
				<?php
				$logos = [ 'cenovus.png', 'cnrl.png', 'shell.png', 'exxon.png', 'occidental.png', 'enterprise-products.png', 'paramount.svg', 'whitecap.png', 'xto.png' ];
				foreach ( $logos as $l ) :
					?>
				<li><img src="<?php echo esc_url( "$img/logos/clients/$l" ); ?>" alt="<?php echo esc_attr( ucwords( str_replace( [ '-', '_' ], ' ', pathinfo( $l, PATHINFO_FILENAME ) ) ) ); ?>" loading="lazy"></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

</main>
<?php get_footer(); ?>
