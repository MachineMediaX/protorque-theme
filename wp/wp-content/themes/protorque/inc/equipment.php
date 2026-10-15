<?php
/**
 * Equipment & Innovation landing: the product lineup grid.
 * Square tiles, product cutout at rest, field photo or video on hover (tap on touch), application filter.
 * Built from the approved equipment prototype (Kim, Oct 9 call; Shane, Oct 15).
 * Media lives in assets/img/equipment (cutouts, field photos, posters) and assets/media/equipment (loops).
 */

/** Per-product media and spec data keyed by page slug. Card titles, leads, links come from pages.json. */
function pt_equipment_media(): array {
	return [
		'crt-tether-ring'               => [ 'app' => 'safety',  'field' => 'video', 'specs' => [ [ 'Load rating', '3,000 lbs (1,665 kg)' ], [ 'Shock rating', '40,000 lbs (18,182 kg)' ] ] ],
		'mobile-bha-frame'              => [ 'app' => 'offline', 'field' => 'video', 'specs' => [ [ 'Pipe size range', '4 ½” to 12”' ], [ 'Torque range', '100,000 ft/lbs' ] ] ],
		'mobile-bucking-frame'          => [ 'app' => 'offline', 'field' => 'video', 'specs' => [ [ 'Flow rating', '40 to 60 GPM' ], [ 'Weight', '4,500 lbs (2,045 kg)' ] ] ],
		'rcd-press'                     => [ 'app' => 'mpd',     'field' => 'video', 'specs' => [ [ 'Press stroke', '36” (914.4 mm)' ], [ 'Max pressure', '3,000 PSI (20,700 kPa)' ] ] ],
		'electric-hydraulic-power-unit' => [ 'app' => 'power',   'field' => 'photo', 'specs' => [ [ 'Drive', 'Electric, no diesel' ] ] ],
		'vacuum-drift-system'           => [ 'app' => 'prep',    'field' => 'video', 'specs' => [ [ 'Airflow', '165 CFM at 1,150 RPM' ], [ 'Vacuum', '27 inHg' ] ] ],
		'break-out-vise'                => [ 'app' => 'offline', 'field' => 'photo', 'specs' => [ [ 'Torque rating', '40,000 to 90,000 ft/lbs' ], [ 'Tubular OD', '4” to 14”' ] ] ],
		'crt-nubbin'                    => [ 'app' => 'safety',  'field' => 'photo', 'specs' => [ [ 'Sizing', '4.5” to 13.38”' ], [ 'Connections', 'API and most premium' ] ] ],
		'pipe-roller'                   => [ 'app' => 'prep',    'field' => 'photo', 'specs' => [ [ 'Casing sizes', '4.5” to 9.63”' ], [ 'Handling weight', '11 to 14 lbs' ] ] ],
		'flush-mounted-spider'          => [ 'app' => 'safety',  'field' => 'video', 'specs' => [ [ 'Tension rating', '350 tons' ], [ 'Casing range', '2.375” to 13.625”' ] ] ],
	];
}

function pt_equipment_apps(): array {
	return [ 'all' => 'All', 'safety' => 'Rig floor safety', 'offline' => 'Offline make-up', 'mpd' => 'MPD', 'prep' => 'Casing prep', 'power' => 'Power' ];
}

/** Render the lineup section. $s is the 'services' section from pages.json for the equipment landing. */
function pt_equipment_lineup( array $s ): void {
	$dir   = get_template_directory_uri();
	$media = pt_equipment_media();
	?>
	<section class="pt-eqlineup pt-section--black">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--yellow"><?php echo esc_html( $s['eyebrow'] ?? 'Equipment Lineup' ); ?></p>
				<h2 class="pt-h2"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php pt_paras( $s['intro'] ?? [], 'pt-lede' ); ?>
				<p class="pt-eqlineup__by">Engineered and supplied by <a href="https://www.prostarequipment.com/" rel="noopener">ProStar Equipment</a>.</p>
			</div>
			<div class="pt-eqfilters" role="group" aria-label="Filter by application">
				<?php foreach ( pt_equipment_apps() as $key => $label ) : ?>
				<button class="pt-eqchip" type="button" data-app="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo 'all' === $key ? 'true' : 'false'; ?>"><?php echo esc_html( $label ); ?></button>
				<?php endforeach; ?>
			</div>
			<ul class="pt-eqgrid">
				<?php foreach ( $s['cards'] as $c ) :
					$slug = basename( untrailingslashit( (string) ( $c['url'] ?? '' ) ) );
					$m    = $media[ $slug ] ?? [ 'app' => 'other', 'field' => null, 'specs' => [] ];
					$prod = "$dir/assets/img/equipment/$slug.png";
					$fimg = "$dir/assets/img/equipment/$slug-field.jpg";
					$fvid = "$dir/assets/media/equipment/$slug-field.mp4";
					$short = preg_replace( '/ \(.*\)/', '', $c['title'] );
					?>
				<li class="pt-eq<?php echo $m['field'] ? ' has-field' : ''; ?>" data-app="<?php echo esc_attr( $m['app'] ); ?>">
					<div class="pt-eq__stage">
						<?php if ( 'video' === $m['field'] ) : ?>
						<video class="pt-eq__field" src="<?php echo esc_url( $fvid ); ?>" poster="<?php echo esc_url( $fimg ); ?>" muted loop playsinline preload="none" aria-hidden="true"></video>
						<?php elseif ( 'photo' === $m['field'] ) : ?>
						<img class="pt-eq__field" src="<?php echo esc_url( $fimg ); ?>" alt="" loading="lazy">
						<?php endif; ?>
						<?php if ( $m['field'] ) : ?>
						<span class="pt-eq__shade"></span>
						<span class="pt-eq__tag">In the field</span>
						<button class="pt-eq__toggle" type="button" aria-pressed="false">In the field</button>
						<?php endif; ?>
						<img class="pt-eq__prod" src="<?php echo esc_url( $prod ); ?>" alt="<?php echo esc_attr( $c['title'] ); ?>" loading="lazy">
					</div>
					<div class="pt-eq__body">
						<h3 class="pt-eq__name"><?php echo esc_html( $c['title'] ); ?></h3>
						<?php if ( ! empty( $c['lead'] ) ) : ?><p class="pt-eq__lead"><?php echo esc_html( $c['lead'] ); ?></p><?php endif; ?>
						<?php if ( ! empty( $m['specs'] ) ) : ?>
						<dl class="pt-eq__specs">
							<?php foreach ( $m['specs'] as $sp ) : ?>
							<div><dt><?php echo esc_html( $sp[0] ); ?></dt><dd><?php echo esc_html( $sp[1] ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
						<?php endif; ?>
						<a class="pt-eq__link" href="<?php echo esc_url( pt_url( $c['url'] ) ); ?>"><?php echo esc_html( $c['cta'] ?? ( 'Explore ' . $short ) ); ?></a>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}
