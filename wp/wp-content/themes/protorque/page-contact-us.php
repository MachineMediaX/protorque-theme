<?php
/**
 * Contact Us. Copy is the approved dev-site copy; office details match the footer.
 */
$pt_page = pt_content_page( 'contact-us' );
$img  = get_template_directory_uri() . '/assets/img';
$hero = $pt_page['sections'][0] ?? [ 'title' => 'Contact ProTorque', 'sub' => '' ];
$form = null;
foreach ( $pt_page['sections'] ?? [] as $s ) {
	if ( $s['kind'] === 'form' ) {
		$form = $s;
	}
}
$offices = [
	[
		'name'       => 'Canada Division',
		'city'       => 'Western Canada',
		'photo'      => 'about/calgary.jpg',
		'dispatch'   => [ [ 'Grande Prairie', '+1.780.288.6128', '+17802886128' ], [ 'Red Deer', '+1.780.933.0404', '+17809330404' ] ],
		'email'      => 'PTCT.Sales@ptenergy.com',
		'address'    => [ 'Canada Head Office', '1700, 520-5th Ave SW', 'Calgary, AB T2P 3R7' ],
		'regions'    => 'Calgary, Red Deer, Edmonton, Grande Prairie',
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=1700%2C%20520-5th%20Ave%20SW%2C%20Calgary%2C%20AB%20T2P%203R7',
	],
	[
		'name'       => 'USA Division',
		'city'       => 'Midland, Texas',
		'photo'      => 'about/midland.jpg',
		'dispatch'   => [ [ '', '+1.713.300.5216', '+17133005216' ] ],
		'email'      => 'PTE.Sales@ptenergy.com',
		'address'    => [ 'USA Head Office', '3424 South County Road 1192', 'Midland, TX 79706' ],
		'regions'    => '',
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=3424%20South%20County%20Road%201192%2C%20Midland%2C%20TX%2079706',
	],
	[
		'name'       => 'South America',
		'city'       => 'Mosquera, Colombia',
		'photo'      => 'about/colombia.jpg',
		'dispatch'   => [ [ '', '+57.321.320.2213', '+573213202213' ] ],
		'email'      => 'PTESAS.Sales@ptenergy.com',
		'address'    => [ 'Colombia Head Office', 'Bodega #47, Parque Industrial San Jorge', 'Municipio de Mosquera, Cundinamarca' ],
		'regions'    => '',
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=Bodega%20%2347%2C%20Parque%20Industrial%20San%20Jorge%2C%20Mosquera%2C%20Cundinamarca',
	],
];
get_header();
?>
<main id="main" class="pt-content pt-contact">

	<section class="pt-pagehero pt-pagehero--sub pt-pagehero--plain">
		<div class="pt-pagehero__text">
			<h1 class="pt-pagehero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
			<?php if ( ! empty( $hero['sub'] ) ) : ?><p class="pt-pagehero__sub"><?php echo esc_html( $hero['sub'] ); ?></p><?php endif; ?>
		</div>
	</section>

	<?php if ( ! empty( $hero['paras'] ) ) : ?>
	<section class="pt-band-text pt-section--red"><div class="pt-container"><?php pt_paras( $hero['paras'], 'pt-band-text__p' ); ?></div></section>
	<?php endif; ?>

	<section class="pt-offices pt-offices--contact">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red">Office Locations</p>
				<h2 class="pt-h2">Where We Work</h2>
			</div>
			<ul class="pt-offices__grid">
				<?php foreach ( $offices as $o ) : ?>
				<li class="pt-office">
					<img src="<?php echo esc_url( "$img/{$o['photo']}" ); ?>" alt="" loading="lazy" width="400" height="250">
					<div class="pt-office__body">
						<h3><?php echo esc_html( $o['name'] ); ?></h3>
						<p class="pt-office__city"><?php echo esc_html( $o['city'] ); ?></p>
						<p class="pt-office__dispatch">24 Hour Dispatch</p>
						<p><?php foreach ( $o['dispatch'] as $i => [ $label, $num, $tel ] ) : ?><?php echo $i ? '<br>' : ''; ?><?php echo $label ? esc_html( $label ) . ' ' : ''; ?><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $num ); ?></a><?php endforeach; ?><br><a href="mailto:<?php echo esc_attr( $o['email'] ); ?>"><?php echo esc_html( $o['email'] ); ?></a></p>
						<p><strong><?php echo esc_html( $o['address'][0] ); ?></strong><br><?php echo esc_html( $o['address'][1] ); ?><br><?php echo esc_html( $o['address'][2] ); ?></p>
						<?php if ( $o['regions'] ) : ?><p><strong>Operating across</strong><br><?php echo esc_html( $o['regions'] ); ?></p><?php endif; ?>
						<p><a class="pt-office__dir" href="<?php echo esc_url( $o['directions'] ); ?>" rel="noopener">Get Directions</a></p>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="pt-form pt-section--grey">
		<div class="pt-container">
			<div class="pt-form__inner">
				<p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $form['eyebrow'] ?? 'Request a Quote' ); ?></p>
				<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $form['title'] ?? 'Tell us about your program' ); ?></h2>
				<?php if ( ! empty( $form['paras'][0] ) ) : ?><p><?php echo pt_kses( $form['paras'][0] ); ?></p><?php endif; ?>
				<?php pt_form( 'Main Contact Form' ); ?>
			</div>
		</div>
	</section>

	<section class="pt-ctabar pt-section--black">
		<div class="pt-ctabar__inner">
			<h2 class="pt-ctabar__title">Our Guarantee</h2>
			<p class="pt-ctabar__text"><?php echo esc_html( $form['paras'][1] ?? 'As your business partner, your success is our highest priority, while offering 24 hour support.' ); ?></p>
			<div class="pt-ctabar__actions"><a class="pt-btn" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Explore About ProTorque</a></div>
		</div>
	</section>

</main>
<?php get_footer(); ?>
