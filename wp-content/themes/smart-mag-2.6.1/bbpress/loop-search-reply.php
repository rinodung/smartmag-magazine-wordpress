<?php

/**
 * Search Loop - Single Reply
 */

?>

<div class="single-post-header section-head">	
	<div class="forum-tags">
		<span class="posted-in">
			<?php _ex('In reply to: ', 'bbPress', 'bunyad'); ?>
			<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
		</span>

	</div><!-- .bbp-reply-title -->
</div>
	
<div <?php bbp_reply_class(0, 'single-post'); ?>>

	<div class="bbp-reply-author">

		<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

		<?php bbp_reply_author_link( array( 'sep' => '<br />', 'show_role' => true, 'type' => 'avatar') ); ?>

		<?php if ( bbp_is_user_keymaster() ) : ?>

			<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>

			<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>

			<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>

		<?php endif; ?>

		<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

	</div><!-- .bbp-reply-author -->

	<div class="bbp-reply-content">

		<?php do_action( 'bbp_theme_before_reply_content' ); ?>

		<div class="reply-meta">
			<?php bbp_reply_author_link(array('type' => 'name')); ?> <?php _ex('on', 'bbPress Reply', 'bunyad'); ?> <span class="post-date"><?php bbp_reply_post_date(); ?></span>
			
			
		<?php if ( bbp_is_single_user_replies() ) : ?>

			<span>&middot; 
				<?php _ex('in reply to: ', 'bbPress', 'bunyad'); ?>
				<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
			</span>

		<?php endif; ?>
			
			<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>
		</div>

		<div class="post-content">
			<?php bbp_reply_content(); ?>
		</div>

		<?php do_action( 'bbp_theme_after_reply_content' ); ?>

		<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

		<?php bbp_reply_admin_links(); ?>

		<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
		

	</div><!-- .bbp-reply-content -->

</div><!-- .reply -->

