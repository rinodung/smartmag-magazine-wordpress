<?php

/**
 * Search 
 */

?>

<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>" class="bbp-search">
	<div>
		<label class="screen-reader-text hidden" for="bbp_search"><?php _ex('Search for:', 'bbPress', 'bunyad'); ?></label>
		<input type="hidden" name="action" value="bbp-search-request" />
		
		<input tabindex="<?php bbp_tab_index(); ?>" class="button" type="submit" id="bbp_search_submit" value="<?php echo esc_attr_x('Search', 'bbPress', 'bunyad'); ?>" />
		
		
		<div class="search-for">
			<input tabindex="<?php bbp_tab_index(); ?>" type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="bbp_search" />
		</div>
	</div>
</form>
