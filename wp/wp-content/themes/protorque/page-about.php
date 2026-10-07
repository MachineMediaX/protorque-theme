<?php
/**
 * About page. Built to Sam Benesh's About layout (Oct 7, 2026 export of the comp sent to the client Oct 5).
 * Copy is the client-approved copy on that comp. Map is rendered from Natural Earth data; marker positions in inc/map-points.php.
 */
$img  = get_template_directory_uri() . '/assets/img';
$home = home_url( '/' );
$pts  = include get_template_directory() . '/inc/map-points.php';
get_header();
?>
<main id="main" class="pt-about">

	<!-- Hero -->
	<section class="pt-pagehero">
		<img src="<?php echo esc_url( "$img/about/hero.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/hero@2x.jpg" ); ?> 2x" alt="" width="1440" height="752">
		<h1 class="pt-pagehero__title">About<br>ProTorque</h1>
	</section>

	<!-- Intro statement -->
	<section class="pt-intro pt-section--red">
		<div class="pt-container pt-center">
			<p class="pt-intro__text">Total Rig and Midstream Solutions. Engineering, experienced crews, and disciplined execution built over 20+ years.</p>
			<a class="pt-btn pt-btn--black" href="<?php echo esc_url( $home . 'contact-us/' ); ?>">Get in Touch</a>
		</div>
	</section>

	<!-- Our Story -->
	<section class="pt-story">
		<div class="pt-container pt-story__grid">
			<div class="pt-map" data-map>
				<div class="pt-map__stage">
				<img class="pt-map__base" src="<?php echo esc_url( "$img/map-americas.svg" ); ?>" alt="Map of North and South America showing ProTorque locations" width="620" height="820">
				<?php foreach ( $pts['dots'] as $d ) : ?>
				<span class="pt-map__dot" style="left:<?php echo esc_attr( $d['px'] ); ?>%;top:<?php echo esc_attr( $d['py'] ); ?>%" aria-hidden="true"></span>
				<?php endforeach; ?>
				<?php foreach ( $pts['markers'] as $key => $m ) : ?>
				<div class="pt-map__marker<?php echo $m['open'] ? ' is-open' : ''; echo empty( $m['pin'] ) ? ' pt-map__marker--dot' : ''; ?>" style="left:<?php echo esc_attr( $m['px'] ); ?>%;top:<?php echo esc_attr( $m['py'] ); ?>%">
					<button class="pt-map__pin" type="button" aria-expanded="<?php echo $m['open'] ? 'true' : 'false'; ?>" aria-controls="map-card-<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $m['country'] . ', ' . $m['city'] ); ?>">
						<svg viewBox="0 0 24 32" width="24" height="32" aria-hidden="true"><path fill="#d22730" d="M12 0C5.4 0 0 5.4 0 12c0 9 12 20 12 20s12-11 12-20C24 5.4 18.6 0 12 0z"/><circle cx="12" cy="12" r="5" fill="#fff"/></svg>
					</button>
					<div class="pt-map__card" id="map-card-<?php echo esc_attr( $key ); ?>">
						<img src="<?php echo esc_url( "$img/about/{$m['photo']}.jpg" ); ?>" alt="" width="400" height="250" loading="lazy">
						<div class="pt-map__cardbody">
							<p class="pt-map__country"><?php echo esc_html( $m['country'] ); ?></p>
							<p class="pt-map__city"><?php echo esc_html( $m['city'] ); ?></p>
							<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $m['phone'] ) ); ?>"><?php echo esc_html( $m['phone'] ); ?></a><br><a href="mailto:<?php echo esc_attr( $m['email'] ); ?>"><?php echo esc_html( $m['email'] ); ?></a></p>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				</div>
				<div class="pt-map__cards" hidden></div>
			</div>

			<div class="pt-story__text">
				<p class="pt-eyebrow pt-eyebrow--red">Our Story</p>
				<h2 class="pt-h2 pt-h2--left">Two Decades of <br>Trusted Performance</h2>
				<p>Since 2004, ProTorque has built its reputation as a trusted energy services partner through field expertise, disciplined execution, and an unwavering commitment to operational excellence. What began as a focus on supplying reliable tubular running services has evolved into a recognized industry leader specializing in a vast range of rig floor and midstream solutions.</p>
				<p>We deliver performance-driven solutions through experienced teams, proven methods, and practical innovation. Our hands-on approach allows us to tackle complex operational challenges, helping clients enhance safety, maximize efficiency, and achieve stronger outcomes where it matters most.</p>
				<p class="pt-story__lead">Operating across North and South America</p>
				<ul class="pt-entities">
					<li><span class="pt-flag pt-flag--ca" aria-hidden="true"></span>ProTorque Energy Canada Ltd</li>
					<li><span class="pt-flag pt-flag--us" aria-hidden="true"></span>ProTorque Energy Inc.</li>
					<li><span class="pt-flag pt-flag--co" aria-hidden="true"></span>ProTorque Energy SAS</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- Core Values -->
	<section class="pt-values pt-section--charcoal">
		<div class="pt-container pt-center">
			<p class="pt-eyebrow pt-eyebrow--yellow">What We Stand For</p>
			<h2 class="pt-h2">Our Core Values</h2>
			<p class="pt-lede">These four values shape how ProTorque hires, coaches, and executes. They are the standard our people hold each other to, on the rig floor and in the office.</p>
			<ol class="pt-values__list">
				<li>Eliminate the Mediocre</li>
				<li>Skilled Influential Confidence</li>
				<li>Ownership Without Entitlement</li>
				<li>Execute for the Greater Good</li>
			</ol>
		</div>
	</section>

	<!-- Principles -->
	<section class="pt-principles">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">Our Foundation</p>
				<h2 class="pt-h2">The Principles That Guide Us</h2>
			</div>
			<div class="pt-principles__grid">
				<article class="pt-principle">
					<h3>Safety<br>Comes First</h3>
					<p><strong>Safety is the guiding principle.</strong> Safety is at the center of everything we do. Through rigorous training, open communication, and a proactive approach to risk management, we empower our teams to protect people, assets, and operations.</p>
				</article>
				<article class="pt-principle">
					<h3>Built for<br>Change</h3>
					<p><strong>The energy industry is constantly evolving.</strong> ProTorque embraces new technologies, changing requirements, and unique project challenges to deliver solutions that meet the needs of today&rsquo;s operations.</p>
				</article>
				<article class="pt-principle">
					<h3>Collaborative<br>Partnerships</h3>
					<p><strong>We view ourselves as an extension of our clients&rsquo; teams.</strong> Through collaboration, we work to understand project goals and operational challenges, developing solutions tailored to each client&rsquo;s needs to support safer, more efficient, and successful outcomes.</p>
				</article>
				<article class="pt-principle">
					<h3>Leading Through<br>Innovation</h3>
					<p><strong>Innovation is driven by real-world challenges.</strong> Our recognition of industry challenges drives the development of practical solutions that enhance safety, efficiency, and performance. Resulting in innovations that revolutionize operations.</p>
				</article>
			</div>
		</div>
	</section>

	<!-- ProTorque Advantage -->
	<section class="pt-advantage">
		<img class="pt-advantage__bg" src="<?php echo esc_url( "$img/about/advantage.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/advantage@2x.jpg" ); ?> 2x" alt="" width="1440" height="817" loading="lazy">
		<div class="pt-advantage__box">
			<p class="pt-eyebrow pt-eyebrow--red">Our Commitment</p>
			<h2 class="pt-h2">The ProTorque Advantage</h2>
			<ul class="pt-advantage__list">
				<li><h3>Safety as a Core Value</h3><p>An uncompromised dedication to protecting people and assets.</p></li>
				<li><h3>Partnership Mindset</h3><p>Aligning our goals with yours for long-term success.</p></li>
				<li><h3>Industry-Leading Expertise:</h3><p>Delivering efficient and reliable solutions.</p></li>
				<li><h3>Technology-Driven Approach</h3><p>Utilizing advancements to maximize results.</p></li>
			</ul>
		</div>
	</section>

	<!-- Safety -->
	<section class="pt-safety pt-section--red">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow">How We Work</p>
				<h2 class="pt-h2">Safety at ProTorque is a <br>working practice, not a poster.</h2>
				<p class="pt-lede pt-safety__lede">Every job starts with a job safety analysis. High-risk work gets dedicated safety supervision. Equipment is inspected on a schedule, and any crew member can stop work when conditions call for it. The record is kept the same way the work is done: documented, current, and available to the operators who audit it. ProTorque maintains compliance across the contractor qualification networks our customers use and reports through the provincial and industry bodies that govern the work.</p>
			</div>
			<div class="pt-safety__cols">
				<div><h3>Compliance and<br>Contractor Qualification</h3><p>ISNetworld, ComplyWorks (Veriforce), Avetta, Enverus, 8am Solutions</p></div>
				<div><h3>Safety Management<br>and Field Systems</h3><p>WorkHub, SiteDocs, Motive</p></div>
				<div><h3>Regulatory and<br>Industry Standards</h3><p>WCB, WorkSafeBC, Energy Safety Canada</p></div>
			</div>
		</div>
	</section>

	<!-- Executive Team -->
	<section class="pt-team">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">Our Team</p>
				<h2 class="pt-h2">Meet the Executive Team</h2>
				<p class="pt-lede pt-team__lede"><strong>ProTorque&rsquo;s greatest asset is our people.</strong> Behind every successful operation is a team of experienced professionals committed to supporting our clients and each other. At ProTorque every individual contributes to the knowledge, dedication, and solution focused thinking required to deliver excellence. We are proud of our team&rsquo;s experience, collaboration, and commitment which continues to drive our success.</p>
			</div>
			<ul class="pt-team__grid">
				<?php
				$team = [
					[ 'Landon McDonald', 'President &amp; CEO', 'landon-mcdonald' ],
					[ 'Michael Angelozzi', 'Executive Vice President', 'michael-angelozzi' ],
					[ 'Jeff Weitzel', 'Chief Operating Officer', 'jeff-weitzel' ],
					[ 'Matt Braaten', 'Chief Financial Officer', 'matt-braaten' ],
				];
				foreach ( $team as [ $name, $title, $file ] ) :
					?>
				<li class="pt-person">
					<img src="<?php echo esc_url( "$img/about/team-$file.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/team-$file@2x.jpg" ); ?> 2x" alt="<?php echo esc_attr( $name ); ?>" width="300" height="300" loading="lazy">
					<div class="pt-person__caption"><strong><?php echo esc_html( $name ); ?></strong><span><?php echo $title; ?></span></div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- From the President -->
	<section class="pt-president pt-section--charcoal">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">From the President</p>
				<h2 class="pt-h2">The Foundation We Started With</h2>
			</div>
			<div class="pt-president__cols">
				<div>
					<p>I&rsquo;ve always considered myself an entrepreneur first. I believe the best businesses are built by people who are willing to work hard, take calculated risks, challenge the way things have always been done, and never become satisfied with where they are today. The desire to be better is what drives me.</p>
					<p>ProTorque was built on those principles.</p>
					<p>What began as a blue-collar energy service business with one CATM system in a pickup has grown into an internationally recognized group of companies providing specialized TRS and Midstream services, manufacturing innovative equipment, and developing technology that is changing how work is performed in the field.</p>
					<p>I&rsquo;m proud of that evolution, but I&rsquo;m even more proud that we have never lost the foundation we started with.</p>
					<p>At our core, we are still field technicians, or rig hands. We understand that technology only creates value when it makes the job safer, more efficient, more reliable, or more productive. Our innovation comes from field experience, from understanding the challenges our people and customers face, and from the determination to build a better solution.</p>
				</div>
				<div>
					<p>That mindset has taken ProTorque beyond providing services. We are building equipment, developing technology, and integrating data and automation to create solutions that let our customers and our people perform at a higher level.</p>
					<p>My vision for ProTorque is not to become the biggest company in our industry. It is to build a group of companies recognized for execution, innovation, entrepreneurship, and the quality of our people, while continuing to create opportunities for the employees, customers, partners, and communities who have helped us grow.</p>
					<p>The equipment will continue to change. Technology will continue to evolve. Markets will rise and fall. But the fundamentals of building a great company remain remarkably consistent: work hard, surround yourself with great people, take care of your customers, embrace change, and never stop looking for a better way.</p>
					<p>That was the foundation of ProTorque when we started, and it will remain the foundation of where we go next.</p>
					<p class="pt-president__sig"><strong>Landon McDonald</strong><br>President, ProTorque Energy Companies</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Photo band -->
	<section class="pt-photoband pt-photoband--plain">
		<img src="<?php echo esc_url( "$img/about/band.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/band@2x.jpg" ); ?> 2x" alt="" width="1440" height="616" loading="lazy">
	</section>

	<!-- Where We Work -->
	<section class="pt-offices">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">Office Locations</p>
				<h2 class="pt-h2">Where we work</h2>
			</div>
			<ul class="pt-offices__grid">
				<li class="pt-office">
					<img src="<?php echo esc_url( "$img/about/calgary.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/calgary@2x.jpg" ); ?> 2x" alt="Calgary skyline" width="400" height="250" loading="lazy">
					<div class="pt-office__body">
						<h3>Canada</h3><p class="pt-office__city">Calgary, AB</p>
						<p><strong>ProTorque Energy headquarters.</strong><br>Tubular running services, drilling verification, equipment programs, and corporate operations.</p>
						<p>1700, 520-5th Ave SW,<br>Calgary, AB, T2P 3R7<br><a href="tel:+17809330404">+1.780.933.0404</a></p>
						<p><a class="pt-office__dir" href="https://www.google.com/maps/search/?api=1&amp;query=1700%2C+520+5th+Ave+SW%2C+Calgary%2C+AB+T2P+3R7">Get directions</a></p>
					</div>
				</li>
				<li class="pt-office">
					<img src="<?php echo esc_url( "$img/about/midland.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/midland@2x.jpg" ); ?> 2x" alt="Midland, Texas skyline" width="400" height="250" loading="lazy">
					<div class="pt-office__body">
						<h3>United States</h3><p class="pt-office__city">Midland, TX</p>
						<p><strong>ProTorque US operations base.</strong><br>Tubular running services for completions and workovers, drilling verification, and midstream services across the Permian Basin and New Mexico.</p>
						<p>3424 South County Road 1192,<br>Midland, TX, 79706<br><a href="tel:+17133005216">+1.713.300.5216</a></p>
						<p><a class="pt-office__dir" href="https://www.google.com/maps/search/?api=1&amp;query=3424+South+County+Road+1192%2C+Midland%2C+TX+79706">Get directions</a></p>
					</div>
				</li>
				<li class="pt-office">
					<img src="<?php echo esc_url( "$img/about/colombia.jpg" ); ?>" srcset="<?php echo esc_url( "$img/about/colombia@2x.jpg" ); ?> 2x" alt="Bogotá, Colombia" width="400" height="250" loading="lazy">
					<div class="pt-office__body">
						<h3>South America</h3><p class="pt-office__city">Colombia</p>
						<p><strong>ProTorque Energy SAS.</strong><br>Tubular running services and drilling verification supporting operators across Colombia and select South American markets.</p>
						<p>Bodega # 47, Parque Industrial San Jorge, Municipio de Mosquera Cundinamarca<br><a href="tel:+573213202213">+57.321.320.2213</a></p>
						<p><a class="pt-office__dir" href="https://www.google.com/maps/search/?api=1&amp;query=Parque+Industrial+San+Jorge%2C+Mosquera%2C+Cundinamarca%2C+Colombia">Get directions</a></p>
					</div>
				</li>
			</ul>
		</div>
	</section>

	<!-- Get in Touch bar -->
	<section class="pt-ctabar pt-section--black">
		<div class="pt-ctabar__inner">
			<h2 class="pt-ctabar__title">Want to work with ProTorque?</h2>
			<p class="pt-ctabar__text">Talk to a ProTorque crew lead about your next program. We respond fast and prepare before the work begins.</p>
			<a class="pt-btn" href="<?php echo esc_url( $home . 'contact-us/' ); ?>">Get in Touch</a>
		</div>
	</section>

</main>
<?php get_footer(); ?>
