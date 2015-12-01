<?php

/**
 * Replies Loop
 */

?>

<?php do_action( 'bbp_template_before_replies_loop' ); ?>

	<div class="single-post-header section-head">
	
		<div class="forum-tags">
			<p class="posted-in"><?php 
				_ex('Posted In: ', 'bbPress', 'bunyad'); 
			?> 
			
			<a href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a></p>
	
		
		</div>

		<div class="user-links">
		
				<?php echo bbp_get_user_favorites_link() . bbp_get_topic_subscription_link(); ?>
				
		</div>
		
	</div>



<ul id="topic-<?php bbp_topic_id(); ?>-replies" class="forums bbp-replies">	

	<li class="bbp-body">

		<?php if (bbp_thread_replies()) : ?>

			<ul class="thread-replies"><?php bbp_list_replies(); ?></ul>

		<?php else : ?>

			<?php while ( bbp_replies() ) : bbp_the_reply(); ?>

				<?php bbp_get_template_part( 'loop', 'single-reply' ); ?>

			<?php endwhile; ?>

		<?php endif; ?>

	</li><!-- .bbp-body -->

</ul><!-- #topic-<?php bbp_topic_id(); ?>-replies -->

<?php do_action( 'bbp_template_after_replies_loop' ); ?>
