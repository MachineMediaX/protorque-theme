<?php
/**
 * Midstream Services landing page. Structure per Revision 3.7 with the design system applied:
 * hero, overview with the Full-trade / Permian / T&M panel, six services grid, client logo strip, Get in Touch bar.
 * Copy is the approved dev-site copy for this page. New Mexico references stay as written pending the US content review.
 * Hero photo supplied by the client Oct 7, 2026 (IMG_0960).
 */
$img  = get_template_directory_uri() . '/assets/img';
$home = home_url( '/' );
get_header();

$services = [
	[ 'n' => '01', 'title' => 'Midstream Construction', 'lead' => 'New facilities, pipelines, and infrastructure built right.', 'body' => 'Plant construction, pipeline construction, facility expansion, civil work, foundations, and turnkey midstream builds. Combo welders for poly, carbon, and stainless.', 'url' => $home . 'midstream-services/midstream-construction/', 'cta' => 'Explore Midstream Construction' ],
	[ 'n' => '02', 'title' => 'Plant and Pipeline Maintenance', 'lead' => 'Keeping facilities running. Responding fast when they need support.', 'body' => 'Ongoing maintenance, plant turnarounds, T&amp;M work, pipeline maintenance, and reliability programs. The crews operators call when downtime is not an option.', 'url' => $home . 'midstream-services/plant-and-pipeline-maintenance/', 'cta' => 'Explore Plant and Pipeline Maintenance' ],
	[ 'n' => '03', 'title' => 'Facilities Electrical I&amp;C and Mechanical', 'lead' => 'Master electricians and mechanical trades under one roof.', 'body' => 'Electrical and instrumentation, mechanical trades, and full E&amp;I-Mech capability for facility builds, expansions, and ongoing maintenance. Turnkey when operators need it.', 'url' => $home . 'midstream-services/facilities-electrical-instrumentation-mechanical/', 'cta' => 'Explore Facilities E&amp;I and Mechanical' ],
	[ 'n' => '04', 'title' => 'Environmental and Water Management', 'lead' => 'Water, environmental, and compliance services for midstream operations.', 'body' => 'Produced water management, clean brine facility operations, environmental compliance, and integrated water management programs. Connected to ProTorque&rsquo;s broader water and drilling capability.', 'url' => $home . 'midstream-services/environmental-and-water-management/', 'cta' => 'Explore Environmental and Water Management' ],
	[ 'n' => '05', 'title' => 'ROW Services', 'lead' => 'Right of way clearing, pads, and civil work that gets the project ready.', 'body' => 'ROW clearing, new pad construction, civil concrete work, industrial weed control, and the foundational site work that midstream construction depends on.', 'url' => $home . 'midstream-services/row-services/', 'cta' => 'Explore ROW Services' ],
	[ 'n' => '06', 'title' => 'Specialty Piping', 'lead' => 'Fiber glass piping and specialty material installation.', 'body' => 'Fiber glass piping installation for facilities and corrosive-service applications. Combo-welded carbon and stainless piping for facility expansions and new builds.', 'url' => $home . 'midstream-services/specialty-piping/', 'cta' => 'Explore Specialty Piping' ],
];
?>
<main id="main" class="pt-landing pt-midstream">

	<section class="pt-pagehero pt-pagehero--sub">
		<img src="<?php echo esc_url( "$img/midstream/hero.jpg" ); ?>" srcset="<?php echo esc_url( "$img/midstream/hero@2x.jpg" ); ?> 2x" alt="" width="1440" height="750">
		<div class="pt-pagehero__scrim"></div>
		<div class="pt-pagehero__text">
			<h1 class="pt-pagehero__title">Midstream<br>Services</h1>
			<p class="pt-pagehero__sub">Construction, maintenance, and operational support for the facilities and pipelines that move energy.</p>
		</div>
	</section>

	<section class="pt-overview">
		<div class="pt-container pt-overview__grid">
			<div class="pt-overview__photo">
				<img src="<?php echo esc_url( "$img/midstream/overview.jpg" ); ?>" srcset="<?php echo esc_url( "$img/midstream/overview@2x.jpg" ); ?> 2x" alt="" width="620" height="670" loading="lazy">
			</div>
			<div class="pt-overview__text">
				<p class="pt-eyebrow pt-eyebrow--red">Midstream Services</p>
				<h2 class="pt-h2 pt-h2--left">The crews behind reliable midstream operations.</h2>
				<p>Facility reliability does not happen by accident. It takes skilled trades, fast response, and crews that understand the infrastructure they are working on. ProTorque provides midstream services for operators across North America: construction, maintenance, electrical and instrumentation, mechanical, environmental, ROW, and specialty piping. The work that keeps facilities operational and infrastructure performing.</p>
				<p>Whether the program is a new facility build in the Permian Basin, a turnaround in West Texas, a pipeline tie-in, or ongoing E&amp;I and mechanical support for a midstream operator, ProTorque shows up with the trades, the equipment, and the discipline to keep operations on schedule.</p>
				<p>Twenty-plus years of energy services experience. Combo welders for poly, carbon, and stainless. Master electricians on staff. Both mechanical and electrical departments under one roof for turnkey operations and builds.</p>
			</div>
		</div>
	</section>

	<section class="pt-panel pt-section--red">
		<div class="pt-container">
			<dl class="pt-panel__tiles">
				<div><dt>Full-trade</dt><dd>Capability under one roof</dd></div>
				<div><dt>Permian</dt><dd>And beyond</dd></div>
				<div><dt>T&amp;M</dt><dd>Turnkey work</dd></div>
			</dl>
		</div>
	</section>

	<section class="pt-services pt-section--charcoal">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--yellow">Six Capability Areas</p>
				<h2 class="pt-h2">What we deliver on the midstream side</h2>
				<p class="pt-lede">Six capability areas, all the trades and disciplines a midstream operator needs from a single services contractor. Build, maintain, expand, or support. ProTorque covers it.</p>
			</div>
			<ul class="pt-services__grid">
				<?php foreach ( $services as $s ) : ?>
				<li class="pt-svc">
					<p class="pt-svc__eyebrow"><?php echo esc_html( $s['n'] ); ?></p>
					<h3 class="pt-svc__title"><?php echo $s['title']; ?></h3>
					<p class="pt-svc__lead"><?php echo esc_html( $s['lead'] ); ?></p>
					<p class="pt-svc__body"><?php echo $s['body']; ?></p>
					<a class="pt-svc__link" href="<?php echo esc_url( $s['url'] ); ?>"><?php echo $s['cta']; ?></a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="pt-clients pt-clients--sub">
		<div class="pt-container pt-center">
			<h2 class="pt-eyebrow pt-eyebrow--dark">Midstream Clients</h2>
			<ul class="pt-logos">
				<?php foreach ( [ 'cenovus.png', 'cnrl.png', 'shell.png', 'exxon.png', 'occidental.png', 'enterprise-products.png', 'paramount.svg', 'whitecap.png', 'xto.png' ] as $l ) : ?>
				<li><img src="<?php echo esc_url( "$img/logos/clients/$l" ); ?>" alt="<?php echo esc_attr( ucwords( str_replace( [ '-', '_' ], ' ', pathinfo( $l, PATHINFO_FILENAME ) ) ) ); ?>" loading="lazy"></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="pt-ctabar pt-section--black">
		<div class="pt-ctabar__inner">
			<h2 class="pt-ctabar__title">Need crews on a midstream facility or pipeline?</h2>
			<p class="pt-ctabar__text">Talk to ProTorque about construction, maintenance, E&amp;I, ROW, or specialty piping work.</p>
			<a class="pt-btn" href="<?php echo esc_url( $home . 'contact-us/' ); ?>">Get in Touch</a>
		</div>
	</section>

</main>
<?php get_footer(); ?>
