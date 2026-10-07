<?php
/**
 * Primary navigation with two mega menu modes, carried over from the dev site's PT Megamenu plugin
 * and restyled to the new system:
 *   - split:     top-level item with class pt-split-megamenu (Services). Left column tabs = its children,
 *                right pane = the active child's children.
 *   - equipment: top-level item with class pt-equipment-megamenu (Equipment). Two columns of child links,
 *                right pane previews the hovered item (blurb + image from the page's _pt_mm_* meta).
 * Plain items render as links. Below 900px the same tree renders as an accordion.
 */

function pt_nav_tree( string $location ): array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return [];
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return [];
	}
	$by_id = [];
	foreach ( $items as $it ) {
		$by_id[ $it->ID ] = [
			'id'       => $it->ID,
			'title'    => $it->title,
			'url'      => $it->url,
			'parent'   => (int) $it->menu_item_parent,
			'classes'  => array_filter( (array) $it->classes ),
			'object'   => (int) $it->object_id,
			'children' => [],
		];
	}
	$tree = [];
	foreach ( $by_id as $id => &$node ) {
		if ( $node['parent'] && isset( $by_id[ $node['parent'] ] ) ) {
			$by_id[ $node['parent'] ]['children'][] = &$node;
		} else {
			$tree[] = &$node;
		}
	}
	unset( $node );
	return $tree;
}

function pt_nav_mode( array $node ): string {
	if ( in_array( 'pt-equipment-megamenu', $node['classes'], true ) ) {
		return 'equipment';
	}
	if ( in_array( 'pt-split-megamenu', $node['classes'], true ) ) {
		return 'split';
	}
	return $node['children'] ? 'list' : 'link';
}

function pt_nav_render_desktop( array $tree ): void {
	echo '<ul class="pt-nav__list">';
	foreach ( $tree as $node ) {
		$mode = pt_nav_mode( $node );
		$has  = 'link' !== $mode;
		printf( '<li class="pt-nav__item pt-nav__item--%s"%s>', esc_attr( $mode ), $has ? ' data-mega' : '' );
		printf(
			'<a class="pt-nav__link" href="%s"%s>%s%s</a>',
			esc_url( $node['url'] ),
			$has ? ' aria-haspopup="true" aria-expanded="false"' : '',
			esc_html( $node['title'] ),
			$has ? '<span class="pt-nav__chev" aria-hidden="true"></span>' : ''
		);
		if ( 'split' === $mode ) {
			pt_nav_render_split( $node );
		} elseif ( 'equipment' === $mode ) {
			pt_nav_render_equipment( $node );
		} elseif ( 'list' === $mode ) {
			echo '<div class="pt-mega pt-mega--list"><div class="pt-mega__inner"><ul class="pt-mega__simple">';
			foreach ( $node['children'] as $c ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( $c['url'] ), esc_html( $c['title'] ) );
			}
			echo '</ul></div></div>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

function pt_nav_render_split( array $node ): void {
	$tabs = $node['children'];
	echo '<div class="pt-mega pt-mega--split" role="region" aria-label="' . esc_attr( $node['title'] ) . '"><div class="pt-mega__inner">';
	echo '<div class="pt-mega__tabs"><p class="pt-mega__eyebrow">' . esc_html( $node['title'] ) . '</p><ul role="tablist">';
	foreach ( $tabs as $i => $t ) {
		printf(
			'<li><button class="pt-mega__tab%s" type="button" role="tab" aria-selected="%s" aria-controls="pt-pane-%d" id="pt-tab-%d">%s<span class="pt-nav__chev" aria-hidden="true"></span></button></li>',
			0 === $i ? ' is-active' : '',
			0 === $i ? 'true' : 'false',
			$t['id'],
			$t['id'],
			esc_html( $t['title'] )
		);
	}
	echo '</ul></div><div class="pt-mega__panes">';
	foreach ( $tabs as $i => $t ) {
		printf( '<div class="pt-mega__pane%s" id="pt-pane-%d" role="tabpanel" aria-labelledby="pt-tab-%d"%s>', 0 === $i ? ' is-active' : '', $t['id'], $t['id'], 0 === $i ? '' : ' hidden' );
		printf( '<p class="pt-mega__eyebrow"><a href="%s">%s</a></p><ul class="pt-mega__links">', esc_url( $t['url'] ), esc_html( $t['title'] ) );
		foreach ( $t['children'] as $c ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $c['url'] ), esc_html( $c['title'] ) );
		}
		printf( '<li class="pt-mega__all"><a href="%s">Explore %s</a></li>', esc_url( $t['url'] ), esc_html( $t['title'] ) );
		echo '</ul></div>';
	}
	echo '</div></div></div>';
}

/** Equipment preview image: the theme-bundled product image by page slug, else the page's _pt_mm_image_id meta. */
function pt_nav_item_image( $page_id ) {
	if ( ! $page_id ) {
		return '';
	}
	$slug = get_post_field( 'post_name', $page_id );
	if ( $slug && file_exists( get_template_directory() . "/assets/img/menu/$slug.png" ) ) {
		return get_template_directory_uri() . "/assets/img/menu/$slug.png";
	}
	$img = (int) get_post_meta( $page_id, '_pt_mm_image_id', true );
	return $img ? ( wp_get_attachment_image_url( $img, 'medium' ) ?: '' ) : '';
}

function pt_nav_render_equipment( array $node ): void {
	$items = $node['children'];
	$half  = (int) ceil( count( $items ) / 2 );
	$cols  = array_chunk( $items, max( 1, $half ) );
	$first = $items[0] ?? null;
	echo '<div class="pt-mega pt-mega--equipment" role="region" aria-label="' . esc_attr( $node['title'] ) . '"><div class="pt-mega__inner">';
	echo '<div class="pt-mega__items"><p class="pt-mega__eyebrow"><a href="' . esc_url( $node['url'] ) . '">' . esc_html( $node['title'] ) . '</a></p><div class="pt-mega__cols">';
	foreach ( $cols as $col ) {
		echo '<ul class="pt-mega__links">';
		foreach ( $col as $c ) {
			$blurb = $c['object'] ? get_post_meta( $c['object'], '_pt_mm_blurb', true ) : '';
			$src   = pt_nav_item_image( $c['object'] );
			printf(
				'<li><a href="%s" data-blurb="%s" data-image="%s" data-title="%s">%s<span class="pt-nav__chev" aria-hidden="true"></span></a></li>',
				esc_url( $c['url'] ),
				esc_attr( $blurb ),
				esc_url( $src ),
				esc_attr( $c['title'] ),
				esc_html( $c['title'] )
			);
		}
		echo '</ul>';
	}
	echo '</div></div>';
	$fb = $first && $first['object'] ? get_post_meta( $first['object'], '_pt_mm_blurb', true ) : '';
	$fs = $first ? pt_nav_item_image( $first['object'] ) : '';
	echo '<div class="pt-mega__preview" aria-live="polite">';
	echo '<div class="pt-mega__previewtext"><h3 class="pt-mega__previewtitle">' . esc_html( $first['title'] ?? '' ) . '</h3><p class="pt-mega__previewblurb">' . esc_html( $fb ) . '</p><a class="pt-btn pt-mega__cta" href="' . esc_url( $first['url'] ?? '#' ) . '">Explore ' . esc_html( $first['title'] ?? '' ) . '</a></div>';
	echo '<div class="pt-mega__previewimg"><img src="' . esc_url( $fs ) . '" alt="" width="300" height="300"></div>';
	echo '</div></div></div>';
}

function pt_nav_render_mobile( array $tree ): void {
	echo '<ul class="pt-mnav">';
	foreach ( $tree as $node ) {
		$has = ! empty( $node['children'] );
		echo '<li>';
		if ( $has ) {
			printf( '<div class="pt-mnav__row"><a href="%s">%s</a><button type="button" class="pt-mnav__toggle" aria-expanded="false" aria-label="Open %s"><span class="pt-nav__chev"></span></button></div><ul class="pt-mnav__sub" hidden>', esc_url( $node['url'] ), esc_html( $node['title'] ), esc_attr( $node['title'] ) );
			foreach ( $node['children'] as $c ) {
				$sub = ! empty( $c['children'] );
				echo '<li>';
				if ( $sub ) {
					printf( '<div class="pt-mnav__row"><a href="%s">%s</a><button type="button" class="pt-mnav__toggle" aria-expanded="false" aria-label="Open %s"><span class="pt-nav__chev"></span></button></div><ul class="pt-mnav__sub" hidden>', esc_url( $c['url'] ), esc_html( $c['title'] ), esc_attr( $c['title'] ) );
					foreach ( $c['children'] as $g ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $g['url'] ), esc_html( $g['title'] ) );
					}
					echo '</ul>';
				} else {
					printf( '<a href="%s">%s</a>', esc_url( $c['url'] ), esc_html( $c['title'] ) );
				}
				echo '</li>';
			}
			echo '</ul>';
		} else {
			printf( '<a href="%s">%s</a>', esc_url( $node['url'] ), esc_html( $node['title'] ) );
		}
		echo '</li>';
	}
	echo '</ul>';
}
