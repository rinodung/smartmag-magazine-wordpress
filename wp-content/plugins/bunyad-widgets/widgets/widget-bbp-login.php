<?php

/**
 * bbPress Login Widget for Bunyad Framework
 */
class Bunyad_BbpLogin_Widget extends WP_Widget {

	/**
	 * bbPress Login Widget
	 *
	 * Registers the login widget
	 *
	 * @uses apply_filters() Calls 'bbp_login_widget_options' with the
	 *                        widget options
	 */
	public function __construct() {
		$widget_ops = apply_filters( 'bunyad_bbp_login_widget_options', array(
			'classname'   => 'bbp_widget_login',
			'description' => __( 'A simple login form with optional links to sign-up and lost password pages.', 'bbpress' )
		) );

		parent::__construct(false, __('(bbPress) Bunyad Login Widget', 'bunyad-widgets'), $widget_ops);
	}

	/**
	 * Register the widget
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		
		if (class_exists('bbpress')) {
			register_widget('Bunyad_BbpLogin_Widget');
		}
	}

	/**
	 * Displays the output, the login form
	 * 
	 * @param mixed $args Arguments
	 * @param array $instance Instance
	 * @uses apply_filters() Calls 'bbp_login_widget_title' with the title
	 * @uses get_template_part() To get the login/logged in form
	 */
	public function widget( $args = array(), $instance = array() ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance );

		// Typical WordPress filter
		$settings['title'] = apply_filters( 'widget_title', $settings['title'], $instance, $this->id_base );

		// bbPress filters
		$settings['title']    = apply_filters( 'bbp_login_widget_title',    $settings['title'],    $instance, $this->id_base );
		$settings['register'] = apply_filters( 'bbp_login_widget_register', $settings['register'], $instance, $this->id_base );
		$settings['lostpass'] = apply_filters( 'bbp_login_widget_lostpass', $settings['lostpass'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( !empty( $settings['title'] ) ) {
			echo $args['before_title'] . $settings['title'] . $args['after_title'];
		}

		if (!is_user_logged_in()) : ?>

			<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form widget-login">
				<fieldset>
				
					<legend><?php _e( 'Log In', 'bbpress' ); ?></legend>

					<div class="bbp-username input-group">
						<i class="fa fa-user"></i>
						<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" tabindex="<?php bbp_tab_index(); ?>" 
							placeholder="<?php echo esc_attr_x('Your Username', 'bbPress', 'bunyad-widgets'); ?>" />
					</div>

					<div class="bbp-password input-group">
						<i class="fa fa-lock"></i>
						<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" tabindex="<?php bbp_tab_index(); ?>" 
							placeholder="<?php echo esc_attr_x('Your Password', 'bbPress', 'bunyad-widgets'); ?>"/>
					</div>

					<div class="bbp-submit-wrapper">

						<?php if (!empty($settings['lostpass'])) : ?>

								<a href="<?php echo esc_url($settings['lostpass']); ?>" title="<?php esc_attr_e('Lost password?', 'bbpress'); ?>" class="bbp-lostpass-link lost-pass-modal"><?php 
									 _e('Lost password?', 'bbpress'); ?></a>

							<?php endif; ?>

						<?php do_action('login_form'); ?>

						<button type="submit" name="user-submit" id="user-submit" tabindex="<?php bbp_tab_index(); ?>" class="button submit user-submit"><?php _e( 'Log In', 'bbpress' ); ?></button>

						<?php bbp_user_login_fields(); ?>

					</div>

				</fieldset>
			</form>
			
			<?php  if (!empty($settings['register'])): ?>
				
			<div class="bbp-register-info"><?php _ex("Don't have an account?", 'bbPress', 'bunyad-widgets'); ?>
				<a href="<?php echo esc_url( $settings['register'] ); ?>" class="register-modal"><?php _ex('Register Now!', 'bbPress', 'bunyad-widgets'); ?></a>
			</div>
							
			<?php endif; ?>
			

		<?php else : ?>

			<div class="bbp-logged-in">
				<a href="<?php bbp_user_profile_url(bbp_get_current_user_id()); ?>" class="submit user-submit"><?php echo get_avatar(bbp_get_current_user_id(), '60'); ?></a>
				<div class="content">
				
				<?php _ex('Welcome back, ', 'bbPress', 'bunyad-widgets'); ?>
				<?php bbp_user_profile_link(bbp_get_current_user_id()); ?>
				
				<ol class="links">
					<li><a href="<?php bbp_user_profile_edit_url(bbp_get_current_user_id()); ?>">
						<?php _ex('Edit Profile', 'bbPress', 'bunyad-widgets'); ?></a></li>
					<li><a href="<?php bbp_subscriptions_permalink(bbp_get_current_user_id()); ?>">
						<?php _ex('Subscriptions', 'bbPress', 'bunyad-widgets'); ?></a></li>
					<li><a href="<?php bbp_favorites_permalink(bbp_get_current_user_id()); ?>">
						<?php _ex('Favorites', 'bbPress', 'bunyad-widgets'); ?></a></li>
				</ol>

				<?php bbp_logout_link(); ?>
				
				</div>
			</div>

		<?php endif;

		echo $args['after_widget'];
	}

	/**
	 * Update the login widget options
	 *
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['register'] = esc_url_raw( $new_instance['register'] );
		$instance['lostpass'] = esc_url_raw( $new_instance['lostpass'] );

		return $instance;
	}

	/**
	 * Output the login widget options form
	 *
	 *
	 * @param $instance Instance
	 * @uses BBP_Login_Widget::get_field_id() To output the field id
	 * @uses BBP_Login_Widget::get_field_name() To output the field name
	 */
	public function form($instance = array()) {

		// Get widget settings
		$settings = $this->parse_settings( $instance ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e( 'Register URI:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>" type="text" value="<?php echo esc_url( $settings['register'] ); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e( 'Lost Password URI:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'lostpass' ); ?>" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" type="text" value="<?php echo esc_url( $settings['lostpass'] ); ?>" /></label>
		</p>

		<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @param $instance Instance
	 * @uses bbp_parse_args() To merge widget settings into defaults
	 */
	public function parse_settings( $instance = array() ) {
		return bbp_parse_args( $instance, array(
			'title'    => '',
			'register' => '',
			'lostpass' => ''
		), 'login_widget_settings' );
	}
}