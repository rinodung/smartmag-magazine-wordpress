<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(null, 'single-forum'); ?>>

	<li class="bbp-forum-info">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>

		<?php do_action( 'bbp_theme_before_forum_title' ); ?>

		<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>

		<?php do_action( 'bbp_theme_before_forum_description' ); ?>

		<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>

		<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</li>

	<li class="bbp-forum-topic-count">
		<div><span class="number"><?php bbp_forum_topic_count(); ?></span> <?php _ex('topics', 'bbpress', 'bunyad'); ?></div>
		<div><?php echo (bbp_show_lead_topic() 
				? '<span class="number">' . bbp_get_forum_reply_count() . '</span> ' . _x('replies', 'bbpress', 'bunyad') 
				: '<span class="number">' . bbp_get_forum_post_count() . '</span> ' . _x('posts', 'bbpress', 'bunyad')); ?></li>

	<li class="bbp-forum-freshness">

		<p class="bbp-topic-meta">

			<?php do_action( 'bbp_theme_before_topic_author' ); ?>			

			<span class="bbp-topic-freshness-author">
			
				<?php if (($has_replies = (strstr(bbp_get_forum_freshness_link(), '<a')))): ?>
					
					<?php _ex('by ', 'bbPress Freshness Author', 'bunyad'); ?>
					<?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'size' => 45 ) ); ?></span>
					
				<?php endif; ?>

			<?php do_action( 'bbp_theme_after_topic_author' ); ?>
			
		
			<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>

			<?php bbp_forum_freshness_link(); ?>

			<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
		
		</p>
	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
