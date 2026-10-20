<?php
/**
 * Content migrations: one-off database fixes that ship with the theme and run once per site.
 * Each step is keyed in the pt_migrations option so a redeploy never repeats it.
 * Source: claude/261020-Blog-Migration-and-Redirects.md (blog migration check against the live site).
 */

function pt_migration_done( $key ) {
	return in_array( $key, (array) get_option( 'pt_migrations', [] ), true );
}
function pt_migration_mark( $key ) {
	$done   = (array) get_option( 'pt_migrations', [] );
	$done[] = $key;
	update_option( 'pt_migrations', array_values( array_unique( $done ) ), false );
}

/** 2026-10-20: blog slugs matched to the live site, titles fixed, Hello world removed, Cold Weather post added. */
function pt_migration_blog_2026_10_20() {
	if ( pt_migration_done( 'blog-2026-10-20' ) ) {
		return;
	}

	// 1. Delete the WordPress sample content.
	foreach ( [ [ 'hello-world', 'post' ], [ 'sample-page', 'page' ] ] as $s ) {
		$p = get_page_by_path( $s[0], OBJECT, $s[1] );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}

	// 2. Slugs that differed from the live site (only the /portfolio/ prefix changes at cutover).
	$slugs = [
		'hook-load-torque-verification-keep-your-top-drive-and-iron-roughneck-honest'           => 'hook-load-torque-verification',
		're-thinking-rig-hydraulics-for-trs-why-electric-hpus-are-replacing-diesel-units'       => 'rethinking-rig-hydraulics-for-trs-why-electric-hpus-are-replacing-diesel-units',
		'clearing-the-way-protorques-pipeline-maintenance-and-mowing-crews'                     => 'protorques-pipeline-maintenance-and-mowing-crews',
		'powering-reliability-protorques-electrical-capabilities-in-midstream-operations'       => 'electrical-capabilities-in-midstream-operations',
		'protorques-fleet-camera-systems-driving-safety-and-accountability'                     => 'protorque-fleet-camera-systems-driving-safety-and-accountability',
		'precision-through-training-catm-and-power-tong-competency'                             => 'precision-through-training',
		'enhancing-midstream-maintenance-through-digitalization-and-safety'                     => 'ptem-enhancing-midstream-maintenance-through-digitalization-and-safety',
		'why-competency-and-compliance-matter'                                                  => 'why-competency-and-compliance-matter-protorques-investment-in-workhub',
		'maximizing-efficiency-in-casing-runs-time-saving-thread-protector-solution'            => 'time-saving-thread-protector-solution',
		'drilling-down-to-excellence-protorques-competency-program'                             => 'drilling-down-to-excellence-protorque-competency-program',
		'protorque-celebrates-20-years-of-innovation-and-leadership-in-total-rig-solutions-trs' => 'protorque-celebrates-20-years',
	];
	foreach ( $slugs as $old => $new ) {
		$p = get_page_by_path( $old, OBJECT, 'post' );
		if ( $p ) {
			wp_update_post( [ 'ID' => $p->ID, 'post_name' => $new ] );
		}
	}

	// 3. Titles: restore the live title on Workhub; drop the spaced hyphen on the thread protector post.
	$titles = [
		'why-competency-and-compliance-matter-protorques-investment-in-workhub' => 'Why Competency and Compliance Matter: ProTorque’s Investment in Workhub',
		'time-saving-thread-protector-solution'                                 => 'Maximizing Efficiency in Casing Runs: Time-Saving Thread Protector Solution',
	];
	foreach ( $titles as $slug => $title ) {
		$p = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $p ) {
			wp_update_post( [ 'ID' => $p->ID, 'post_title' => $title ] );
		}
	}

	// 4. Cold Weather post (live Oct 14, 2026; missing from the export). Copy: 261007-ProTorque_Cold_Weather_Blog_Final.
	if ( ! get_page_by_path( 'cold-weather-casing-running', OBJECT, 'post' ) ) {
		$author = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
		$news   = get_term_by( 'slug', 'news', 'category' );
		$id     = wp_insert_post( [
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_name'     => 'cold-weather-casing-running',
			'post_title'    => 'Cold Weather Casing Running: Compound, Hydraulics, and the Crew',
			'post_date'     => '2026-10-14 09:00:00',
			'post_author'   => $author ? (int) $author[0] : 1,
			'post_excerpt'  => 'How cold weather affects casing running: thread compound, tong and CRT hydraulics, and the crew practices that keep make-up on target below freezing.',
			'post_content'  => pt_cold_weather_post_content(),
			'post_category' => $news ? [ (int) $news->term_id ] : [],
			'meta_input'    => [
				'_pt_seo_title'        => 'Cold Weather Casing Running: Compound and Hydraulics',
				'_pt_meta_description' => 'How cold weather affects casing running: thread compound, tong and CRT hydraulics, and the crew practices that keep make-up on target below freezing.',
			],
		] );
		if ( $id && ! is_wp_error( $id ) && function_exists( 'pt_seed_news_media' ) ) {
			delete_option( 'pt_news_media_complete' );
			pt_seed_news_media();
		}
	}

	flush_rewrite_rules();
	pt_migration_mark( 'blog-2026-10-20' );
}

function pt_cold_weather_post_content() {
	$h = function ( $t ) { return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">$t</h3>\n<!-- /wp:heading -->\n\n"; };
	$p = function ( $t ) { return "<!-- wp:paragraph -->\n<p>$t</p>\n<!-- /wp:paragraph -->\n\n"; };
	return
		$p( 'Cold changes the physics on a casing job before it changes anything else. Hydraulic fluid thickens, thread compound stiffens, and a crew&rsquo;s working window shortens. None of that changes what a good connection looks like on the torque-turn graph. It changes what it takes to get there.' ) .
		$h( 'Thread compound behaves differently' ) .
		$p( 'Compound rated for warm conditions goes stiff in the cold and stops spreading evenly across the threads. API 5A3 requires thread compound to stay brushable in cold weather, and not every compound on the truck meets it. Uneven compound shows up as make-up torque out of window and, on shouldered connections, shoulders landing off target. The fix is a compound rated for the temperature on location, stored warm until it is applied, and applied the same way on every joint. Consistency matters more in January than in July because the margin for error is smaller.' ) .
		$h( 'Hydraulics and seals need a warm-up plan' ) .
		$p( 'Tongs, power units, and bucking equipment run on hydraulic fluid that thickens as the temperature falls. Cold fluid means slow response, higher pressure spikes, and seals under stress. Elastomer seals stiffen in the cold too, on the tongs, on the power unit, and on every tool that depends on them, which is one more reason the warm-up happens before the first connection. Equipment gets warmed before the first make-up, not during it, and the warm-up is part of the job plan rather than something the crew improvises when the first connection feels wrong.' ) .
		$h( 'The torque reading does not move with the cold' ) .
		$p( 'Hydraulic load cells lose accuracy as the temperature changes. ProTorque runs electronic load cells on its torque systems, which do not carry that error, and verifies them on a set schedule in its facilities rather than waiting for a cold job to show a problem. The graph reflects what happened to the threads, not what the weather did to the sensor.' ) .
		$h( 'Ice and snow on threads are an inspection problem' ) .
		$p( 'Moisture that freezes on a pin or box before make-up becomes debris under compound. Thread protectors stay on until the joint is picked up, threads are inspected and cleaned at the point of make-up, and anything that came off the truck with ice in the threads gets cleaned before it goes in the string. This is where <a href="/tubular-running-services/tubular-thread-cleaning-and-inspection/">tubular thread cleaning and inspection</a> earns its place on a winter program.' ) .
		$h( 'The crew is the first system that degrades' ) .
		$p( 'Cold shortens attention spans and fine motor control long before it stops work. Warm-up rotations, daylight planning, and a pre-job meeting that names who is watching for what keep the crew ahead of the conditions instead of reacting to them. A rushed connection in the cold is the one that gets pulled.' ) .
		$h( 'What doesn&rsquo;t change' ) .
		$p( 'Every connection still gets a torque-turn graph. Every graph still goes in the well file. The standard for a good make-up is the same at any temperature. Cold weather readiness is about protecting that standard when the conditions are working against it.' ) .
		$p( 'Planning a winter casing program? ProTorque crews run cold weather programs every season with the same documentation on every connection. <a href="/contact-us/">Contact us</a> at the planning stage.' );
}

/* Run once on the next request that has the database available; admin or front end. */
add_action( 'init', function () {
	if ( wp_installing() || pt_migration_done( 'blog-2026-10-20' ) ) {
		return;
	}
	pt_migration_blog_2026_10_20();
}, 20 );

/**
 * Launch redirects (301) from the old ptenergy.com URLs, crawled Oct 20, 2026 from the live sitemaps and the
 * Services and Products pages. Explicit map first, then the blog rule: /portfolio/<slug>/ to /<slug>/ for any post.
 */
function pt_legacy_redirects(): array {
	$eq  = '/equipment-and-innovation/';
	$trs = '/tubular-running-services/';
	$drl = '/drilling-services/';
	return [
		// Pages
		'/services'                                              => $trs,
		'/products'                                              => $eq,
		'/resources'                                             => '/news/',
		'/contact'                                               => '/contact-us/',
		'/careers-2024'                                          => '/careers/',
		'/careers-application-canada'                            => '/careers/',
		'/careers-application-usa-english'                       => '/careers/',
		'/careers-application-usa-spanish'                       => '/careers/',
		'/privacy'                                               => '/privacy-policy/',
		'/privacy-2'                                             => '/privacy-policy/',
		'/portfolio'                                             => '/news/',
		'/portfolio-category/news'                               => '/news/',
		'/portfolio-category/products'                           => $eq,
		'/portfolio-category/services'                           => $trs,
		// Services (old portfolio items)
		'/portfolio/tubular-thread-cleaning-inspection-services' => $trs . 'tubular-thread-cleaning-and-inspection/',
		'/portfolio/casing-cleaning-thread-inspection-services-2' => $trs . 'tubular-thread-cleaning-and-inspection/',
		'/portfolio/volant-casingrunning-tool'                   => $trs . 'casing-running-and-power-tong-services/',
		'/portfolio/conventional-casingrunning-services-and-power-tongs' => $trs . 'casing-running-and-power-tong-services/',
		'/portfolio/power-tong-tubular-running-services'         => $trs . 'casing-running-and-power-tong-services/',
		'/portfolio/computer-analyzed-torque-monitoring-catm-power-tongs' => $trs . 'catm-for-power-tongs/',
		'/portfolio/computer-analyzed-torque-monitoring-catm-top-drive'   => $trs . 'catm-for-top-drive/',
		'/portfolio/iron-roughnecktorque-testing'                => $drl . 'iron-roughneck-torque-testing/',
		'/portfolio/real-time-drilling-data-tts'                 => $drl . 'real-time-drilling-data/',
		'/portfolio/top-drive-hook-load'                         => $drl . 'top-drive-and-hook-load-verification/',
		'/portfolio/midstream-services'                          => '/midstream-services/',
		// Products (old portfolio items)
		'/portfolio/protorquebucking-frame'                      => $eq . 'mobile-bucking-frame/',
		'/portfolio/protorquebucking-frame-2'                    => $eq . 'mobile-bucking-frame/',
		'/portfolio/protorque-rcd-press'                         => $eq . 'rcd-press/',
		'/portfolio/protorque-rcd-press-2'                       => $eq . 'rcd-press/',
		'/portfolio/bha-frame'                                   => $eq . 'mobile-bha-frame/',
		'/portfolio/protorque-tether-bearing-ring-sub'           => $eq . 'crt-tether-ring/',
		'/portfolio/electric-hydraulic-power-unit'               => $eq . 'electric-hydraulic-power-unit/',
		'/portfolios/electric-hydraulic-power-unit'              => $eq . 'electric-hydraulic-power-unit/',
		'/portfolio/flush-mounted-spider'                        => $eq . 'flush-mounted-spider/',
		'/portfolio/break-out-frame'                             => $eq . 'break-out-vise/',
		'/portfolio/break-out-vise'                              => $eq . 'break-out-vise/',
		'/portfolio/crt-nubbin'                                  => $eq . 'crt-nubbin/',
		'/portfolio/pipe-roller'                                 => $eq . 'pipe-roller/',
		'/portfolio/protorque-vacuum-drift'                      => $eq . 'vacuum-drift-system/',
	];
}
add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}
	$path = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	$key  = '/' . trim( $path, '/' );
	$map  = pt_legacy_redirects();
	if ( isset( $map[ $key ] ) ) {
		wp_redirect( home_url( $map[ $key ] ), 301 );
		exit;
	}
	if ( preg_match( '#^/portfolio/([^/]+)/?$#', $path, $m ) ) {
		$post = get_page_by_path( $m[1], OBJECT, 'post' );
		wp_redirect( $post ? get_permalink( $post ) : home_url( '/news/' ), 301 );
		exit;
	}
}, 1 );
