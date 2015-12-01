<?php

class Bunyad_Twitter_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad_twitter-widget',
			'Bunyad - Twitter',
			array('description' => 'Latest tweets widget.', 'classname' => 'latest-tweets')
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		extract($instance, EXTR_SKIP);
		
		$title = apply_filters('widget_title', $instance['title']);
		
		// get tweet data
		$data = $this->get_data($instance);
		
		// do custom loop if available
		if (has_action('bunyad_widget_twitter_loop')):

			$args['title'] = $title;
			do_action('bunyad_widget_twitter_loop', $args, $data);
		
		else:
	?>
		
	
		<?php echo $before_widget; ?>
		<?php echo $before_title . $title . $after_title; ?>
		
		<div class="twitter-widget">
		<ul id="twitter_update_list">
			<?php 
				foreach ($data as $tweet): 
					// convert links and @ references		
					$tweet->text = preg_replace('/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;\'">\:\s\<\>\)\]\!])/', '<a href="\\1">\\1</a>', $tweet->text);
					$tweet->text = preg_replace('/\B@([_a-z0-9]+)/i', '<a href="http://twitter' . '.com/\\1">@\\1</a>', $tweet->text);
			?>
			
				<li><span><?php echo $tweet->text; ?></span></li>
			
			<?php endforeach; ?>
			</ul>
			
			<p><a href="http://twitter.com/<?php echo esc_attr($username); ?>" class="follow" title="<?php 
				echo esc_attr_e('Follow on twitter', 'bunyad-widgets'); ?>"><?php _e('Follow', 'bunyad-widgets'); ?></a></p>
			
		</div>
		
		<?php echo $after_widget; ?>
	
	<?php
	
		endif; // check action override
	
	} // end widget function
	

	/**
	 * Wrapper to get data off cache or via twitter API - cache updated every 10 mins
	 */
	public function get_data($instance)
	{
		$type = 'tweets';
		
		$data_store  = 'bunyad-transient-' . $type;
		$back_store  = 'bunyad-transient-backup-' . $type;
		$cache       = get_transient($data_store);
		$cache_mins = 10;
		 
		// no cache found?
		if ($cache === false) {
			$data = $this->get_twitter_data($instance);
			
			if ($data) {
				// save a transient to expire in $cache_mins and a permanent backup option
				set_transient($data_store, $data, 60 * $cache_mins);
				update_option($back_store, $data);
			}
			// fall to permanent backup store - no fresh data available
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
	 * Fetch timeline from twitter api
	 * 
	 * @param array $data
	 */
	public function get_twitter_data($data)
	{
		extract($data);

		/*
		 * Twitter API
		 */
		require_once dirname(__FILE__) .  '/../vendor/twitteroauth/twitteroauth.php';
		$twitterConnection = new TwitterOAuth(
			$data['consumer_key'], // consumer key
			$data['consumer_secret'], // consumer secret
			$data['access_token'], // access token
			$data['access_secret'] // access token secret 
		);
		
		
		$data = $twitterConnection->get('statuses/user_timeline', array('screen_name' => $username, 'count' => $show_num, 'exclude_replies' => false));
		
		if ($twitterConnection->http_code === 200) {
			return $data;
		}
		
		return false;
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = wp_filter_kses($val);
		}
		
		delete_transient('bunyad-transient-tweets');
		
		$new['show_num'] = intval($new['show_num']);
		
		return $new;
	}
	
	public function form($instance)
	{
		$instance = array_merge(array(
			'title' => 'Twitter Widget', 'username' => '', 'consumer_key' => '', 'consumer_secret' => '', 'access_token' => '', 'access_secret' => '', 'show_num' => 3
		), (array) $instance);
		
		extract($instance);
		
	?>
	
	<p><a href="http://dev.twitter.com/apps" target="_blank"><?php _e('Create your Twitter App', 'bunyad-widgets'); ?></a></p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php _e('Username:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
	</p>

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
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('show_num')); ?>"><?php _e('Number of Tweets:', 'bunyad-widgets'); ?></label>
		<input class="width100" id="<?php echo esc_attr($this->get_field_id('show_num')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('show_num')); ?>" type="text" value="<?php echo esc_attr($show_num); ?>" />
	</p>
	
	<?php
	
	} // end form()
}