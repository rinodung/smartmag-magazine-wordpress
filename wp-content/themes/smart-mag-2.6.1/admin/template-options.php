<?php

$js_events = array();

?>

<div class="wrap" id="bunyad-options">

	<form method="post" action="" id="bunyad-options-form" enctype="multipart/form-data">
		<?php echo wp_nonce_field($option_key . '_save'); ?>
	
	<header class="options-main-head">
		<h2><?php _e('SmartMag Settings', 'bunyad-admin'); ?></h2>
	</header>
	
	<div class="options-header cf">
		<p class="submit alignright">
			<?php submit_button(__( 'Save Settings', 'bunyad-admin'), 'primary', 'update', false); ?>
		</p>
		
		<p class="submit alignleft">
			<?php submit_button(__('Reset All Settings', 'bunyad-admin'), 'delete', 'delete', false, array(
					'data-confirm' => __('Do you really wish to reset your options to default?', 'bunyad-admin')
				)); ?>
		</p>
	</div>
	
	<div class="options-main">
		<ul class="tabs">
		
		<?php foreach ($options as $tab): ?>
		
			<li><a href="#" id="<?php echo esc_attr($tab['id']); ?>"><?php 
				if (!empty($tab['icon'])) {
					echo '<div class="dashicons ' . esc_attr($tab['icon']) . '"></div> ';	
				}
				
				echo esc_html($tab['title']); ?></a></li>
	
		<?php endforeach; ?>
		
		</ul>
			
	
		<div class="form-sections">
		
				
	<?php if (isset($options_saved) && $options_saved === true): ?>
		<div class="success updated settings-error"><p><?php _e('Options saved!', 'bunyad-admin'); ?></p></div>
	<?php elseif (!empty($options_deleted)): ?>
		<div class="success updated settings-error"><p><?php _e('Options reset to defaults.', 'bunyad-admin'); ?></p></div>
	<?php elseif (!empty($form_errors)): ?>
		<div class="error settings-error">
			<p><strong><?php _e('Errors:', 'bunyad-admin'); ?></strong></p>
			<p><?php echo implode('<br />', $form_errors); ?></p>
		</div>
	<?php endif;?>
		
		<?php foreach ($options as $option_tab): ?>
			<div id="options-<?php echo $option_tab['id'];?>" class="options-sections">
				
			<?php foreach ($option_tab['sections'] as $section): ?>
			
				<fieldset>
					<?php if (!empty($section['title'])): ?>
						<legend><?php echo esc_html($section['title']); ?></legend>
					<?php endif; ?>
					
					<?php if (!empty($section['desc'])): ?>
						<p class="section-desc"><?php echo $section['desc']; ?></p>
					<?php endif; ?>
					
					
					<?php // finally render the element ?>
					<?php foreach ($section['fields'] as $element): 
					
							if (empty($element['type'])) {
								continue;
							}
					?>
						
						<div class="element cf <?php echo (!empty($element['name']) ? 'ele-' . $element['name'] : ''); ?>">
							<?php echo $this->render($element); ?>
							<div class="element-desc"><?php echo $element['desc']; ?></div>
						</div>
						
						<?php 
						
						if (!empty($element['events'])) {
							$js_events[$element['name']] = (array) $element['events'];
						}
						
						?>
						
					<?php endforeach; ?>
					
				
				</fieldset>
				
			<?php endforeach; ?>
			
			</div>
		<?php endforeach; ?>
		
		</div>
	
	</div>
			
	<footer class="options-footer">	
		<?php submit_button(__( 'Save Settings', 'bunyad-admin'), 'primary', 'update'); ?>
	</footer>
	
	</form>
</div>

<?php if (count($js_events)): ?>

<script>
Bunyad_Options.events = <?php echo json_encode($js_events); ?>;
</script>

<?php endif; ?>