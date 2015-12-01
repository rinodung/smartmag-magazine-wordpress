<?php
/**
 * Partial: Default Navigation
 */

	// Setup data variables to enable or disable sticky nav functionality
	$attribs = array('class' => array(
		'navigation cf', 
		Bunyad::options()->nav_align, 
		Bunyad::options()->nav_layout, 
		(Bunyad::options()->nav_style ? Bunyad::options()->nav_style : 'nav-dark'), 
		(Bunyad::options()->nav_search ? 'has-search' : '')
	));
	
	// Wrapper attributes required for sticky nav mainly - to contain nav and search
	$wrap_attribs = array('class' => array(
		'navigation-wrap cf',
	));
	
	if (Bunyad::options()->sticky_nav) {
		
		$wrap_attribs['data-sticky-nav'] = 1;
		$wrap_attribs['data-sticky-type'] = Bunyad::options()->sticky_nav;
					
		// sticky navigation logo?
		if (Bunyad::options()->sticky_nav_logo) {
			$wrap_attribs['data-sticky-logo'] = 1;
		}
	}
	
?>

<div <?php Bunyad::markup()->attribs('navigation-wrap', $wrap_attribs); ?>>

	<nav <?php Bunyad::markup()->attribs('navigation', $attribs); ?>>
	
		<div <?php Bunyad::markup()->attribs('nav-inner', array('class' => (Bunyad::options()->nav_layout == 'nav-full' ? 'wrap' : ''))); ?>>
		
			<div class="mobile" data-type="<?php echo Bunyad::options()->mobile_menu_type; ?>" data-search="<?php echo Bunyad::options()->mobile_nav_search; ?>">
				<a href="#" class="selected">
					<span class="text"><?php _e('Navigate', 'bunyad'); ?></span><span class="current"></span> <i class="hamburger fa fa-bars"></i>
				</a>
			</div>
			
			<?php wp_nav_menu(array('theme_location' => 'main', 'fallback_cb' => '', 'walker' =>  'Bunyad_Menu_Walker')); ?>
			
			<?php if (has_nav_menu('main-mobile')): // Have a custom mobile menu? ?>
			
				<?php 
					wp_nav_menu(array(
						'theme_location' => 'main-mobile', 
						'fallback_cb' => '', 
						'walker' =>  'Bunyad_Menu_Walker', 
						'menu_class' => 'menu mobile-menu', 
						'container_class' => 'mobile-menu-container'
					)); 
				?>
			
			<?php endif; ?>
	
		</div>
		
	</nav>

	<?php if (Bunyad::options()->nav_search): ?>
	
	<div <?php 
			Bunyad::markup()->attribs('nav-search', array('class' => array(
					'nav-search', 
					(Bunyad::options()->nav_style ? Bunyad::options()->nav_style . '-search': ''),
					(Bunyad::options()->nav_layout == 'nav-full' ? 'wrap' : '')  // add wrap only if full-width navigation
			))); ?>>
				
		<div class="search-overlay">
			<a href="#" title="<?php esc_attr_e('Search', 'bunyad'); ?>" class="search-icon"><i class="fa fa-search"></i></a>
			<?php include locate_template('partials/header/search.php'); ?>
		</div>
	</div>
	
	<?php endif; ?>
	
</div>