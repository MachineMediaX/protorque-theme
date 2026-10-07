<?php
/**
 * Site setup: template routing for the content pages, the Careers job post type,
 * and first-run configuration when the theme is activated (menu location, front page, posts page, jobs import).
 */

/* Route landing, service and equipment pages to the content template without needing page-template meta. */
add_filter( 'template_include', function ( $template ) {
	if ( is_page() ) {
		$page = pt_content_page( get_post_field( 'post_name', get_queried_object_id() ) );
		if ( $page && in_array( $page['kind'], [ 'landing', 'service', 'equipment' ], true ) ) {
			return get_template_directory() . '/template-content.php';
		}
	}
	return $template;
} );

/* Careers: job post type with the fields the dev site used. */
add_action( 'init', function () {
	register_post_type( 'pt_job', [
		'labels'       => [ 'name' => 'Jobs', 'singular_name' => 'Job', 'add_new_item' => 'Add New Job', 'edit_item' => 'Edit Job', 'menu_name' => 'Careers' ],
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => [ 'slug' => 'careers/jobs', 'with_front' => false ],
		'menu_icon'    => 'dashicons-id-alt',
		'supports'     => [ 'title', 'editor', 'revisions' ],
		'show_in_rest' => true,
	] );
	foreach ( [ 'country', 'region', 'city', 'discipline', 'employment_type', 'closing_date', 'apply_email' ] as $f ) {
		register_post_meta( 'pt_job', '_pt_job_' . $f, [ 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ] );
	}
} );

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'pt_job_details', 'Job details', function ( $post ) {
		wp_nonce_field( 'pt_job_details', 'pt_job_nonce' );
		$fields = [ 'country' => 'Country', 'region' => 'Province / State', 'city' => 'City', 'discipline' => 'Discipline', 'employment_type' => 'Employment type', 'closing_date' => 'Closing date (YYYY-MM-DD)', 'apply_email' => 'Apply-to email' ];
		foreach ( $fields as $k => $label ) {
			$v = get_post_meta( $post->ID, '_pt_job_' . $k, true );
			echo '<p><label><strong>' . esc_html( $label ) . '</strong><br><input type="text" class="widefat" name="pt_job[' . esc_attr( $k ) . ']" value="' . esc_attr( $v ) . '"></label></p>';
		}
	}, 'pt_job', 'side' );
} );

add_action( 'save_post_pt_job', function ( $post_id ) {
	if ( ! isset( $_POST['pt_job_nonce'] ) || ! wp_verify_nonce( $_POST['pt_job_nonce'], 'pt_job_details' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( (array) ( $_POST['pt_job'] ?? [] ) as $k => $v ) {
		update_post_meta( $post_id, '_pt_job_' . sanitize_key( $k ), sanitize_text_field( wp_unslash( $v ) ) );
	}
} );

/* First run: wire the menu, front page and posts page, and import the dev-site jobs once. */
function pt_first_run() {
	$menus = wp_get_nav_menus();
	if ( $menus ) {
		$locations = get_theme_mod( 'nav_menu_locations', [] );
		if ( empty( $locations['primary'] ) ) {
			$pick = $menus[0];
			foreach ( $menus as $m ) {
				if ( $m->slug === 'main-menu' ) {
					$pick = $m;
				}
			}
			$locations['primary'] = $pick->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	$home = get_page_by_path( 'home' );
	$news = get_page_by_path( 'news' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}
	if ( $news ) {
		update_option( 'page_for_posts', $news->ID );
	}
	if ( ! get_posts( [ 'post_type' => 'pt_job', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] ) ) {
		$jobs = json_decode( file_get_contents( get_template_directory() . '/inc/content/jobs.json' ), true ) ?: [];
		foreach ( $jobs as $j ) {
			$id = wp_insert_post( [
				'post_type'    => 'pt_job',
				'post_status'  => $j['status'] === 'publish' ? 'publish' : 'draft',
				'post_title'   => $j['title'],
				'post_name'    => $j['slug'],
				'post_content' => $j['content'],
				'post_date'    => $j['date'],
			] );
			if ( $id && ! is_wp_error( $id ) ) {
				foreach ( [ 'country', 'region', 'city', 'discipline', 'employment' => 'employment_type', 'closing' => 'closing_date', 'email' => 'apply_email' ] as $from => $to ) {
					if ( is_int( $from ) ) {
						$from = $to;
					}
					update_post_meta( $id, '_pt_job_' . $to, $j[ $from ] ?? '' );
				}
			}
		}
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'pt_first_run' );
add_action( 'import_end', 'pt_first_run' );
/* The content import usually happens after activation, so re-check the menu location and front page in admin until both are set. */
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$locations = get_theme_mod( 'nav_menu_locations', [] );
	if ( empty( $locations['primary'] ) || ! get_option( 'page_on_front' ) || ! get_option( 'page_for_posts' ) ) {
		pt_first_run();
	}
}, 5 );

/* Contact Form 7: the two site forms, created on first run when the plugin is active and they do not exist yet. */
function pt_form_definitions() {
	return [
		'Main Contact Form' => [
			'form'    => '<div class="pt-form__fields">
<label>First Name* [text* your-firstname autocomplete:given-name]</label>
<label>Last Name* [text* your-lastname autocomplete:family-name]</label>
<label>Company* [text* your-company autocomplete:organization]</label>
<label>Phone [tel your-phone autocomplete:tel]</label>
<label class="wide">Email* [email* your-email autocomplete:email]</label>
<label class="wide">Project / Program [text your-project]</label>
<label class="wide">Message* [textarea* your-message rows:6]</label>
</div>
<p class="pt-form__submit">[submit "Submit Request"]</p>',
			'subject' => 'Website enquiry from [your-firstname] [your-lastname]',
			'body'    => "Name: [your-firstname] [your-lastname]\nCompany: [your-company]\nEmail: [your-email]\nPhone: [your-phone]\nProject: [your-project]\n\nMessage:\n[your-message]\n\n--\nSent from the ProTorque website contact form ([_site_url]).",
		],
		'Newsletter Signup' => [
			'form'    => '<div class="pt-form__fields">
<label>Email [email* your-email autocomplete:email placeholder "email@example.com"]</label>
<p class="pt-form__submit">[submit "Subscribe"]</p>
</div>',
			'subject' => 'Newsletter signup - ProTorque website',
			'body'    => "[your-email]\n\nwould like to subscribe to ProTorque news.\n\n--\nSent from the ProTorque website ([_site_url]).",
		],
	];
}

function pt_create_forms() {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return;
	}
	foreach ( pt_form_definitions() as $title => $f ) {
		$existing = get_posts( [ 'post_type' => 'wpcf7_contact_form', 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] );
		if ( $existing ) {
			continue;
		}
		$cf = WPCF7_ContactForm::get_template( [ 'title' => $title ] );
		$cf->set_properties( [
			'form' => $f['form'],
			'mail' => [ 'active' => true, 'subject' => $f['subject'], 'sender' => '[_site_title] <wordpress@ptenergy.com>', 'recipient' => '[_site_admin_email]', 'body' => $f['body'], 'additional_headers' => 'Reply-To: [your-email]', 'attachments' => '', 'use_html' => false, 'exclude_blank' => true ],
		] );
		$cf->save();
	}
	// Contact Form 7's sample form is not used.
	foreach ( get_posts( [ 'post_type' => 'wpcf7_contact_form', 'title' => 'Contact form 1', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] ) as $sample ) {
		wp_trash_post( $sample );
	}
}
add_action( 'after_switch_theme', 'pt_create_forms', 20 );
add_action( 'admin_init', function () {
	if ( get_option( 'pt_forms_created' ) !== PT_VERSION && class_exists( 'WPCF7_ContactForm' ) ) {
		pt_create_forms();
		update_option( 'pt_forms_created', PT_VERSION );
	}
} );

/** Render a Contact Form 7 form by title, with a static stand-in when the plugin is not active. */
function pt_form( $title ) {
	if ( class_exists( 'WPCF7_ContactForm' ) ) {
		$existing = get_posts( [ 'post_type' => 'wpcf7_contact_form', 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] );
		if ( $existing ) {
			echo do_shortcode( '[contact-form-7 id="' . (int) $existing[0] . '" title="' . esc_attr( $title ) . '"]' );
			return;
		}
	}
	$def = pt_form_definitions()[ $title ] ?? null;
	if ( ! $def ) {
		return;
	}
	// Static stand-in: same fields, no handler, so the layout can be reviewed before Contact Form 7 is installed.
	$html = preg_replace_callback( '/\[(text|email|tel|textarea)\*? ([\w-]+)([^\]]*)\]/', function ( $m ) {
		$ph = preg_match( '/placeholder "([^"]*)"/', $m[3], $p ) ? ' placeholder="' . esc_attr( $p[1] ) . '"' : '';
		return $m[1] === 'textarea' ? '<textarea name="' . $m[2] . '" rows="6"' . $ph . '></textarea>' : '<input type="' . ( $m[1] === 'text' ? 'text' : $m[1] ) . '" name="' . $m[2] . '"' . $ph . '>';
	}, $def['form'] );
	$html = preg_replace( '/\[submit "([^"]*)"\]/', '<input type="submit" value="$1">', $html );
	echo '<form class="pt-form__static" action="#" onsubmit="return false">' . $html . '</form><p class="pt-form__note">Form submissions go live once Contact Form 7 is installed on the production site.</p>';
}

/* Contact Form 7 markup: no auto-paragraphs, so the theme's field grid works. */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/* News featured images: bundled with the theme and attached to posts by slug once the posts exist (after the content import). */
function pt_seed_news_media() {
	$map  = json_decode( file_get_contents( get_template_directory() . '/inc/content/news-media.json' ), true ) ?: [];
	$done = get_option( 'pt_news_media_done', [] );
	$left = 0;
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	foreach ( $map as $slug => $file ) {
		if ( in_array( $slug, $done, true ) ) {
			continue;
		}
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( ! $post ) {
			$left++;
			continue;
		}
		if ( has_post_thumbnail( $post ) && wp_get_attachment_image_url( get_post_thumbnail_id( $post ) ) ) {
			$done[] = $slug;
			continue;
		}
		$src = get_template_directory() . '/assets/media/news/' . $file;
		if ( ! file_exists( $src ) ) {
			$done[] = $slug;
			continue;
		}
		$tmp = wp_tempnam( $file );
		copy( $src, $tmp );
		$id = media_handle_sideload( [ 'name' => $file, 'tmp_name' => $tmp ], $post->ID, get_the_title( $post ) );
		if ( ! is_wp_error( $id ) ) {
			set_post_thumbnail( $post->ID, $id );
			$done[] = $slug;
		} else {
			@unlink( $tmp );
		}
	}
	update_option( 'pt_news_media_done', $done, false );
	return $left;
}
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'edit_posts' ) || get_option( 'pt_news_media_complete' ) ) {
		return;
	}
	if ( pt_seed_news_media() === 0 ) {
		update_option( 'pt_news_media_complete', 1 );
	}
} );
add_action( 'import_end', 'pt_seed_news_media' );
