
	<?php
	
		// sidebar HTML attributes
		$attribs = array('class' => 'col-4 sidebar');
		if (Bunyad::options()->sticky_sidebar) {
			$attribs['data-sticky'] = 1;
		}
	
		do_action('bunyad_sidebar_start'); 	
	?>		
		
		
		<aside <?php Bunyad::markup()->attribs('sidebar', $attribs); ?>>
			<ul>
			
			<?php if (!dynamic_sidebar('primary-sidebar')) : ?>
				<?php _e("<li>Nothing yet.</li>", 'bunyad'); ?>
			<?php endif; ?>
	
			</ul>
		</aside>
		
	<?php do_action('bunyad_sidebar_end'); ?>