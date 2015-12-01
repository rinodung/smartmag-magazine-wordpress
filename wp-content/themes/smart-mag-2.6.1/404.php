<?php get_header(); ?>

<div class="main wrap cf">
	<div class="row">
		<div class="col-8 main-content page">
		
			<header class="post-heading">
				<h1 class="main-heading"><?php _e('404 Error', 'bunyad'); ?></h1>
			</header>
		
			<div class="post-content error-page row">
				
				<div class="col-3 text-404">404</div>
				
				<div class="col-9">
					<h1><?php _e('Page Not Found!', 'bunyad'); ?></h1>
					<p>
					<?php _e("We're sorry, but we can't find the page you were looking for. It's probably some thing we've done wrong but now we know about it and we'll try to fix it. In the meantime, try one of these options:", 'bunyad'); ?>
					</p>
					<ul class="links fa-ul">
						<li><i class="fa fa-angle-double-right"></i> <a href="javascript: history.go(-1);"><?php _e('Go to Previous Page', 'bunyad'); ?></a></li>
						<li><i class="fa fa-angle-double-right"></i> <a href="<?php echo site_url(); ?>"><?php _e('Go to Homepage', 'bunyad'); ?></a></li>
					</ul>
				</div>
				
			</div>

		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>