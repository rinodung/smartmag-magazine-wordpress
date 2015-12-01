<?php

/**
 * Block for review shortcode
 * 
 * Collect criteria and create an average rating to display.
 * 
 * Note: supports microformat schema. Add hreview, reviewer, dtreviewed
 * relevant fields in content.php
 */

$meta = Bunyad::posts()->meta();
$ratings = array();
foreach ($meta as $key => $value)
{
	if (preg_match('/criteria_rating_([0-9]+)$/', $key, $match)) {
		
		$ratings[] = array(
			'number' => $match[1],
			'rating' => $value,
			'label'  => $meta['_bunyad_criteria_label_' . $match[1]]
		);
	}
}

$overall = Bunyad::posts()->meta('review_overall');
$heading = Bunyad::posts()->meta('review_heading');
$type    = Bunyad::posts()->meta('review_type');

// add verdict text 
$verdict = Bunyad::posts()->meta('review_verdict');
$verdict_text = Bunyad::posts()->meta('review_verdict_text');

// container classes
$classes[] = 'review-box';
if ($position == 'top-left') {
	array_push($classes, 'alignleft', 'column', 'half');
}

// add stars class
if ($type == 'stars') {
	array_push($classes, 'stars');
}

?>

<section class="<?php echo implode(' ', $classes); ?>">
	<?php if ($heading): ?>
	
	<h3 class="heading"><?php echo esc_html($heading); ?></h3>
	
	<?php endif; ?>
	
	
	<div class="verdict-box">
		<div class="overall">
			<span class="number rating"><span class="value"><?php

				$overall_percent = round($overall / 10 * 100);
				
				echo ($type != 'points' ?  $overall_percent . '<span class="percent">%</span>' : $overall); ?></span>
				
				<?php if ($type == 'stars'): ?>
				<div class="overall-stars">
					<div class="main-stars">
						<span style="width: <?php echo $overall_percent; ?>%;"><strong class="number"><?php echo esc_html($overall_percent); ?>%</strong></span>
					</div>
				</div>
				
				<?php endif; ?>
				
				<span class="best"><span class="value-title" title="<?php echo ($type != 'points' ? 100 : Bunyad::reviews()->rating_max); ?>"></span></span>
			</span>
			<span class="verdict"><?php echo $verdict; ?></span>
		</div>
		
		<div class="text summary"><?php echo do_shortcode(wpautop($verdict_text)); ?></div>		
	</div>
	
	
	<ul>
	<?php foreach ((array) $ratings as $rating): 
	
			$percent = round(($rating['rating'] / 10) * 100);
	
			if ($type == 'percent') {
				$rating['rating'] = $percent . ' %';
			}
	?>
	
		<li>
		
		<?php if ($type == 'stars'): ?>
		
			<div class="criterion">
				<span class="label"><?php echo esc_html($rating['label']); ?></span>

				<div class="main-stars">
					<span style="width: <?php echo $percent; ?>%;"><strong class="rating"><?php echo esc_html($rating['rating']); ?></strong></span>
				</div>
			
			</div>
				
		<?php else: ?>
		
			<div class="criterion">
				<span class="label"><?php echo esc_html($rating['label']); ?></span>	
				<span class="rating"><?php echo esc_html($rating['rating']); ?></span>
			</div>
			
			<div class="rating-bar"><div class="bar" style="width: <?php echo $percent; ?>%;"></div></div>
				
		<?php endif; ?>

		</li>
	
	<?php endforeach; ?>
	
	<?php if (Bunyad::options()->user_rating): ?>
	
		<li class="user-ratings<?php echo (!Bunyad::reviews()->can_rate() ? ' voted' : ''); ?>" data-post-id="<?php echo get_the_ID(); ?>">
			<span class="label"><?php _e('User Ratings', 'bunyad'); ?> <span class="votes">(<?php 
				printf(__('%s Votes', 'bunyad'), '<span class="number">' . Bunyad::reviews()->votes_count() . '</span>'); ?>)</span>
			</span>
			
			<?php if ($type == 'stars'): ?>
				
				<div class="main-stars">
					<span style="width: <?php echo Bunyad::reviews()->get_user_rating(null, 'percent'); ?>%;"><strong class="rating"><?php echo esc_html(Bunyad::reviews()->get_user_rating()); ?></strong></span>
				</div>
				
				<span class="hover-number"></span>
				
			<?php else: ?>
			
				<span class="rating"><?php echo esc_html(Bunyad::reviews()->get_user_rating(null, $type)) . ($type == 'percent' ? ' %' : ''); ?></span>
				
				<span class="hover-number"></span>
			
				<div class="rating-bar"><span class="bar" style="width: <?php echo Bunyad::reviews()->get_user_rating(null, 'percent'); ?>%;"></span></div>			
			
			<?php endif; ?>
		</li>
	
	<?php endif;?>
	
	</ul>
</section>