<?php

/**
 * WooCommerce Main Template Catch-All 
 */

get_header();

?>

<div class="main wrap cf">

	<div class="row">
		<div class="col-8 main-content">
			
			<?php woocommerce_content(); ?>
			
		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>
