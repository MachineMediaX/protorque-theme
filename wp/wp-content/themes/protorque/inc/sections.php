<?php
/**
 * Section renderer for the content pages built from inc/content/pages.json
 * (service landings, Template 2 service detail pages, equipment pages, careers, contact, news).
 *
 * Each page is a list of sections; each section has a kind and the fields the kind needs.
 * Backgrounds alternate so two light sections never sit on top of each other.
 */

function pt_content_pages() {
	static $pages = null;
	if ( $pages === null ) {
		$pages = json_decode( file_get_contents( get_template_directory() . '/inc/content/pages.json' ), true ) ?: [];
	}
	return $pages;
}

function pt_content_page( $slug ) {
	return pt_content_pages()[ $slug ] ?? null;
}

function pt_img( $rel ) {
	return get_template_directory_uri() . '/assets/img/' . ltrim( $rel, '/' );
}

/** Hero photo for a page: a dedicated frame from the client's drone footage, or a midstream photo. */
function pt_hero_image( $slug ) {
	$map = [
		'midstream-construction'                           => 'midstream/detail-construction.jpg',
		'plant-and-pipeline-maintenance'                   => 'content/plant-and-pipeline-maintenance.jpg',
		'facilities-electrical-instrumentation-mechanical' => 'content/facilities-and-electrical.jpg',
		'environmental-and-water-management'               => 'content/environmental-water-card.jpg',
		'row-services'                                     => 'midstream/hero.jpg',
		'specialty-piping'                                 => 'content/specialty-piping.jpg',
	];
	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}
	if ( file_exists( get_template_directory() . "/assets/img/heroes/$slug.jpg" ) ) {
		return "heroes/$slug.jpg";
	}
	return null;
}

function pt_kses( $html ) {
	return wp_kses( $html, [ 'strong' => [], 'em' => [], 'b' => [], 'i' => [], 'br' => [], 'a' => [ 'href' => [], 'title' => [] ] ] );
}

function pt_url( $u ) {
	if ( ! $u || $u === '#' ) {
		return '#';
	}
	if ( preg_match( '#^(https?:|mailto:|tel:)#', $u ) ) {
		return $u;
	}
	return home_url( $u );
}

function pt_paras( $list, $class = '' ) {
	foreach ( (array) $list as $p ) {
		echo '<p' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . pt_kses( $p ) . '</p>';
	}
}

function pt_section_head( $s, $align = 'center', $eyebrow_class = 'pt-eyebrow--red' ) {
	$wrap = $align === 'center' ? 'pt-center' : '';
	echo '<div class="' . $wrap . '">';
	if ( ! empty( $s['eyebrow'] ) ) {
		echo '<p class="pt-eyebrow ' . esc_attr( $eyebrow_class ) . '">' . esc_html( $s['eyebrow'] ) . '</p>';
	}
	if ( ! empty( $s['title'] ) ) {
		echo '<h2 class="pt-h2' . ( $align === 'left' ? ' pt-h2--left' : '' ) . '">' . esc_html( $s['title'] ) . '</h2>';
	}
	echo '</div>';
}

/** Choose a light background that differs from the previous section's. */
function pt_light_bg( &$prev ) {
	$bg   = $prev === 'white' ? 'grey' : 'white';
	$prev = $bg;
	return $bg === 'grey' ? ' pt-section--grey' : '';
}

function pt_render_page( $page ) {
	$slug = $page['slug'];
	$prev = 'hero';
	echo '<main id="main" class="pt-content pt-content--' . esc_attr( $page['kind'] ) . ' pt-page-' . esc_attr( $slug ) . '">';
	foreach ( $page['sections'] as $s ) {
		pt_render_section( $s, $page, $prev );
	}
	echo '</main>';
}

function pt_render_section( $s, $page, &$prev ) {
	$slug = $page['slug'];
	switch ( $s['kind'] ) {

		case 'hero':
			if ( $page['kind'] === 'equipment' ) {
				$prev = 'black';
				?>
				<section class="pt-eqhero pt-section--black">
					<div class="pt-container pt-eqhero__grid">
						<div class="pt-eqhero__text">
							<p class="pt-eyebrow pt-eyebrow--yellow">Equipment &amp; Innovation</p>
							<h1 class="pt-pagehero__title pt-eqhero__title"><?php echo esc_html( $s['title'] ); ?></h1>
							<?php if ( ! empty( $s['sub'] ) ) : ?><p class="pt-pagehero__sub"><?php echo esc_html( $s['sub'] ); ?></p><?php endif; ?>
							<p class="pt-eqhero__prostar">Engineered and supplied by <a href="https://www.prostarequipment.com/" rel="noopener">ProStar</a>, ProTorque&rsquo;s equipment division.</p>
							<a class="pt-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Request a Quote</a>
						</div>
						<?php if ( ! empty( $s['image'] ) ) : ?>
						<div class="pt-eqhero__photo"><img src="<?php echo esc_url( pt_img( $s['image'] ) ); ?>" alt="<?php echo esc_attr( $s['title'] ); ?>" width="800" height="800"></div>
						<?php endif; ?>
					</div>
				</section>
				<?php
				break;
			}
			$hero = pt_hero_image( $slug );
			$prev = 'hero';
			$long = mb_strlen( $s['title'] ) > 26 ? ' pt-pagehero__title--long' : '';
			?>
			<section class="pt-pagehero pt-pagehero--sub<?php echo $hero ? '' : ' pt-pagehero--plain'; ?>">
				<?php if ( $hero ) : ?><img src="<?php echo esc_url( pt_img( $hero ) ); ?>" alt="" width="1440" height="810"><div class="pt-pagehero__scrim"></div><?php endif; ?>
				<div class="pt-pagehero__text">
					<h1 class="pt-pagehero__title<?php echo $long; ?>"><?php echo esc_html( $s['title'] ); ?></h1>
					<?php if ( ! empty( $s['sub'] ) ) : ?><p class="pt-pagehero__sub"><?php echo esc_html( $s['sub'] ); ?></p><?php endif; ?>
					<?php if ( $page['kind'] === 'service' ) : ?><a class="pt-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Get in Touch</a><?php endif; ?>
				</div>
			</section>
			<?php
			if ( ! empty( $s['paras'] ) ) : // Contact: red intro band
				$prev = 'red';
				?>
				<section class="pt-band-text pt-section--red"><div class="pt-container"><?php pt_paras( $s['paras'], 'pt-band-text__p' ); ?></div></section>
				<?php
			endif;
			break;

		case 'overview':
			$has_img   = ! empty( $s['images'] );
			$has_media = ! empty( $s['video'] ) || ! empty( $s['gallery'] );
			if ( $has_img ) {
				$prev = 'white';
				?>
				<section class="pt-overview">
					<div class="pt-container pt-overview__grid">
						<div class="pt-overview__photo"><img src="<?php echo esc_url( pt_img( $s['images'][0] ) ); ?>" alt="" loading="lazy"></div>
						<div class="pt-overview__text">
							<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
							<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php pt_paras( $s['paras'] ?? [] ); ?>
						</div>
					</div>
				</section>
				<?php
			} elseif ( ! empty( $s['tiles'] ) || $has_media || $page['kind'] !== 'service' ) {
				$prev = 'white';
				?>
				<section class="pt-svc-overview pt-svc-overview--light">
					<div class="pt-container">
						<div class="pt-svc-overview__inner">
							<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
							<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php pt_paras( $s['paras'] ?? [] ); ?>
						</div>
						<?php if ( $has_media ) : ?>
						<div class="pt-media<?php echo empty( $s['video'] ) ? ' pt-media--gallery-only' : ''; ?>">
							<?php if ( ! empty( $s['video'] ) ) : ?>
							<div class="pt-media__video"><iframe src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $s['video'] ); ?>?rel=0" title="<?php echo esc_attr( $page['title'] ); ?> video" loading="lazy" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
							<?php endif; ?>
							<?php if ( ! empty( $s['gallery'] ) ) : ?>
							<ul class="pt-media__gallery">
								<?php foreach ( array_filter( $s['gallery'] ) as $g ) : ?><li><img src="<?php echo esc_url( pt_img( $g ) ); ?>" alt="" loading="lazy"></li><?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
				</section>
				<?php
			} else {
				$prev = 'black';
				?>
				<section class="pt-svc-overview pt-section--black">
					<div class="pt-container">
						<div class="pt-svc-overview__inner">
							<p class="pt-eyebrow pt-eyebrow--yellow"><?php echo esc_html( $s['eyebrow'] ?? 'Overview' ); ?></p>
							<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php pt_paras( $s['paras'] ?? [] ); ?>
						</div>
					</div>
				</section>
				<?php
			}
			if ( ! empty( $s['tiles'] ) ) {
				$prev = 'red';
				?>
				<section class="pt-panel pt-section--red">
					<div class="pt-container">
						<dl class="pt-panel__tiles">
							<?php foreach ( $s['tiles'] as $t ) : ?><div><dt><?php echo esc_html( $t['value'] ); ?></dt><dd><?php echo esc_html( $t['label'] ); ?></dd></div><?php endforeach; ?>
						</dl>
					</div>
				</section>
				<?php
			}
			break;

		case 'list':
			$items    = $s['items'] ?? [];
			$intro    = $s['intro'] ?? [];
			$two_col  = count( $intro ) >= 2;
			$eyebrow  = $s['eyebrow'] ?? '';
			$title    = $s['title'] ?? '';
			// "What the service includes" lives in the eyebrow on some pages and in the title on others. Keep it as the heading.
			if ( stripos( $eyebrow, 'what the service includes' ) !== false ) {
				array_unshift( $intro, $title );
				$title   = 'What the service includes';
				$eyebrow = '';
			}
			if ( $two_col ) {
				$prev = 'white';
				?>
				<section class="pt-textlist">
					<div class="pt-container pt-textlist__grid">
						<div class="pt-textlist__text">
							<?php if ( $eyebrow ) : ?><p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
							<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $title ); ?></h2>
							<?php pt_paras( $intro ); ?>
						</div>
						<ul class="pt-rulelist">
							<?php foreach ( $items as $it ) : ?>
								<?php if ( $it['kind'] === 'subhead' ) : ?><li class="pt-rulelist__head"><?php echo esc_html( $it['text'] ); ?></li>
								<?php else : ?><li><?php echo esc_html( $it['text'] ); ?></li><?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
				<?php
			} else {
				$prev = 'red';
				?>
				<section class="pt-includes pt-section--red">
					<div class="pt-container">
						<div class="pt-center">
							<?php if ( $eyebrow ) : ?><p class="pt-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
							<h2 class="pt-h2"><?php echo esc_html( $title ); ?></h2>
							<?php pt_paras( $intro, 'pt-lede' ); ?>
						</div>
						<ul class="pt-checklist">
							<?php foreach ( $items as $it ) : ?>
								<?php if ( $it['kind'] === 'subhead' ) : ?><li class="pt-checklist__head"><?php echo esc_html( $it['text'] ); ?></li>
								<?php else : ?>
								<li><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"/></svg><span><?php echo esc_html( $it['text'] ); ?></span></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
				<?php
			}
			break;

		case 'process':
			$bg = pt_light_bg( $prev );
			?>
			<section class="pt-process<?php echo $bg; ?>">
				<div class="pt-container">
					<?php pt_section_head( [ 'eyebrow' => $s['eyebrow'] ?? 'How We Do It', 'title' => $s['title'] ?: 'Our process' ] ); ?>
					<ol class="pt-principles__grid pt-process__grid">
						<?php foreach ( $s['steps'] as $i => $st ) : ?>
						<li class="pt-principle pt-step">
							<p class="pt-step__eyebrow">Step <?php echo (int) $i + 1; ?></p>
							<h3><?php echo esc_html( $st['title'] ); ?></h3>
							<p><?php echo pt_kses( $st['text'] ); ?></p>
						</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</section>
			<?php
			break;

		case 'why':
			$prev = 'black';
			?>
			<section class="pt-why pt-section--black">
				<div class="pt-container pt-why__grid">
					<div class="pt-why__text">
						<p class="pt-eyebrow pt-eyebrow--yellow">Why This Matters</p>
						<h2 class="pt-h2 pt-h2--left"><?php echo esc_html( $s['title'] ?: 'Why this matters' ); ?></h2>
						<?php pt_paras( $s['paras'] ?? [] ); ?>
					</div>
					<?php if ( ! empty( $s['quote'] ) ) : ?>
					<blockquote class="pt-quote"><p><?php echo pt_kses( trim( $s['quote'], " \"\u{201C}\u{201D}" ) ); ?></p></blockquote>
					<?php endif; ?>
				</div>
			</section>
			<?php
			break;

		case 'related':
		case 'slider':
			$bg    = pt_light_bg( $prev );
			$cards = $s['kind'] === 'slider' ? array_map( function ( $sl ) {
				return [ 'img' => $sl['img'], 'title' => $sl['title'], 'lead' => $sl['subtitle'], 'text' => $sl['text'], 'cta' => $sl['cta'], 'url' => $sl['url'] ];
			}, $s['slides'] ?? [] ) : $s['cards'];
			$eyebrow = $s['eyebrow'] ?? ( $page['kind'] === 'equipment' ? 'In The Field' : 'Connected Services' );
			?>
			<section class="pt-related<?php echo $bg; ?>">
				<div class="pt-container">
					<div class="pt-center">
						<p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $eyebrow ); ?></p>
						<h2 class="pt-h2"><?php echo esc_html( $s['title'] ); ?></h2>
						<?php pt_paras( $s['intro'] ?? [], 'pt-lede' ); ?>
					</div>
					<ul class="pt-rcards pt-rcards--<?php echo count( $cards ) >= 4 ? '4' : '3'; ?>">
						<?php foreach ( $cards as $c ) : ?>
						<li class="pt-rcard">
							<?php if ( ! empty( $c['img'] ) ) : ?><a class="pt-rcard__img" href="<?php echo esc_url( pt_url( $c['url'] ) ); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo esc_url( pt_img( $c['img'] ) ); ?>" alt="" loading="lazy"></a><?php endif; ?>
							<div class="pt-rcard__body">
								<h3 class="pt-svc__title"><?php echo esc_html( $c['title'] ); ?></h3>
								<?php if ( ! empty( $c['lead'] ) ) : ?><p class="pt-svc__lead"><?php echo esc_html( $c['lead'] ); ?></p><?php endif; ?>
								<p class="pt-svc__body"><?php echo pt_kses( $c['text'] ); ?></p>
								<?php if ( ! empty( $c['cta'] ) ) : ?><a class="pt-svc__link" href="<?php echo esc_url( pt_url( $c['url'] ) ); ?>"><?php echo esc_html( $c['cta'] ); ?></a><?php endif; ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
			break;

		case 'services':
			$prev = 'charcoal';
			$n    = count( $s['cards'] );
			?>
			<section class="pt-services pt-section--charcoal">
				<div class="pt-container">
					<div class="pt-center">
						<p class="pt-eyebrow pt-eyebrow--yellow"><?php echo esc_html( $s['eyebrow'] ?? ( $n . ' ' . ( $page['kind'] === 'landing' && $slug === 'equipment-and-innovation' ? 'Products' : 'Service Lines' ) ) ); ?></p>
						<h2 class="pt-h2"><?php echo esc_html( $s['title'] ); ?></h2>
						<?php pt_paras( $s['intro'] ?? [], 'pt-lede' ); ?>
					</div>
					<ul class="pt-services__grid">
						<?php foreach ( $s['cards'] as $c ) : ?>
						<li class="pt-svc<?php echo ! empty( $c['img'] ) ? ' pt-svc--thumb' : ''; ?>">
							<?php if ( ! empty( $c['img'] ) ) : ?><a class="pt-svc__thumb" href="<?php echo esc_url( pt_url( $c['url'] ) ); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo esc_url( pt_img( $c['img'] ) ); ?>" alt="" loading="lazy"></a><?php endif; ?>
							<p class="pt-svc__eyebrow"><?php echo esc_html( str_pad( $c['n'], 2, '0', STR_PAD_LEFT ) ); ?></p>
							<h3 class="pt-svc__title"><?php echo esc_html( $c['title'] ); ?></h3>
							<?php if ( ! empty( $c['lead'] ) ) : ?><p class="pt-svc__lead"><?php echo esc_html( $c['lead'] ); ?></p><?php endif; ?>
							<p class="pt-svc__body"><?php echo pt_kses( $c['text'] ); ?></p>
							<a class="pt-svc__link" href="<?php echo esc_url( pt_url( $c['url'] ) ); ?>"><?php echo esc_html( $c['cta'] ); ?></a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
			break;

		case 'tabs':
			$bg = pt_light_bg( $prev );
			$id = 'tabs-' . sanitize_title( $s['title'] );
			?>
			<section class="pt-tabs<?php echo $bg; ?>" data-tabs>
				<div class="pt-container">
					<?php pt_section_head( [ 'eyebrow' => $s['eyebrow'] ?? 'Our Approach', 'title' => $s['title'] ] ); ?>
					<div class="pt-tabs__grid<?php echo empty( $s['images'] ) ? ' pt-tabs__grid--noimg' : ''; ?>">
						<div class="pt-tabs__panelwrap">
							<div class="pt-tabs__list" role="tablist" aria-label="<?php echo esc_attr( $s['title'] ); ?>">
								<?php foreach ( $s['tabs'] as $i => $t ) : ?>
								<button class="pt-tabs__tab<?php echo $i === 0 ? ' is-active' : ''; ?>" role="tab" id="<?php echo $id . '-tab-' . $i; ?>" aria-controls="<?php echo $id . '-panel-' . $i; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>" tabindex="<?php echo $i === 0 ? '0' : '-1'; ?>"><?php echo esc_html( $t['label'] ); ?></button>
								<?php endforeach; ?>
							</div>
							<?php foreach ( $s['tabs'] as $i => $t ) : ?>
							<div class="pt-tabs__panel<?php echo $i === 0 ? ' is-active' : ''; ?>" role="tabpanel" id="<?php echo $id . '-panel-' . $i; ?>" aria-labelledby="<?php echo $id . '-tab-' . $i; ?>"<?php echo $i === 0 ? '' : ' hidden'; ?>>
								<?php if ( ! empty( $t['eyebrow'] ) ) : ?><p class="pt-eyebrow pt-eyebrow--red"><?php echo esc_html( $t['eyebrow'] ); ?></p><?php endif; ?>
								<h3 class="pt-tabs__title"><?php echo esc_html( $t['title'] ); ?></h3>
								<p><?php echo pt_kses( $t['text'] ); ?></p>
							</div>
							<?php endforeach; ?>
						</div>
						<?php if ( ! empty( $s['images'] ) ) : ?>
						<div class="pt-tabs__photo"><img src="<?php echo esc_url( pt_img( $s['images'][0] ) ); ?>" alt="" loading="lazy"></div>
						<?php endif; ?>
					</div>
				</div>
			</section>
			<?php
			break;

		case 'specs':
			$bg     = pt_light_bg( $prev );
			$tables = array_values( array_filter( $s['tables'] ?? [] ) );
			$caps   = array_values( array_map( function ( $i ) { return $i['text']; }, array_filter( $s['items'] ?? [], function ( $i ) { return $i['kind'] === 'subhead'; } ) ) );
			?>
			<section class="pt-specs<?php echo $bg; ?>">
				<div class="pt-container">
					<?php pt_section_head( [ 'eyebrow' => 'Specifications', 'title' => $s['title'] ] ); ?>
					<div class="pt-specs__tables">
						<?php foreach ( $tables as $k => $tb ) : ?>
						<table class="pt-table">
							<?php if ( $k > 0 && isset( $caps[ $k - 1 ] ) ) : ?><caption><?php echo esc_html( $caps[ $k - 1 ] ); ?></caption><?php endif; ?>
							<tbody>
								<?php foreach ( $tb['rows'] as $row ) : ?>
								<tr><th scope="row"><?php echo esc_html( $row[0] ); ?></th><td><?php echo nl2br( esc_html( $row[1] ?? '' ) ); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php endforeach; ?>
					</div>
					<?php if ( ! empty( $s['paras'] ) ) : ?><div class="pt-specs__note"><?php pt_paras( $s['paras'] ); ?></div><?php endif; ?>
				</div>
			</section>
			<?php
			break;

		case 'benefits':
			$prev = 'black';
			?>
			<section class="pt-benefits pt-section--black">
				<div class="pt-container">
					<?php pt_section_head( [ 'eyebrow' => 'Why Operators Use It', 'title' => $s['title'] ], 'center', 'pt-eyebrow--yellow' ); ?>
					<div class="pt-benefits__cols">
						<?php foreach ( $s['benefits'] as $b ) : ?>
						<div><h3><?php echo esc_html( $b['title'] ); ?></h3><p><?php echo pt_kses( $b['text'] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
			break;

		case 'cta':
			$prev = 'black';
			?>
			<section class="pt-ctabar pt-section--black">
				<div class="pt-ctabar__inner">
					<h2 class="pt-ctabar__title"><?php echo esc_html( $s['title'] ?? 'Get in Touch' ); ?></h2>
					<p class="pt-ctabar__text"><?php echo pt_kses( implode( ' ', $s['paras'] ?? [] ) ); ?></p>
					<div class="pt-ctabar__actions">
						<?php foreach ( $s['buttons'] ?? [ [ 'text' => 'Get in Touch', 'url' => '/contact-us/' ] ] as $b ) : ?>
						<a class="pt-btn" href="<?php echo esc_url( pt_url( $b['url'] ) ); ?>"><?php echo esc_html( $b['text'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
			break;

		default:
			do_action( 'pt_render_section_' . $s['kind'], $s, $page );
	}
}
