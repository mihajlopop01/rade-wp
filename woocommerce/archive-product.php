<?php
/**
 * Shop / category archive. The design has no shop screen; each product renders
 * as the home page's feature block.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="ro-shop">
	<h1 class="ro-shop__title">Prodavnica</h1>
	<?php
	$i = 0;
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$p = wc_get_product( get_the_ID() );
			if ( $p ) {
				get_template_part( 'template-parts/product-feature', null, array( 'product' => $p, 'index' => $i++, 'id' => '' ) );
			}
		}
	} else {
		echo '<p class="ro-shop__empty">Uskoro u prodaji.</p>';
	}
	?>
</main>
<?php
get_footer();
