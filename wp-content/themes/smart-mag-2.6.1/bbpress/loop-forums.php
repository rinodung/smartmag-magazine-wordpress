<?php

/**
 * Forums Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_forums_loop' ); ?>

<ul id="forums-list-<?php bbp_forum_id(); ?>" class="bbp-forums">

	<li class="bbp-body">

		<?php while ( bbp_forums() ) : bbp_the_forum(); ?>
		
			<?php if (bbp_is_forum_category() && !bbp_get_forum_parent_id()): ?>
			
			<div class="section-head gallery-title forum-cat">
		
				<ul class="forum-titles">
					<li class="bbp-forum-info"><?php bbp_forum_title(); ?></li>
					<li class="normal bbp-forum-topic-count"><?php _ex('Topics', 'bbPress', 'bunyad'); ?> / <?php 
							bbp_show_lead_topic() ? _ex('Replies', 'bbPress', 'bunyad') : _ex('Posts', 'bbPress', 'bunyad'); ?></li>
					<li class="normal bbp-forum-freshness"><?php _ex('Freshness', 'bbPress', 'bunyad'); ?></li>
				</ul>
		
			</div>

				<?php 
				
					// get sub-forums
					$orig_query = clone bbpress()->forum_query;
					bbp_has_forums(array('post_parent' => bbp_get_forum_id()));
					
					while (bbp_forums()): 
						bbp_the_forum();				
				?>
					
					<?php bbp_get_template_part('loop', 'single-forum'); ?>
										
				<?php 
					endwhile;

					// restore query
					bbpress()->forum_query = $orig_query;
						
				?>
				
			<?php else: ?>

				<?php bbp_get_template_part( 'loop', 'single-forum' ); ?>
			
			<?php endif; ?>

		<?php endwhile; ?>

	</li><!-- .bbp-body -->

</ul><!-- .forums-directory -->

<?php do_action( 'bbp_template_after_forums_loop' ); ?>
