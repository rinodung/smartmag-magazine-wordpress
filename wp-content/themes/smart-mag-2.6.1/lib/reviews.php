<?php

/**
 * Deals with reviews
 * 
 * @uses Bunyad_Posts
 */
class Bunyad_Reviews
{
	
	public $rating_max;
	
	public function __construct()
	{
		$this->rating_max = (!empty(Bunyad::options()->rating_max) ? Bunyad::options()->rating_max : 10);

		// our ajax actions
		add_action('wp_ajax_nopriv_bunyad_rate', array($this, 'add_rating'));
		add_action('wp_ajax_bunyad_rate', array($this, 'add_rating'));

		add_action('wp_enqueue_scripts', array($this, 'register_assets'));
		
	}
	
	public function register_assets()
	{
		wp_localize_script('bunyad-theme', 'Bunyad', array('ajaxurl' => admin_url('admin-ajax.php')));
	}
	
	public function add_rating()
	{		
		// can the rating be added - perform all checks
		if (!$this->can_rate(intval($_POST['id']))) {
			echo -1;
			exit;
		}
		
		if ($_POST['rating'] && $_POST['id']) {
		
			$votes = Bunyad::posts()->meta('user_rating', intval($_POST['id']));
			
			// defaults if no votes yet
			if (!is_array($votes)) {
				$votes = array('votes' => array(), 'overall' => null, 'count' => 0);
			}
			
			$votes['count']++;
		
			// add to votes record
			$votes['votes'][time()] = array($this->percent_to_decimal($_POST['rating']), $this->get_user_ip());
			
			// recount overall
			$total = 0;
			foreach ($votes['votes'] as $ip => $data) {
				$total += $data[0]; // rating
			}
			
			$votes['overall'] = $total / $votes['count'];
			
			// save meta data
			update_post_meta(intval($_POST['id']), '_bunyad_user_rating', $votes); 
			
			// set the cookie
			$ids = array();
			if (!empty($_COOKIE['bunyad_user_ratings'])) {
				$ids = (array) explode('|', $_COOKIE['bunyad_user_ratings']);
			}
			
			array_push($ids, $_POST['id']);
			setcookie('bunyad_user_ratings', implode('|', $ids), time() + 86400 * 30);
			
			echo json_encode(array('decimal' => round($votes['overall'], 1), 'percent' => $this->decimal_to_percent($votes['overall'])));
		}

		exit;
	}
	
	/**
	 * Converts percent rating to points rating
	 * 
	 * @param integer $percent
	 */
	public function percent_to_decimal($percent)
	{
		return ($percent / 100) * $this->rating_max;
	}
	
	/**
	 * Converts point rating to percent 
	 * 
	 * @param integer $decimal
	 */
	public function decimal_to_percent($decimal)
	{
		return round($decimal / $this->rating_max * 100);	
	}
	
	/**
	 * Whether a user can rate 
	 * 
	 * @param integer|null $post_id
	 * @param integer|null $user_id
	 */
	public function can_rate($post_id = null, $user_id = null)
	{
		if (!$post_id) {
			$post_id = get_the_ID();
		}
		
		// rating not even enabled
		if (!Bunyad::posts()->meta('reviews', $post_id) OR !Bunyad::options()->user_rating) {
			return false;
		}

		// ip check
		$votes = Bunyad::posts()->meta('user_rating', $post_id);
		$user_ip = $this->get_user_ip();
		
		if (!empty($votes['votes'])) {
			
			foreach ((array) $votes['votes'] as $time => $data) {
				if (!empty($data[1]) && $data[1] == $user_ip) {
					return false;
				}
			}
		}
		
		// cookie check
		if (!empty($_COOKIE['bunyad_user_ratings'])) {
			$ids = (array) explode('|', $_COOKIE['bunyad_user_ratings']);
			
			if (in_array($post_id, $ids)) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Get number of votes on a post
	 */
	public function votes_count($post_id = null)
	{
		$rating = Bunyad::posts()->meta('user_rating', $post_id);
		
		if (!empty($rating['count'])) {
			return $rating['count'];
		}
		
		return 0;
	}
	
	/**
	 * Get overall user rating for a post
	 * 
	 * @param integer|null $post_id
	 * @param string $type  empty for overall number or 'percent' for rounded percent
	 */
	public function get_user_rating($post_id = null, $type = '')
	{
		$rating = Bunyad::posts()->meta('user_rating', $post_id);
		
		if (!empty($rating['overall'])) {
			
			// return percent?
			if ($type == 'percent') {
				return $this->decimal_to_percent($rating['overall']);
			}
			
			return round($rating['overall'], 1);
		}
		
		return 0;
	}
	
	/**
	 * Get user ip
	 */
	public function get_user_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			// check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];	
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}