<?php /* News grid card. */ ?>
<li class="pt-post">
	<a class="pt-post__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy' ] ); else : ?><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/content/card-drilling-services.jpg' ); ?>" alt="" loading="lazy"><?php endif; ?>
	</a>
	<p class="pt-post__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
	<h3 class="pt-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<p class="pt-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
</li>
