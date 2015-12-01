
	<?php do_action('bunyad_post_main_content'); ?>
	
	<footer class="main-footer">
	
	<?php if (!Bunyad::options()->disable_footer): ?>
		<div class="wrap">
		
		<?php if (is_active_sidebar('main-footer')): ?>
			<ul class="widgets row cf">
				<?php dynamic_sidebar('main-footer'); ?>
			</ul>
		<?php endif; ?>
		
		</div>
	
	<?php endif; ?>
	
	
	<?php if (!Bunyad::options()->disable_lower_footer): ?>
		<div class="lower-foot">
			<div class="wrap">
		
			<?php if (is_active_sidebar('lower-footer')): ?>
			
			<div class="widgets">
				<?php dynamic_sidebar('lower-footer'); ?>
			</div>
			
			<?php endif; ?>
		
			</div>
		</div>		
	<?php endif; ?>
	
	</footer>
	
</div> <!-- .main-wrap -->

<?php wp_footer(); ?>

</body>
</html>