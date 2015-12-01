<?php
/**
 * Partial: Default Header
 */

// Get the partial template for top bar
get_template_part('partials/header/top-bar');

?>
	<div id="main-head" class="main-head">
		
		<div class="wrap">
			
			<?php if (Bunyad::options()->mobile_header == 'modern'): // modern mobile header? ?>
				<div class="mobile-head">
				
					<div class="menu-icon"><a href="#"><i class="fa fa-bars"></i></a></div>
					<div class="title">
						<?php get_template_part('partials/header/logo'); ?>
					</div>
					<div class="search-overlay">
						<a href="#" title="<?php esc_attr_e('Search', 'bunyad'); ?>" class="search-icon"><i class="fa fa-search"></i></a>
					</div>
					
				</div>
			<?php endif; ?>

			<header <?php Bunyad::markup()->attribs('header', array('class' => Bunyad::options()->header_style)); ?>>
			
				<div class="title">
					<?php get_template_part('partials/header/logo'); ?>
				</div>
				
				<?php if (Bunyad::options()->header_style !== 'centered'): ?>
					
					<div class="right">
					<?php dynamic_sidebar('header-right');	?>
					</div>
					
				<?php endif; ?>
				
			</header>
				
			<?php if (!Bunyad::options()->nav_layout): // normal width navigation? ?>
				
				<?php get_template_part('partials/header/nav'); ?>
				
			<?php endif; ?>
				
		</div>
		
		<?php 
			// Full width navigation goes out of wrap container
			if (Bunyad::options()->nav_layout == 'nav-full'): 
				get_template_part('partials/header/nav');
			endif;
		?>
		
	</div>