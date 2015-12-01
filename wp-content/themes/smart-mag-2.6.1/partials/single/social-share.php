<?php
/**
 * Partial template for social share buttons on single page
 */
?>

<?php if ((is_single() OR Bunyad::options()->social_icons_classic) && Bunyad::options()->social_share): ?>
	
	<div class="post-share">
		<span class="text"><?php _e('Share.', 'bunyad'); ?></span>
		
		<span class="share-links">

			<a href="http://twitter.com/home?status=<?php echo urlencode(get_permalink()); ?>" class="fa fa-twitter" title="<?php _e('Tweet It', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Twitter', 'bunyad'); ?></span></a>
				
			<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="fa fa-facebook" title="<?php _e('Share on Facebook', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Facebook', 'bunyad'); ?></span></a>
				
			<a href="http://plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="fa fa-google-plus" title="<?php _e('Share on Google+', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Google+', 'bunyad'); ?></span></a>
				
			<a href="http://pinterest.com/pin/create/button/?url=<?php 
				echo urlencode(get_permalink()); ?>&amp;media=<?php echo urlencode(wp_get_attachment_url(get_post_thumbnail_id($post->ID))); ?>" class="fa fa-pinterest"
				title="<?php _e('Share on Pinterest', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Pinterest', 'bunyad'); ?></span></a>
				
			<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(get_permalink()); ?>" class="fa fa-linkedin" title="<?php _e('Share on LinkedIn', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('LinkedIn', 'bunyad'); ?></span></a>
				
			<a href="http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink()) ?>&amp;name=<?php echo urlencode(get_the_title()) ?>" class="fa fa-tumblr"
				title="<?php _e('Share on Tumblr', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Tumblr', 'bunyad'); ?></span></a>
				
			<a href="mailto:?subject=<?php echo rawurlencode(get_the_title()); ?>&amp;body=<?php echo rawurlencode(get_permalink()); ?>" class="fa fa-envelope-o"
				title="<?php _e('Share via Email', 'bunyad'); ?>">
				<span class="visuallyhidden"><?php _e('Email', 'bunyad'); ?></span></a>
			
		</span>
	</div>
	
<?php endif; ?>