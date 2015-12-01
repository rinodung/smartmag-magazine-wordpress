<?php

class Bunyad_SocialCount_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-social-count-widget',
			'Bunyad - Social Buttons',
			array('description' => __('Twitter/facebook counter and RSS subscribe buttons.', 'bunyad-widgets'), 'classname' => 'bunyad-social-count')
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		extract($instance, EXTR_SKIP);


		echo $before_widget;
		 
		if ($rss) {
				
		?>
		
			<div class="counter">
				<a href="<?php bloginfo('rss2_url'); ?>" class="rss" title="<?php esc_attr_e('Subscribe via RSS', 'bunyad-widgets'); ?>">
					<?php _e('rss', 'bunyad-widgets'); ?></a>
				<span><?php _e('feed', 'bunyad-widgets'); ?></span>
			</div>
		
		<?php 
		} // end feedburner
		
		if ($facebook_page) {
			$fb_count = $this->get_data('facebook_likes', $facebook_page);
			
		?>
		
			<div class="counter">
				<a href="http://facebook.com/<?php echo urlencode($facebook_page); ?>" class="facebook" title="<?php esc_attr_e('Connect at Facebook'); ?>">
					<?php echo $this->format_thousand($fb_count); ?></a>
				<span><?php _e('fans', 'bunyad-widgets'); ?></span>
			</div>
		
		<?php 
		} // end facebook
		
		if ($twitter_user) {
			$followers_count = $this->get_data('twitter_count', $twitter_user, $instance);
			
		?>
		
			<div class="counter">
				<a href="http://twitter.com/<?php echo urlencode($twitter_user); ?>" class="twitter" title="<?php esc_attr_e('Follow on twitter'); ?>">
					<?php echo $this->format_thousand($followers_count); ?></a>
				<span><?php _e('followers', 'bunyad-widgets'); ?></span>
			</div>
		
		<?php 
			
		} // end twitter
		
		echo $after_widget;
		
	}
	
	public function format_thousand($number)
	{
		$number = intval($number);
		return ($number > 10000 ? round($number / 1000, 1) .'k' : $number);
	}
	
	// @todo: move to Bunyad_Options
	public function get_data($type = '')
	{
		$data_store  = 'bunyad-transient-' . $type;
		$back_store  = 'bunyad-transient-backup-' . $type;
		$cache       = get_transient($data_store);
		$cache_hours = 10;
		 
		// no cache found?
		if ($cache === false) {
			$data = call_user_func_array(array($this, 'get_' . $type), array_slice(func_get_args(), 1));
			
			if (is_numeric($data)) {
				// save a transient to expire in $cache_hours and a permanent backup option
				set_transient($data_store, $data, 60 * 60 * $cache_hours);
				update_option($back_store, $data);
			}
			// fall to latest backup store - no fresh data available
			else { 
				$data = get_option($back_store);
			}
			
			return $data;
		}
		else {
			return $cache;
		}
	}
	
	/**
	 * Get number of likes at facebook 
	 * 
	 * @param string $page
	 */
	public function get_facebook_likes($page)
	{
		$json = wp_remote_get('http://graph.facebook.com/' . urlencode($page));
		
		if (is_wp_error($json)) {
			return false;
		}
		
		$data = json_decode($json['body']);
		return $data->likes;
	}
	
	/**
	 * Get number of followers from twitter

	 * @param string $user
	 */
	public function get_twitter_count($user, $data)
	{
		
		require_once dirname(__FILE__) .  '/../vendor/twitteroauth/twitteroauth.php';
		$twitterConnection = new TwitterOAuth(
			$data['consumer_key'], // consumer key
			$data['consumer_secret'], // consumer secret
			$data['access_token'], // access token
			$data['access_secret'] // access token secret 
		);
		
		
		$data = $twitterConnection->get('users/show', array('screen_name' => $user));

		return (isset($data->followers_count) ? $data->followers_count : false);
	}
		
	public function update($new, $old)
	{
		extract($new);
		
		$new['feedburner'] = preg_replace('#http://feeds\.feedburner\.com/(.+?)(/|$)#', '\\1', trim($new['feedburner']));
		
		foreach ($new as $key => $val) {
			$new[$key] = wp_filter_kses($val);
		}
		
		// remove data
		delete_transient('bunyad-transient-twitter_count');
		delete_transient('bunyad-transient-facebook_likes');
		
		return $new;
	}
	
	public function form($instance)
	{
		$instance = array_merge(
			array('twitter_user' => '', 'facebook_page' => '', 'rss' => 1, 'consumer_key' => '', 'consumer_secret' => '', 'access_token' => '', 'access_secret' => ''),
			$instance
		);
		
		extract($instance);
		
	?>
	
	<script>
		jQuery(function($) {
			$('.twitter-user').keyup(function() {
				if ($(this).val()) {
					$('.twitter-info').show();
				}
				else {
					$('.twitter-info').hide();
				}
			});
		});	
	</script>
	
	<style type="text/css">
		.twitter-info {
			border-left: 3px solid #d8d8d8;
			padding: 10px 10px 0px 10px;
			margin-bottom: 1em;
		}
	</style>

	<p>
		<label for="<?php echo esc_attr($this->get_field_id('twitter_user')); ?>"><?php _e('Twitter User:', 'bunyad-widgets'); ?></label>
		<input class="widefat twitter-user" id="<?php echo esc_attr($this->get_field_id('twitter_user')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('twitter_user')); ?>" type="text" value="<?php echo esc_attr($twitter_user); ?>" />
	</p>
	

	<fieldset class="twitter-info">	

		<p><a href="http://dev.twitter.com/apps" target="_blank"><?php _e('Create your Twitter App', 'bunyad-widgets'); ?></a></p>
	
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('consumer_key')); ?>"><?php _e('Consumer Key', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('consumer_key')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('consumer_key')); ?>" type="text" value="<?php echo esc_attr($consumer_key); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('consumer_secret')); ?>"><?php _e('Consumer Secret', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('consumer_secret')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('consumer_secret')); ?>" type="text" value="<?php echo esc_attr($consumer_secret); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('access_token')); ?>"><?php _e('Access Token', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('access_token')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('access_token')); ?>" type="text" value="<?php echo esc_attr($access_token); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('access_secret')); ?>"><?php _e('Access Token Secret', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('access_secret')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('access_secret')); ?>" type="text" value="<?php echo esc_attr($access_secret); ?>" />
		</p>
	</fieldset>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('facebook_page')); ?>"><?php _e('Facebook Page:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('facebook_page')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('facebook_page')); ?>" type="text" value="<?php echo esc_attr($facebook_page); ?>" />
	</p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('rss')); ?>"><?php _e('Show RSS', 'bunyad-widgets'); ?></label>
		<input type="hidden" name="<?php echo $this->get_field_name('rss'); ?>" value="0" />
		<input id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" type="checkbox" 
			<?php checked(isset($rss) ? $rss : 0); ?> value="1" />
	</p>
	
	<?php
	}
}