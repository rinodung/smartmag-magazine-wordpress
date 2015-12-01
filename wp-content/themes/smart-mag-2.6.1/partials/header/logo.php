<?php
/**
 * Partial: Logo 
 */

$class = '';
if (Bunyad::options()->image_logo) {
	$class = Bunyad::options()->image_logo_mobile ? 'is-logo-mobile' : '';
}

// attributes for the logo link
$attribs = array(
	'href'  => home_url('/'),
	'title' => get_bloginfo('name', 'display'),
	'rel'   => 'home',
	'class' => $class
);

?>
		<a <?php Bunyad::markup()->attribs('main-logo', $attribs); ?>">
		
			<?php if (Bunyad::options()->image_logo): // custom logo ?>
											
				<?php if (Bunyad::options()->image_logo_mobile): // add mobile logo if set ?>
					<img src="<?php echo esc_attr(Bunyad::options()->image_logo_mobile); ?>" class="logo-mobile" width="0" height="0" />
				<?php endif; ?>
				
				<img src="<?php echo esc_attr(Bunyad::options()->image_logo); ?>" class="logo-image" alt="<?php 
					 echo esc_attr(get_bloginfo('name', 'display')); ?>" <?php 
					 echo (Bunyad::options()->image_logo_retina ? 'data-at2x="'. esc_attr(Bunyad::options()->image_logo_retina) .'"' : ''); 
				?> />
					 
			<?php else: ?>
				<?php echo do_shortcode(Bunyad::options()->text_logo); ?>
			<?php endif; ?>
			
		</a>