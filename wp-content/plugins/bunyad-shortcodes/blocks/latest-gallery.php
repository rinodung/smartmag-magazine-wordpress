<?php

wp_enqueue_script('flex-slider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array('jquery'));

$tax_query = array();
$number    = $number ? $number : 10;

// specific post format? 
if ($format != '' && $format !== 'all') {
	$tax_query[] = array(
		'taxonomy' => 'post_format',
		'field' => 'slug',
		'terms' => array('post-format-' . $format),
	);
}

// specific category?
if (!empty($cat) && $cat !== 'all') {
	if (count($tax_query)) {
		$tax_query['relation'] = 'AND';
	}
	
	$tax_query[] = array(
		'taxonomy' => 'category',
		'field' => 'id',
		'terms' => (array) $cat
	);
}

// specific tag?
if (!empty($tax_tag)) {
	if (count($tax_query)) {
		$tax_query['relation'] = 'AND';
	}
	
	$tax_query[] = array(
		'taxonomy' => 'post_tag',
		'field' => 'slug',
		'terms' => explode(',', $tax_tag)
	);
}

// taxonomy query
query_posts(apply_filters(
		'bunyad_block_query_args', 
		array('tax_query' => $tax_query, 'posts_per_page' => $number, 'offset' => ($offset ? $offset : '')), 
		'latest_gallery',
		$atts
));

?>

<section class="gallery-block">

<?php if ($title): ?>
	<h3 class="gallery-title prominent"><?php echo esc_html($title); ?></h3>
<?php endif; ?>

<?php if (have_posts()): ?>
<div class="flexslider <?php echo ($type == 'slider' ? 'slider' : 'carousel'); ?>">
	<ul class="slides">
	<?php while (have_posts()): the_post(); ?>
	
		<li>
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>" class="image-link">
				<span class="image"><?php the_post_thumbnail(($type == 'slider' ?  'main-block' : 'gallery-block'), array('alt' => '', 'title' => '')); ?>
				</span>
				<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
			</a>
			<p class="title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>"><?php the_title(); ?></a></p>
		</li>
	
	<?php endwhile; ?>
	</ul>
	<div class="title-bar"></div>
</div>
<?php endif; ?>

<?php wp_reset_query(); ?>

</section>