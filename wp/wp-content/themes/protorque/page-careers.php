<?php
/**
 * Careers. Dev-site copy; open positions come from the Jobs post type (pt_job).
 */
$pt_page  = pt_content_page( 'careers' );
$img   = get_template_directory_uri() . '/assets/img';
$secs  = $pt_page['sections'] ?? [];
$hero  = $secs[0] ?? [ 'title' => 'Careers at ProTorque', 'sub' => '' ];
$overs = array_values( array_filter( $secs, function ( $s ) { return $s['kind'] === 'overview'; } ) );
$jobsS = null;
$cta   = null;
foreach ( $secs as $s ) {
	if ( $s['kind'] === 'jobs' ) { $jobsS = $s; }
	if ( $s['kind'] === 'cta' ) { $cta = $s; }
}
$jobs = get_posts( [ 'post_type' => 'pt_job', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC' ] );
get_header();
?>
<main id="main" class="pt-content pt-careers">

	<section class="pt-pagehero pt-pagehero--sub">
		<img src="<?php echo esc_url( "$img/heroes/careers.jpg" ); ?>" alt="" width="1440" height="810">
		<div class="pt-pagehero__scrim"></div>
		<div class="pt-pagehero__text">
			<h1 class="pt-pagehero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
			<?php if ( ! empty( $hero['sub'] ) ) : ?><p class="pt-pagehero__sub"><?php echo esc_html( $hero['sub'] ); ?></p><?php endif; ?>
			<a class="pt-btn" href="#positions">Explore Open Positions</a>
		</div>
	</section>

	<?php if ( ! empty( $overs[0] ) ) : ?>
	<section class="pt-svc-overview pt-svc-overview--light">
		<div class="pt-container">
			<div class="pt-svc-overview__inner">
				<p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $overs[0]['eyebrow'] ?? 'Why ProTorque' ); ?></p>
				<h2 class="pt-h2"><?php echo esc_html( pt_title_case( $overs[0]["title"] ) ); ?></h2>
				<?php pt_paras( $overs[0]['paras'] ?? [] ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $overs[1] ) ) : ?>
	<section class="pt-overview pt-section--black">
		<div class="pt-container pt-overview__grid">
			<div class="pt-overview__photo"><img src="<?php echo esc_url( "$img/content/024A7522-small.jpg" ); ?>" alt="" loading="lazy"></div>
			<div class="pt-overview__text">
				<p class="pt-eyebrow pt-eyebrow--yellow"><?php echo esc_html( $overs[1]['eyebrow'] ?? 'Where we work' ); ?></p>
				<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( pt_title_case( $overs[1]['title'] ) ); ?></h2>
				<?php pt_paras( $overs[1]['paras'] ?? [] ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="pt-jobs" id="positions">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $jobsS['eyebrow'] ?? 'Open positions' ); ?></p>
				<h2 class="pt-h2"><?php echo esc_html( pt_title_case( $jobsS['title'] ?? 'Current opportunities' ) ); ?></h2>
			</div>
			<?php if ( $jobs ) : ?>
			<div class="pt-jobs__list">
				<?php foreach ( $jobs as $j ) :
					$m = function ( $k ) use ( $j ) { return get_post_meta( $j->ID, '_pt_job_' . $k, true ); };
					$meta = array_filter( [ $m( 'discipline' ), $m( 'employment_type' ), trim( $m( 'city' ) . ( $m( 'region' ) ? ', ' . $m( 'region' ) : '' ) ), $m( 'country' ) ] );
					$email = $m( 'apply_email' ) ?: 'careers@ptenergy.com';
					?>
				<details class="pt-job" id="job-<?php echo (int) $j->ID; ?>">
					<summary>
						<div>
							<p class="pt-job__title"><?php echo esc_html( get_the_title( $j ) ); ?></p>
							<p class="pt-job__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?><?php if ( $m( 'closing_date' ) ) : ?> · Closes <?php echo esc_html( date_i18n( 'F j, Y', strtotime( $m( 'closing_date' ) ) ) ); ?><?php endif; ?></p>
						</div>
						<span class="pt-job__chev" aria-hidden="true"></span>
					</summary>
					<div class="pt-job__body">
						<?php echo wp_kses_post( apply_filters( 'the_content', $j->post_content ) ); ?>
						<a class="pt-btn pt-job__apply" href="#apply" data-position="<?php echo esc_attr( get_the_title( $j ) ); ?>">Apply</a>
					</div>
				</details>
				<?php endforeach; ?>
			</div>
			<?php else : ?>
			<p class="pt-lede pt-center" style="margin-inline:auto">There are no open positions posted right now. Submit a general application below and we will keep it on file.</p>
			<?php endif; ?>
		</div>
	</section>

	<?php pt_careers_render(); ?>

</main>
<?php get_footer(); ?>
