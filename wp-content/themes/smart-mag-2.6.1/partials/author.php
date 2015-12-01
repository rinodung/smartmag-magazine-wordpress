<?php 
/**
 * Partial template for Author Box
 */

?>
		<section class="author-info">
		
			<?php echo get_avatar(get_the_author_meta('user_email'), 100); ?>
			
			<div class="description">
				<?php the_author_posts_link(); ?>
				
				<ul class="social-icons">
				<?php 
				
					// author fields
					$fields = array(
						'url' => array('icon' => 'home', 'label' => __('Website', 'bunyad')),
						'facebook' => array('icon' => 'facebook', 'label' => __('Facebook', 'bunyad')),
						'twitter' => array('icon' => 'twitter', 'label' => __('Twitter', 'bunyad')), 
						'gplus' => array('icon' => 'google-plus', 'label' => __('Google+', 'bunyad')), 
						'linkedin' => array('icon' => 'linkedin', 'label' => __('LinkedIn', 'bunyad')),							
					);
					
					foreach ($fields as $meta => $data): 
					
						if (!get_the_author_meta($meta)) {
							continue;
						}
						
						$type = $data['icon'];
				?>
					
					<li>
						<a href="<?php echo esc_url(get_the_author_meta($meta)); ?>" class="icon fa fa-<?php echo esc_attr($type); ?>" title="<?php echo esc_attr($data['label']); ?>"> 
							<span class="visuallyhidden"><?php echo esc_html($data['label']); ?></span></a>				
					</li>
					
					
				<?php endforeach; ?>
				</ul>
				
				<p class="bio"><?php the_author_meta('description'); ?></p>
			</div>
			
		</section>