<!-- Modal -->
<div class="modal hide fade login-modal" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content main-screen">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="login-modal-label"><?php _e('Sign In', 'bunyad'); ?></h4>
			</div>
			<div class="modal-body">
			
			<?php if (!is_user_logged_in()): ?>
			
				<form method="post" action="<?php echo site_url('wp-login.php', 'login_post'); ?>" class="bbp-login-form widget-login">
					<fieldset>
					
						<legend><?php _e('Log In', 'bunyad'); ?></legend>
	
						<div class="bbp-username input-group">
							<i class="fa fa-user"></i>
							<input type="text" name="log" value="" size="20" id="user_login" tabindex="1" 
								placeholder="<?php echo esc_attr_x('Your Username', 'bbPress', 'bunyad'); ?>" />
						</div>
	
						<div class="bbp-password input-group">
							<i class="fa fa-lock"></i>
							<input type="password" name="pwd" value="" size="20" id="user_pass" tabindex="2" 
								placeholder="<?php echo esc_attr_x('Your Password', 'bbPress', 'bunyad'); ?>"/>
						</div>
	
						<div class="bbp-submit-wrapper">
	
	
							<a href="<?php echo wp_lostpassword_url(); ?>" title="<?php esc_attr_e('Lost password?', 'bunyad'); ?>" class="bbp-lostpass-link lost-pass-modal"><?php 
								 _e('Lost password?', 'bunyad'); ?></a>
	
	
							<?php do_action('login_form'); ?>
	
							<button type="submit" name="wp-submit" id="user-submit" tabindex="3" class="button submit user-submit"><?php _e('Log In', 'bunyad'); ?></button>
	
							<?php bbp_user_login_fields(); ?>
	
						</div>
	
					</fieldset>
				</form>
				
								
				<div class="bbp-register-info"><?php _ex("Don't have an account?", 'bbPress', 'bunyad'); ?>
					<a href="#" class="register-modal"><?php _ex('Register Now!', 'bbPress', 'bunyad'); ?></a>
				</div>
			
			<?php elseif (class_exists('bbpress')): ?>
			

				<div class="bbp-logged-in">
					<a href="<?php bbp_user_profile_url(bbp_get_current_user_id()); ?>" class="submit user-submit"><?php echo get_avatar(bbp_get_current_user_id(), '60'); ?></a>
					<div class="content">
					
					<?php _ex('Welcome back, ', 'bbPress', 'bunyad'); ?>
					<?php bbp_user_profile_link(bbp_get_current_user_id()); ?>
					
					<ol class="links">
						<li><a href="<?php bbp_user_profile_edit_url(bbp_get_current_user_id()); ?>">
							<?php _ex('Edit Profile', 'bbPress', 'bunyad'); ?></a></li>
						<li><a href="<?php bbp_subscriptions_permalink(bbp_get_current_user_id()); ?>">
							<?php _ex('Subscriptions', 'bbPress', 'bunyad'); ?></a></li>
						<li><a href="<?php bbp_favorites_permalink(bbp_get_current_user_id()); ?>">
							<?php _ex('Favorites', 'bbPress', 'bunyad'); ?></a></li>
					</ol>
	
					<?php bbp_logout_link(); ?>
					
					</div>
				</div>
			
			
			<?php else: ?>
			
				<div class="bbp-logged-in">
				
				<?php 
					$current = wp_get_current_user();
			        echo get_avatar($current->ID, 60);
				
				?>
				
					<div class="content">
					
					<?php _ex('Welcome back, ', 'bbPress', 'bunyad'); ?> <?php echo esc_html($current->display_name); ?>. 
					<?php wp_loginout(get_permalink()); ?>
					
					</div>
				
				</div>
			
			<?php endif; ?>
		
			</div>
		</div>
		
		<div class="modal-content lost-pass">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="login-modal-label"><?php _e('Recover Password', 'bunyad'); ?></h4>
			</div>
			<div class="modal-body">
				
				<form method="post" action="<?php echo esc_url(site_url('wp-login.php?action=lostpassword', 'login_post')); ?>" class="bbp-login-form widget-login">
					<fieldset>
					
						<legend><?php _e('Recover Password', 'bunyad'); ?></legend>

						<div class="bbp-password input-group">
							<i class="fa fa-user"></i>
							<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1" 
								placeholder="<?php echo esc_attr_x('Username or Email', 'bbPress', 'bunyad'); ?>"/>
						</div>
	
						<div class="bbp-submit-wrapper">
	
							<?php do_action( 'lostpassword_form' ); ?>
	
							<button type="submit" name="wp-submit" id="user-submit" tabindex="2" class="button submit user-submit"><?php 
								_e('Recover', 'bunyad'); ?></button>	
						</div>
	
					</fieldset>
				</form>
				
			</div>
		</div>
		
		<div class="modal-content register-now">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="login-modal-label"><?php _e('Register', 'bunyad'); ?></h4>
			</div>
			<div class="modal-body">
			
				<form method="post" action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); ?>" class="bbp-login-form widget-login">
					<fieldset>
					
						<legend><?php _e('Log In', 'bunyad'); ?></legend>
	
						<div class="bbp-username input-group">
							<i class="fa fa-user"></i>
							<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1" 
								placeholder="<?php echo esc_attr_x('Your Username', 'bbPress', 'bunyad'); ?>" />
						</div>
	
						<div class="bbp-password input-group">
							<i class="fa fa-envelope"></i>
							<input type="text" name="user_email" value="" size="20" id="user_email" tabindex="2" 
								placeholder="<?php echo esc_attr_x('Your Email', 'bbPress', 'bunyad'); ?>"/>
						</div>
	
						<div class="bbp-submit-wrapper">
	
	
							<span class="password-msg"><?php _e('A password will be e-mailed to you.', 'bunyad'); ?></span>
	
	
							<?php do_action('register_form'); ?>
	
							<button type="submit" name="wp-submit" id="wp-submit" tabindex="3" class="button submit user-submit"><?php 
								_e('Register', 'bunyad'); ?></button>	
						</div>
	
					</fieldset>
				</form>
			
			</div>
		</div>
		
		
	</div>
</div>