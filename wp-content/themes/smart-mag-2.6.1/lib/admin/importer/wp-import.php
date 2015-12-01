<?php

/**
 * Extend the native WordPress importer to make the process better
 */

class Bunyad_Admin_Importer_WpImport extends WP_Import {

	public $the_images = array();
	public $current_url = '';
	public $attachments_original = array();

	public function __construct() 
	{
		// called via lib/importer.php when the import process begins
		@set_time_limit(0);
		@ini_set('memory_limit', '512M');

		// pre-load featured images path
		$this->the_images = glob(get_template_directory() . '/admin/demo-data/images/*.jpg');

		// attachments that will be fetched from original source instead of being replaced a random image
		$this->attachments_original = apply_filters('bunyad_import_attachments_original', $this->attachments_original);

		//add_action('wp_insert_post', array($this, 'send_flush'));
	}

	public function process_posts()
	{
		do_action('bunyad_import_process_posts_pre', $this);

		parent::process_posts();
	}

	/**
	 * Add an action at the process_term
	 * 
	 * @see WP_Import::process_terms()
	 */
	public function process_terms()
	{
		parent::process_terms();

		do_action('bunyad_import_process_terms', $this);
	}

	/**
	 * Use local random images for attachments
	 * 
	 * @see WP_Import::process_attachment()
	 */
	public function process_attachment($post, $url)
	{
		$this->current_url = $url;

		if (!in_array($url, $this->attachments_original)) {
			// pick a random image 
			if (is_array($this->the_images) && count($this->the_images)) {

				$image = $this->the_images[ array_rand($this->the_images) ];
				$url = content_url(substr($image, strrpos($image, '/themes/')));
			}
			else {
				return;
			}
		}

		// process at the parent
		// return parent::process_attachment($post, $url);
		/**
		 * Code from WordPress Importer WP_Import::process_attachment()
		 */
		
		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) )
			$url = rtrim( $this->base_url, '/' ) . $url;

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) )
			return $upload;

		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wordpress-importer') );

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		$metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
		wp_update_attachment_metadata( $post_id, $metadata );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$url = $this->current_url;
			
			// old file - the local file from demo-data
			// new file - the new local file in uploads
			$parts = pathinfo($url);
			$parts_new = pathinfo($upload['url']);
			
			/**
			 * Since images are dynamic, the aspect ratio might be different - so images that don't have a crop
			 * will need changing the -1024xYYY.jpg part too
			 */
					$old_sizes = array();
			foreach ($this->posts as $orig_post) {
				if ($orig_post['post_id'] == $post['import_id']) {
					foreach ($orig_post['postmeta'] as $meta) {
						if ($meta['key'] == '_wp_attachment_metadata') {
							$old_sizes = unserialize($meta['value']);
							$old_sizes = $old_sizes['sizes'];
							break;	
						}
					}
					break;
				}
			}
			
			/* Large */
			if (!empty($old_sizes['large'])) {
				$dims = image_resize_dimensions($metadata['width'], $metadata['height'], 1024, 1024);
				list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

				$old_ext = pathinfo($old_sizes['large']['file'], PATHINFO_EXTENSION);			

				$name = $parts['filename']. '-' . $old_sizes['large']['width'] . 'x' . $old_sizes['large']['height'] . '.' . $old_ext; 
				$name_new = $parts_new['filename'] . "-{$dst_w}x{$dst_h}" . '.' . $parts['extension'];
				$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
			}
			
			/* Medium */
			if (!empty($old_sizes['medium'])) {
				$dims = image_resize_dimensions($metadata['width'], $metadata['height'], 300, 300);
				list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

				$old_ext = pathinfo($old_sizes['medium']['file'], PATHINFO_EXTENSION);			

				$name = $parts['filename']. '-' . $old_sizes['medium']['width'] . 'x' . $old_sizes['medium']['height'] . '.' . $old_ext; 
				$name_new = $parts_new['filename'] . "-{$dst_w}x{$dst_h}" . '.' . $parts['extension'];
				$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
			}
			
			/* Others - remap resized image URLs, works by stripping the extension and remapping the URL stub. */
			$name = basename($parts['basename'], ".{$parts['extension']}"); // PATHINFO_FILENAME in PHP 5.2
			$name_new = basename($parts_new['basename'], ".{$parts_new['extension']}");
			$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}
		
		return $post_id;
	}

	public function send_flush()
	{
		echo "<!-- flush -->";
		flush();
	}

	/**
	 * Use local images instead of remote images
	 * 
	 * @see WP_Import::fetch_remote_file()
	 */
	public function fetch_remote_file($url, $post)
	{
		// only use if demo-data is present in the url
		if (!strstr($url, '/demo-data/')) {
			return parent::fetch_remote_file($url, $post);
		}

		// extract the file name and extension from the url
		$file_name = basename($url);

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// use the local image and write it to the placeholder file
		$local_file = WP_CONTENT_DIR . '/' . substr($url, strrpos($url, '/themes/'));
		file_put_contents($upload['file'], file_get_contents($local_file));

		$filesize = filesize($upload['file']);

		if (0 == $filesize) {
			@unlink($upload['file']);
			return new WP_Error('import_file_error', __('Zero size file downloaded', 'wordpress-importer'));
		}

		$max_size = (int) $this->max_attachment_size();
		if (!empty( $max_size ) && $filesize > $max_size) {
			@unlink($upload['file']);
			return new WP_Error('import_file_error', sprintf(__('Remote file is too large, limit is %s', 'wordpress-importer'), size_format($max_size)));
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?

		return $upload;
	}
}
