<?php

/**
 * Author Page
 */

get_header();

$authordata    = get_userdata(get_query_var('author'));
$loop_template = Bunyad::options()->author_loop_template;

// set correct grid options for grid templates
if (strstr($loop_template, '-3')) {
	Bunyad::registry()->set('loop_grid', 3);
	$loop_template = str_replace('-3', '', $loop_template);
}

?>


<div class="main wrap cf">

	<div class="row">
		<div class="col-8 main-content">
		
			<h1 class="main-heading author-title"><?php echo sprintf(__('Author %s', 'bunyad'), '<strong>' . get_the_author() . '</strong>'); ?></h1>

			<?php get_template_part('partials/author'); ?>
	
			<?php get_template_part($loop_template); ?>

		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>