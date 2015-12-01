<?php

class Bunyad_TabbedRecent_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-tabbed-recent-widget',
			'Bunyad - Recent Tabs',
			array('description' => __('Tabs: Recent, category1, category2...', 'bunyad-widgets'), 'classname' => 'tabbed')
		);
		
		add_action('save_post', array($this, 'flush_widget_cache'));
		add_action('edit_post', array($this, 'flush_widget_cache')); // comments covered
		add_action('deleted_post', array($this, 'flush_widget_cache'));
		add_action('switch_theme', array($this, 'flush_widget_cache'));
		add_action('bunyad_widget_flush_cache', array($this, 'flush_widget_cache'));
		
		// enqueue 
		add_action('admin_enqueue_scripts', array($this, 'add_assets'));
		
	}
	
	public function add_assets($hook) 
	{
		// only in admin cp for form
		if ($hook == 'widgets.php') {
			wp_enqueue_script('widget-tabs', plugins_url('/bunyad-widgets/js/widget-tabs.js'));
		}
	}

	// @todo wrap existing widgets with in-memory cache
	public function widget($args, $instance) 
	{
		global $post; // setup_postdata not enough
		
		// set defaults
		$titles = $cats = $tax_tags = array();
		
		extract($args);
		extract($instance);
				
		// missing data?
		if (!count($titles) OR !count($cats)) {
			_e('Recent tabs widget still need to be configured! Add tabs, add a title, and select type for each tab in widgets area.', 'bunyad-widgets');
			return; 
		}
		
		$tabs = array();
		foreach ($titles as $key => $title) {
			
			// defaults missing?
			if (empty($tax_tags[$key])) {
				$tax_tags[$key] = '';
			}
			
			if (empty($cats[$key])) {
				$cats[$key] = '';
			}
			
			if (empty($posts[$key])) {
				$posts[$key] = (!empty($number) ? $number: 0);
			}
			
			$tabs[$title] = array('cat_type' => $cats[$key], 'tag' => $tax_tags[$key], 'posts' => $posts[$key]);
		}
				
		// latest posts
		$posts = $this->get_posts($tabs);
		
		// do custom loop if available
		if (has_action('bunyad_widget_tabbed_recent_loop')):
		
			$args['tabs'] = $tabs;
			do_action('bunyad_widget_tabbed_recent_loop', $args, $posts);
			
		else:
		
		?>
	
			<?php echo $before_widget; ?>
	
			<ul class="tabs-list">
			
				<?php
				$count = 0; 
				foreach ($posts as $key => $val): $count++; $active = ($count == 1 ? 'active' : ''); 
				?>
				
				<li class="<?php echo $active;?>">
					<a href="#" data-tab="<?php echo esc_attr($count); ?>"><?php echo $key; ?></a>
				</li>
				
				<?php endforeach; ?>
				
			</ul>
			
			<div class="tabs-data">
				<?php
					$i = 0; 
					foreach ($posts as $tab => $tab_posts): $i++; $active = ($i == 1 ? 'active' : ''); ?>
					
				<ul class="tab-posts <?php echo $active; ?> posts-list" id="recent-tab-<?php echo esc_attr($i); ?>">
				
				<?php if ($tabs[$tab] == 'comments'): ?>
	
					<?php 
					foreach ($tab_posts as $comment): 
					?>
					
					<li class="comment">
						
						<span class="author"><?php printf('%s said', get_comment_author_link($comment->comment_ID)); ?></span>
						
						<p class="text"><?php comment_excerpt($comment->comment_ID); ?></p>
						
						<a href=""><?php echo get_the_title($comment->comment_post_ID); ?></a>
					
					</li>
	
					<?php				
					endforeach; 
					?>
				
				
				<?php else: ?>
				
					<?php while ($tab_posts->have_posts()): $tab_posts->the_post(); ?>
	
					<li>
					
						<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
	
						<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
							<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
						<?php endif; ?>
						
						</a>
						
						<div class="content">
						
							<?php echo Bunyad::blocks()->meta('above', 'tabbed-recent', array('type' => 'widget')); ?>
						
							<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
								<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
								
							<?php echo Bunyad::blocks()->meta('below', 'tabbed-recent', array('type' => 'widget')); ?>
								
							<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
								<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
							<?php endif; ?>
																		
						</div>
					
					</li>
					
					<?php endwhile; ?>
					
				<?php endif; ?>
					
				</ul>
				<?php endforeach; ?>
			
			</div>
			
			<?php echo $after_widget; ?>
		
		<?php
		
		endif; // end custom loop
		
		wp_reset_postdata();
		wp_reset_query();

	}
	
	public function get_posts($tabs)
	{
		// posts available in cache? - use instance id to suffix
		$cache = get_transient('bunyad_tabbed_recent_posts');
		
		if (!defined('ICL_LANGUAGE_CODE') && is_array($cache) && isset($cache[$this->number])) {
			return $cache[$this->number];
		}

		// get posts
		$args = array('ignore_sticky_posts' => 1);
		foreach ($tabs as $key => $val) {	
			
			$opts = array();
			$opts['posts_per_page'] = $val['posts'];
			
			switch ($val['cat_type']) {
				case 'popular':
					$opts['orderby'] = 'comment_count';
					break;
					
				case 'comments':
					$posts[$key] = get_comments(array('number'=> $val['posts'], 'status' => 'approve'));
					continue 2; // jump switch and foreach loop
					
				case 'top-reviews':
					// get top rated of all time
					$opts = array_merge($opts, array('orderby' => 'meta_value', 'meta_key' => '_bunyad_review_overall'));
					break;
					
				case 'recent':
					break;
					
				case 'tag':
					$opts['tag'] = $val['tag'];
					break;
					
				default:
					$opts['cat'] = intval($val['cat_type']);
					break;
			}
						
			// setup the query
			$posts[$key] = new WP_Query(apply_filters('bunyad_widget_tabbed_recent_query_args', array_merge($args, $opts)));
		}
		
		if (!is_array($cache)) {
			$cache = array();
		}
		
		$cache[ $this->number ] = $posts;
		
		set_transient('bunyad_tabbed_recent_posts', $cache, 60*60*24*30); // 30 days transient cache
		
		return $posts;
	}

	public function flush_widget_cache()
	{
		delete_transient('bunyad_tabbed_recent_posts');
	}
	
	public function update($new, $old)
	{
		// fix categories
		foreach (array('cats', 'titles', 'tax_tags', 'posts') as $var) {
			
			foreach ($new[$var] as $key => $val) {
				$new[$var][$key] = trim(strip_tags($val));				
			}
				
		}
		
		$new['number'] = intval($new['number']);
		
		// delete cache
		$this->flush_widget_cache();

		return $new;
	}
	
	public function form($instance)
	{
		$instance = array_merge(array('titles' => array(), 'cats' => array(0), 'number' => 4, 'cat' => 0, 'tax_tags' => array()), $instance);
		
		extract($instance);
		
	?>
		
		<style>
			.widget-content p.separator { padding-top: 10px; border-top: 1px solid #d8d8d8; }
			.widget-content .tax_tag { display: none; }
		</style>
		
		
		<div id="tab-options">
			

		<script type="text/html" class="template-tab-options">
		<p class="title separator">
			<label><?php printf(__('Tab #%s Title:', 'bunyad-widgets'), '<span>%n%</span>'); ?></label>
			<input class="widefat" name="<?php 
				echo esc_attr($this->get_field_name('titles')); ?>[%n%]" type="text" value="%title%" />
		</p>
		
		
		<div class="cat">
			<label><?php printf(__('Tab #%s Category:', 'bunyad-widgets'), '<span>%n%</span>'); ?></label>
			<?php 
			
			$r = array('orderby' => 'name', 'hierarchical' => 1, 'selected' => $cat, 'show_count' => 0);
			
			// categories list
			$cats_list = walk_category_dropdown_tree(get_terms('category', $r), 0, $r);
			
			// custom options
			$options = apply_filters('bunyad_widget_tabbed_recent_options', array(
				'recent' => __('Recent Posts', 'bunyad-widgets'), 
				'popular' => __('Popular Posts', 'bunyad-widgets'), 
				'top-reviews' => __('Top Reviews', 'bunyad-widgets'),
				'comments' => __('Recent Comments', 'bunyad-widgets'),
				'tag' => __('Use a Tag', 'bunyad-widgets'),
			));
			
			?>

			<select name="<?php echo $this->get_field_name('cats') .'[%n%]'; ?>">

			<?php foreach ($options as $key => $val): ?>
	
				<option value="<?php echo esc_attr($key); ?>"<?php echo ($cat == $key ? ' selected' : ''); ?>><?php echo esc_html($val); ?></option>			
	
			<?php endforeach; ?>

				<optgroup label="<?php _e('Category', 'bunyad-widgets'); ?>">
					<?php echo $cats_list; ?>
				</optgroup>

			</select>

			<div class="tax_tag">
				<p><label><?php printf(__('Tab #%s Tag:', 'bunyad-widgets'), '<span>%n%</span>'); ?></label> <input type="text" name="<?php 
					echo esc_attr($this->get_field_name('tax_tags')); ?>[%n%]" value="%tax_tag%" /></p>
			</div>

			<p><?php _e('Posts:', 'bunyad'); ?> <input name="<?php echo $this->get_field_name('posts'); ?>[%n%]" type="text" value="%posts%" size="3" /></p>

			<p><a href="#" class="remove-recent-tab">[x] <?php _e('remove', 'bunyad-widgets'); ?></a></p>
		</div>
		</script>
				
			
			<p class="separator"><a href="#" id="add-more-tabs"><?php _e('Add More Tabs', 'bunyad-widgets'); ?></a></p>
			
			<?php

			if (is_integer($this->number)): // create for valid instances only 
			
				foreach ($cats as $n => $cat):
				
					if (!isset($tax_tags[$n])) {
						$tax_tags[$n] = '';
					}
					
					// set posts to default number
					if (!isset($posts[$n])) {
						$posts[$n] = $number;
					}
			?>
			
				<script>
					jQuery(function($) {
	
						$('.widget-liquid-right [id$="bunyad-tabbed-recent-widget-'+ <?php echo $this->number; ?> +'"] #add-more-tabs').trigger(
								'click', 
								[{
									'n': <?php echo ($n+1); ?>, 
									'title': '<?php echo esc_attr($titles[$n]); ?>', 
									'selected': '<?php echo esc_attr($cat); ?>',
									'tax_tag': '<?php echo esc_attr($tax_tags[$n]); ?>',
									'posts' : '<?php echo esc_attr($posts[$n]); ?>',
								}]);
					});
				</script>
			
			<?php
				endforeach; 
			endif; 
			?>
			
		</div>	
		
	<?php
	}
	
}