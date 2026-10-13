<?php
/**
 * Fallback template (blog/search/404): simple text list in the theme's style.
 */
get_header();
?>
<main class="ro-page">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<h1 class="ro-page__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<div class="ro-page__content"><?php the_excerpt(); ?></div>
		<?php endwhile; ?>
	<?php else : ?>
		<h1 class="ro-page__title">Stranica nije pronađena</h1>
		<div class="ro-page__content"><p><a class="ro-btn ro-btn--red" href="<?php echo esc_url( home_url( '/' ) ); ?>">Nazad na početnu</a></p></div>
	<?php endif; ?>
</main>
<?php
get_footer();
