<?php

add_action( 'wp_ajax_wr2x_generate', 'wr2x_wp_ajax_wr2x_generate' );
add_action( 'wp_ajax_wr2x_delete', 'wr2x_wp_ajax_wr2x_delete' );
add_action( 'wp_ajax_wr2x_delete_full', 'wr2x_wp_ajax_wr2x_delete_full' );
add_action( 'wp_ajax_wr2x_list_all', 'wr2x_wp_ajax_wr2x_list_all' );
add_action( 'wp_ajax_wr2x_replace', 'wr2x_wp_ajax_wr2x_replace' );
add_action( 'wp_ajax_wr2x_upload', 'wr2x_wp_ajax_wr2x_upload' );
add_action( 'wp_ajax_wr2x_retina_details', 'wr2x_wp_ajax_wr2x_retina_details' );
add_action( 'admin_head', 'wr2x_admin_head' );

/**
 *
 * AJAX CLIENT-SIDE
 *
 */

function wr2x_admin_head() {
	?>
	<script type="text/javascript" >

		/* GENERATE RETINA IMAGES ACTION */

		var current;
		var maxPhpSize = <?php echo (int)ini_get('upload_max_filesize') * 1000000; ?>;
		var ids = [];
		var ajax_action = "generate"; // generate | delete

		function wr2x_display_please_refresh() {
			jQuery('#wr2x_progression').html("<?php echo _e( "<a href='?page=wp-retina-2x&view=issues&refresh=true'>Refresh</a> this page.", 'wp-retina-2x' ); ?>");
		}

		function wr2x_do_next () {
			var data = { action: 'wr2x_' + ajax_action, attachmentId: ids[current - 1] };
			jQuery('#wr2x_progression').text(current + "/" + ids.length + " (" + Math.round(current / ids.length * 100) + "%)");
			jQuery.post(ajaxurl, data, function (response) {
				reply = jQuery.parseJSON(response);
				if (reply.success = false) {
					alert('Error: ' + reply.message);
					return;
				}
				wr2x_refresh_media_sizes(reply.results);
				if (reply.results_full) {
					wr2x_refresh_full(reply.results_full);
				}
				if (++current <= ids.length)
					wr2x_do_next();
				else {
					wr2x_display_please_refresh();
				}
			});
		}

		function wr2x_do_all () {
			current = 1;
			ids = [];
			var data = { action: 'wr2x_list_all', issuesOnly: 0 };
			jQuery('#wr2x_progression').text("<?php _e( "Wait...", 'wp-retina-2x' ); ?>");
			jQuery.post(ajaxurl, data, function (response) {
				reply = jQuery.parseJSON(response);
				if (reply.success = false) {
					alert('Error: ' + reply.message);
					return;
				}
				if (reply.total == 0) {
					jQuery('#wr2x_progression').html("<?php _e( "Nothing to do ;)", 'wp-retina-2x' ); ?>");
					return;
				}
				ids = reply.ids;
				jQuery('#wr2x_progression').text(current + "/" + ids.length + " (" + Math.round(current / ids.length * 100) + "%)");
				wr2x_do_next();
			});
		}

		function wr2x_delete_all () {
			ajax_action = 'delete';
			wr2x_do_all();
		}

		function wr2x_generate_all () {
			ajax_action = 'generate';
			wr2x_do_all();
		}

		// Refresh the dashboard retina full with the results from the Ajax operation (Upload)
		function wr2x_refresh_full (results) {
			jQuery.each(results, function (id, html) {
				jQuery('#wr2x-info-full-' + id).html(html);
				jQuery('#wr2x-info-full-' + id + ' img').attr('src', jQuery('#wr2x-info-full-' + id + ' img').attr('src')+'?'+ Math.random());
				jQuery('#wr2x-info-full-' + id + ' img').on('click', function (evt) {
					wr2x_delete_full( jQuery(evt.target).parents('.wr2x-file-row').attr('postid') );
				});
			});
		}

		// Refresh the dashboard media sizes with the results from the Ajax operation (Replace or Generate)
		function wr2x_refresh_media_sizes (results) {
			jQuery.each(results, function (id, html) {
				jQuery('#wr2x-info-' + id).html(html);
			});
		}

		function wr2x_generate (attachmentId, retinaDashboard) {
			var data = { action: 'wr2x_generate', attachmentId: attachmentId };
			jQuery('#wr2x_generate_button_' + attachmentId).text("<?php echo __( "Wait...", 'wp-retina-2x' ); ?>");
			jQuery.post(ajaxurl, data, function (response) {
				var reply = jQuery.parseJSON(response);
				if (!reply.success) {
					alert(reply.message);
					return;
				}
				jQuery('#wr2x_generate_button_' + attachmentId).html("<?php echo __( "GENERATE", 'wp-retina-2x' ); ?>");
				wr2x_refresh_media_sizes(reply.results);
			});
		}

		/* REPLACE FUNCTION */

		function wr2x_stop_propagation(evt) {
			evt.stopPropagation();
			evt.preventDefault();
		}

		function wr2x_delete_full(attachmentId) {
			var data = {
				action: 'wr2x_delete_full',
				isAjax: true,
				attachmentId: attachmentId
			};

			jQuery.post(ajaxurl, data, function (response) {
				var data = jQuery.parseJSON(response);
				if (data.success === false) {
					alert(data.message);
				}
				else {
					wr2x_refresh_full(data.results);
					wr2x_display_please_refresh();
				}
			});
		}

		function wr2x_load_details(attachmentId) {
			var data = {
				action: 'wr2x_retina_details',
				isAjax: true,
				attachmentId: attachmentId
			};

			jQuery.post(ajaxurl, data, function (response) {
				var data = jQuery.parseJSON(response);
				if (data.success === false) {
					alert(data.message);
				}
				else {
					jQuery('#wr2x-modal-info .loading').css('display', 'none');
					jQuery('#wr2x-modal-info .content').html(data.result);
				}
			});
		}

		function wr2x_filedropped (evt) {
			wr2x_stop_propagation(evt);
			var files = evt.dataTransfer.files;
			var count = files.length;
			if (count < 0) {
				return;
			}

			var wr2x_replace = jQuery(evt.target).parent().hasClass('wr2x-fullsize-replace');
			var wr2x_upload = jQuery(evt.target).parent().hasClass('wr2x-fullsize-retina-upload');

			function wr2x_handleReaderLoad(evt) {
				var attachmentId = evt.target.attachmentId;
				var fileData = evt.target.result;
				fileData = fileData.substr(fileData.indexOf('base64') + 7);
				var action = "";
				if (wr2x_replace) {
					action = 'wr2x_replace';
				}
				else if (wr2x_upload) {
					action = 'wr2x_upload';
				}
				else {
					alert("Unknown command. Contact the developer.");
				}
				var data = {
					action: action,
					isAjax: true,
					filename: evt.target.filename,
					data: fileData,
					attachmentId: evt.target.attachmentId
				};

				jQuery.post(ajaxurl, data, function (response) {
					var data = jQuery.parseJSON(response);
					jQuery('[postid=' + attachmentId + '] td').removeClass('wr2x-loading-file');
					jQuery('[postid=' + attachmentId + '] .wr2x-dragdrop').removeClass('wr2x-hover-drop');

					if (wr2x_replace) {
						var imgSelector = '[postid=' + attachmentId + '] .wr2x-info-thumbnail img';
						jQuery(imgSelector).attr('src', jQuery(imgSelector).attr('src')+'?'+ Math.random());
					}
					if (wr2x_upload) {
						var imgSelector = '[postid=' + attachmentId + '] .wr2x-info-full img';
						jQuery(imgSelector).attr('src', jQuery(imgSelector).attr('src')+'?'+ Math.random());
					}

					if (data.success === false) {
						alert(data.message);
					}
					else {
						if ( wr2x_replace ) {
							wr2x_refresh_media_sizes(data.results);
						}
						else if ( wr2x_upload ) {
							wr2x_refresh_full(data.results);
						}
					}
				});
			}

			var file = files[0];
			if (file.size > maxPhpSize) {
				jQuery(this).removeClass('wr2x-hover-drop');
				alert( "Your PHP configuration only allows file upload of a maximum of " + (maxPhpSize / 1000000) + "MB." );
				return;
			}

			jQuery(evt.target).parents('td').addClass('wr2x-loading-file');
			var reader = new FileReader();
			reader.filename = file.name;
			reader.attachmentId = jQuery(evt.target).parents('.wr2x-file-row').attr('postid');
			reader.onload = wr2x_handleReaderLoad;
			reader.readAsDataURL(file);
		}

		jQuery(document).ready(function () {
			jQuery('.wr2x-dragdrop').on('dragenter', function (evt) {
				wr2x_stop_propagation(evt);
				jQuery(this).addClass('wr2x-hover-drop');
			});

			jQuery('.wr2x-dragdrop').on('dragover', function (evt) {
				wr2x_stop_propagation(evt);
				jQuery(this).addClass('wr2x-hover-drop');
			});

			jQuery('.wr2x-dragdrop').on('dragleave', function (evt) {
				wr2x_stop_propagation(evt);
				jQuery(this).removeClass('wr2x-hover-drop');
			});

			jQuery('.wr2x-dragdrop').on('dragexit', wr2x_stop_propagation);

			jQuery('.wr2x-dragdrop').each(function (index, elem) {
				this.addEventListener('drop', wr2x_filedropped);
			});

			jQuery('.retina-info, .wr2x-button-view').on('click', function (evt) {
				jQuery('#wr2x-modal-info-backdrop').css('display', 'block');
				jQuery('#wr2x-modal-info .content').html("");
				jQuery('#wr2x-modal-info .loading').css('display', 'block');
				jQuery('#wr2x-modal-info').css('display', 'block');
				jQuery('#wr2x-modal-info').focus();
				wr2x_load_details(jQuery(evt.target).parents('.wr2x-file-row').attr('postid'));
			});

			jQuery('#wr2x-modal-info .close, #wr2x-modal-info-backdrop').on('click', function (evt) {
				jQuery('#wr2x-modal-info').css('display', 'none');
				jQuery('#wr2x-modal-info-backdrop').css('display', 'none');
			});

			jQuery('.wr2x-info-full img').on('click', function (evt) {
				wr2x_delete_full( jQuery(evt.target).parents('.wr2x-file-row').attr('postid') );
			});

			jQuery('#wr2x-modal-info').bind('keydown', function (evt) {
				if (evt.keyCode === 27) {
					jQuery('#wr2x-modal-info').css('display', 'none');
					jQuery('#wr2x-modal-info-backdrop').css('display', 'none');
				}
			});
		});

	</script>
	<?php
}

/**
 *
 * AJAX SERVER-SIDE
 *
 */

// Using issuesOnly, only the IDs with a PENDING status will be processed
function wr2x_wp_ajax_wr2x_list_all( $issuesOnly ) {
	$issuesOnly = intval( $_POST['issuesOnly'] );
	if ( $issuesOnly == 1 ) {
		$ids = wr2x_get_issues();
		echo json_encode(
			array(
				'success' => true,
				'message' => "List of issues only.",
				'ids' => $ids,
				'total' => count( $ids )
		) );
		die;
	}
	$reply = array();
	try {
		$ids = array();
		$total = 0;
		global $wpdb;
		$postids = $wpdb->get_col( "
			SELECT p.ID
			FROM $wpdb->posts p
			WHERE post_status = 'inherit'
			AND post_type = 'attachment'
			AND ( post_mime_type = 'image/jpeg' OR
				post_mime_type = 'image/png' OR
				post_mime_type = 'image/gif' )
		" );
		$ignore = wr2x_getoption( "ignore_sizes", "wr2x_basics", array() );
		foreach ($postids as $id) {
			if ( wr2x_is_ignore( $id ) )
				continue;
			array_push( $ids, $id );
			$total++;
		}
		echo json_encode(
			array(
				'success' => true,
				'message' => "List of everything.",
				'ids' => $ids,
				'total' => $total
		) );
		die;
	}
	catch (Exception $e) {
		echo json_encode(
			array(
				'success' => false,
				'message' => $e->getMessage()
		) );
		die;
	}
}

function wr2x_wp_ajax_wr2x_delete_full( $pleaseReturn = false ) {

	if ( !isset( $_POST['attachmentId'] ) ) {
		echo json_encode(
			array(
				'success' => false,
				'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
			)
		);
		die();
	}
	$attachmentId = intval( $_POST['attachmentId'] );
	$originalfile = get_attached_file( $attachmentId );
	$pathinfo = pathinfo( $originalfile );
	$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
	if ( $retina_file && file_exists( $retina_file ) )
		unlink( $retina_file );

	// RESULTS FOR RETINA DASHBOARD
	$info = wpr2x_html_get_basic_retina_info_full( $attachmentId, wr2x_retina_info( $attachmentId ) );
	$results[$attachmentId] = $info;

	// Return if that's not the final step.
	if ( $pleaseReturn )
		return $info;

	echo json_encode(
		array(
			'results' => $results,
			'success' => true,
			'message' => __( "Full retina file deleted.", 'wp-retina-2x' )
		)
	);
	die();
}

function wr2x_wp_ajax_wr2x_delete() {

	if ( !isset( $_POST['attachmentId'] ) ) {
		echo json_encode(
			array(
				'success' => false,
				'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
			)
		);
		die();
	}

	// Information for the retina version of the full-size
	$attachmentId = intval( $_POST['attachmentId'] );
	$results_full[$attachmentId] = wr2x_wp_ajax_wr2x_delete_full( true );

	wr2x_delete_attachment( $attachmentId );
	$meta = wp_get_attachment_metadata( $attachmentId );

	// RESULTS FOR RETINA DASHBOARD
	wr2x_update_issue_status( $attachmentId );
	$info = wpr2x_html_get_basic_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
	$results[$attachmentId] = $info;
	echo json_encode(
		array(
			'results' => $results,
			'results_full' => $results_full,
			'success' => true,
			'message' => __( "Retina files deleted.", 'wp-retina-2x' )
		)
	);
	die();
}

function wr2x_wp_ajax_wr2x_retina_details() {
	if ( !isset( $_POST['attachmentId'] ) ) {
		echo json_encode(
			array(
				'success' => false,
				'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
			)
		);
		die();
	}

	$attachmentId = intval( $_POST['attachmentId'] );
	$info = wpr2x_html_get_details_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
	echo json_encode(
		array(
			'result' => $info,
			'success' => true,
			'message' => __( "Details retrieved.", 'wp-retina-2x' )
		)
	);
	die();
}

function wr2x_wp_ajax_wr2x_generate() {

	if ( !isset( $_POST['attachmentId'] ) ) {
		echo json_encode(
			array(
				'success' => false,
				'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
			)
		);
		die();
	}

	$attachmentId = intval( $_POST['attachmentId'] );
	wr2x_delete_attachment( $attachmentId );
	$meta = wp_get_attachment_metadata( $attachmentId );
	wr2x_generate_images( $meta );

	// RESULTS FOR RETINA DASHBOARD
	$info = wpr2x_html_get_basic_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
	$results[$attachmentId] = $info;
	echo json_encode(
		array(
			'results' => $results,
			'success' => true,
			'message' => __( "Retina files generated.", 'wp-retina-2x' )
		)
	);
	die();
}

function wr2x_check_get_ajax_uploaded_file() {
		if ( !current_user_can('upload_files') ) {
		echo json_encode( array(
			'success' => false,
			'message' => __( "You do not have permission to upload files.", 'wp-retina-2x' )
		));
		die();
	}

	$data = $_POST['data'];

	// Create the file as a TMP
	if ( is_writable( sys_get_temp_dir() ) ) {
		$tmpfname = tempnam( sys_get_temp_dir(), "wpx_" );
		wr2x_log( "Upload a temporary file in $tmpfname (sys_get_temp_dir)." );
	}
	else if ( is_writable( wr2x_get_upload_root() ) ) {
		if ( !file_exists( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp" ) )
			mkdir( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp" );
		$tmpfname = tempnam( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp", "wpx_" );
		wr2x_log( "Upload a temporary file in $tmpfname (wr2x_get_upload_root)." );
	}

	if ( $tmpfname == null || $tmpfname == FALSE ) {
		$tmpdir = get_temp_dir();
		error_log( "Retina: The temporary directory could not be created." );
		wr2x_log( "The temporary directory could not be created." );
		echo json_encode( array(
			'success' => false,
			'message' => __( "The temporary directory could not be created.", 'wp-retina-2x' )
		));
		die;
	}

	$handle = fopen( $tmpfname, "w" );
	fwrite( $handle, base64_decode( $data ) );
	fclose( $handle );
	chmod( $tmpfname, 0664 );

	// Check if it is an image
	$file_info = getimagesize( $tmpfname );
	if ( empty( $file_info ) ) {
		wr2x_log( "The file is not an image or the upload went wrong." );
		unlink( $tmpfname );
		echo json_encode( array(
			'success' => false,
			'message' => __( "The file is not an image or the upload went wrong.", 'wp-retina-2x' )
		));
		die();
	}

	$filedata = wp_check_filetype_and_ext( $tmpfname, $_POST['filename'] );
	if ( $filedata["ext"] == "" ) {
		wr2x_log( "You cannot use this file (wrong extension? wrong type?)." );
		unlink( $current_file );
		echo json_encode( array(
			'success' => false,
			'message' => __( "You cannot use this file (wrong extension? wrong type?).", 'wp-retina-2x' )
		));
		die();
	}

	wr2x_log( "The temporary file was written successfully." );
	return $tmpfname;
}

function wr2x_wp_ajax_wr2x_upload() {
	$tmpfname = wr2x_check_get_ajax_uploaded_file();
	$attachmentId = (int) $_POST['attachmentId'];
	$meta = wp_get_attachment_metadata( $attachmentId );
	$current_file = get_attached_file( $attachmentId );
	$pathinfo = pathinfo( $current_file );
	$basepath = $pathinfo['dirname'];
	$retinafile = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];

	if ( file_exists( $retinafile ) )
		unlink( $retinafile );

	// Insert the new file and delete the temporary one
	list( $width, $height ) = getimagesize( $tmpfname );

	if ( !wr2x_are_dimensions_ok( $width, $height, $meta['width'] * 2, $meta['height'] * 2 ) ) {
		echo json_encode( array(
			'success' => false,
			'message' => "This image has a resolution of ${width}×${height} but your Full Size image requires a retina image of at least " . ( $meta['width'] * 2 ) . "x" . ( $meta['height'] * 2 ) . "."
		));
		die();
	}
	require('wr2x_vt_resize.php');
	wr2x_vt_resize( $tmpfname, $meta['width'] * 2, $meta['height'] * 2, null, $retinafile );
	chmod( $retinafile, 0644 );
	unlink( $tmpfname );

	// Get the results
	$info = wr2x_retina_info( $attachmentId );
	wr2x_update_issue_status( $attachmentId );
	$results[$attachmentId] = wpr2x_html_get_basic_retina_info_full( $attachmentId, $info );

	echo json_encode( array(
		'success' => true,
		'results' => $results,
		'message' => __( "Uploaded successfully.", 'wp-retina-2x' )
	));
	die();
}

function wr2x_wp_ajax_wr2x_replace() {
	$tmpfname = wr2x_check_get_ajax_uploaded_file();
	$attachmentId = (int) $_POST['attachmentId'];
	$meta = wp_get_attachment_metadata( $attachmentId );
	$current_file = get_attached_file( $attachmentId );
	wr2x_delete_attachment( $attachmentId );
	$pathinfo = pathinfo( $current_file );
	$basepath = $pathinfo['dirname'];

	// Let's clean everything first
	if ( wp_attachment_is_image( $attachmentId ) ) {
		$sizes = wr2x_get_image_sizes();
		foreach ($sizes as $name => $attr) {
			if ( isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) && file_exists( trailingslashit( $basepath ) . $meta['sizes'][$name]['file'] ) ) {
				$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
				$pathinfo = pathinfo( $normal_file );
				$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];

				// Test if the file exists and if it is actually a file (and not a dir)
				// Some old WordPress Media Library are sometimes broken and link to directories
				if ( file_exists( $normal_file ) && is_file( $normal_file ) )
					unlink( $normal_file );
				if ( file_exists( $retina_file ) && is_file( $retina_file ) )
					unlink( $retina_file );
			}
		}
	}
	if ( file_exists( $current_file ) )
		unlink( $current_file );

	// Insert the new file and delete the temporary one
	rename( $tmpfname, $current_file );
	chmod( $current_file, 0644 );

	// Generate the images
	wp_update_attachment_metadata( $attachmentId, wp_generate_attachment_metadata( $attachmentId, $current_file ) );
	$meta = wp_get_attachment_metadata( $attachmentId );
	wr2x_generate_images( $meta );

	// Get the results
	$info = wr2x_retina_info( $attachmentId );
	$results[$attachmentId] = wpr2x_html_get_basic_retina_info( $attachmentId, $info );

	echo json_encode( array(
		'success' => true,
		'results' => $results,
		'message' => __( "Replaced successfully.", 'wp-retina-2x' )
	));
	die();
}

?>
