<?php
/*
 * Resize images dynamically using wp built in functions
 * Originally by Victor Teixeira
 * Modified by Jordy Meow for WP Retina 2x
 */

if ( !function_exists('wr2x_vt_resize') ) {
	function wr2x_vt_resize( $file_path, $width, $height, $crop, $newfile, $customCrop = false ) {
		$crop_params = $crop == '1' ? true : $crop;
		$orig_size = getimagesize( $file_path );
		$image_src[0] = $file_path;
		$image_src[1] = $orig_size[0];
		$image_src[2] = $orig_size[1];
		$file_info = pathinfo( $file_path );
		$newfile_info = pathinfo( $newfile );
		$extension = '.' . $newfile_info['extension'];		
		$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];
		$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . "-tmp" . $extension;
		$image = wp_get_image_editor( $file_path );

		if ( is_wp_error( $image ) ) {
			wr2x_log( "Resize failure: " . $image->get_error_message() );
			error_log( "Resize failure: " . $image->get_error_message() );
			return null;
		}

		// Resize or use Custom Crop
		if ( !$customCrop )
			$image->resize( $width, $height, $crop_params );
		else
			$image->crop( $customCrop['x'] * $customCrop['scale'], $customCrop['y'] * $customCrop['scale'], $customCrop['w'] * $customCrop['scale'], $customCrop['h'] * $customCrop['scale'], $width, $height, false );

		$quality = wr2x_getoption( "image_quality", "wr2x_advanced", "80" );
		if ( is_numeric( $quality ) )
			$image->set_quality( intval( $quality ) );
		$saved = $image->save( $cropped_img_path );
		if ( rename( $saved['path'], $newfile ) )
			$cropped_img_path = $newfile;
		else {
			trigger_error( "Retina: Could not move " . $saved['path'] . " to " . $newfile . "." , E_WARNING );
			error_log( "Retina: Could not move " . $saved['path'] . " to " . $newfile . "." );
			return null;
		}
		$new_img_size = getimagesize( $cropped_img_path );
		$new_img = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
		$vt_image = array ( 'url' => $new_img, 'width' => $new_img_size[0], 'height' => $new_img_size[1] );
		return $vt_image;
	}
}
