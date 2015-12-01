<?php 

//require_once('../../../../../wp-load.php');

// include admin features for plugin compatibility
//require_once('../../../../../wp-admin/includes/admin.php');

//do_action('admin_init');


function bunyad_shortcode_editor_plugin() 
{
	global $tinymce_version;
	
	// check auth
	if (!is_user_logged_in() OR !current_user_can('edit_posts')) {
		die('You do not have the right type of authorization. You must be logged in and be able to edit pages and posts.');
	}
	
	// javascript will be output
	header('Content-type: application/x-javascript');
	
	/*
	 * Create shortcode generator menu javascript calls
	 */
	$shortcodes = Bunyad_ShortCodes::getInstance()->get_all();
	$shortcode_menu = '';
	foreach ($shortcodes as $key => $value)
	{
		$output = ''; 
		$data = array();
		
		foreach ($value as $id => $shortcode) {
			
			// defaults
			$shortcode = array_merge(array('label' => null, 'dialog' => false, 'code' => null), $shortcode);
			
			if (!$shortcode['label']) {
				continue;
			}
			
			$dialog  = $shortcode['dialog'] ? true : false;
			$options = array('title' => $shortcode['label'], 'id' => $id, 'dialog' => $dialog);

			// no dialog? has a predefined code to insert by default?
			if (!$dialog && $shortcode['code']) {
				$options['insert_code'] = $shortcode['code'];
			}
		
			// add top-level to output; collect sub-menu items
			if ($key == 'default') {
				$output .= "add_dialog(list, ". json_encode($options) .");\n";
			}
			else {
				$data[] = $options;
			}
		}
		
		// top-level
		if ($key == 'default') {
			$shortcode_menu .= $output;
		}
		else { // sub-menu
			
			$shortcode_menu .= 'add_dialog(list, {title: "' . esc_attr($key) .'"}, ' . json_encode($data) . ");\n";
		}
	}
	
?>

(function() {
	//******* Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('example')
	
	<?php 
		if ($tinymce_version[0] >= 4): 

		/*
		 * Used for WordPress 3.9 and TinyMCE >= 4
		 */
	?>

		tinymce.PluginManager.add('bunyad_shortcodes', function(editor, url) {
	
				var list = [];
				
				var add_dialog = function(list, item, sub) {
				
					if (sub && sub.length > 0) {
											
						var sub_list = [];
						
						for (i in sub) {
							add_dialog(sub_list, sub[i]);
						}
						
						list[list.length] = {text: item.title, menu: sub_list};
						
						return;
					}
					
					list[list.length] = {text: item.title, onclick: function() { // onclick
						
						// TODO: add cache
						if (item.dialog === true) {
							//tb_show('<?php _e('Shortcode: ', 'bunyad-shortcodes')?>' + item.title, '<?php echo plugin_dir_url(__FILE__) . 'shortcode-popup.php?shortcode='; ?>' + item.id);
							tb_show('<?php _e('Shortcode: ', 'bunyad-shortcodes')?>' + item.title, '<?php echo admin_url('admin-ajax.php') . '?action=bunyad_shortcode_popup&shortcode='; ?>' + item.id);
							
							jQuery('#TB_ajaxContent').css({width: 'auto', height: '90%'});
							
							var counter = 0,
								set_height = function() { 
							
								var height = jQuery('#TB_window').height();
								counter++;
								
								if (counter > 20) {
									return;
								}
								
								if (height < 100) {
									window.setTimeout(set_height, 500);
								}
								
								jQuery('#TB_ajaxContent').css('height', jQuery('#TB_window').height() - 45 + 'px');
							};
							
							set_height();
							
						}
						else { // just insert
						
							var shortcode, selected, 
								ed = tinyMCE.activeEditor;
							
							selected = ed.selection.getContent();
							
							if (!item.insert_code) {
								shortcode = '[' + item.id + ']%selected%[/' + item.id + ']'; 
							}
	
							if (jQuery.trim(selected) === '') {
								selected = '<?php esc_attr_e('INSERT HERE', 'bunyad-shortcodes'); ?>'; 
							}
	
							shortcode = shortcode.replace('%selected%', selected);
							ed.execCommand('mceReplaceContent', false, shortcode);
						}						
					}};
				};
				
				<?php echo $shortcode_menu; ?>
	
				editor.addButton('bunyad_shortcodes', {
					type: 'menubutton',
					text: '<?php esc_attr_e('Shortcodes', 'bunyad-shortcodes'); ?>', 
					icon: false,
					menu: list
				});
		});
	
	<?php 
		else:
	/*
	 * Older than WordPress 3.8 Below - TinyMCE 3.x compat
	 */
	?>

	tinymce.create('tinymce.plugins.bunyad_shortcodes', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
		
			// fix some styling issues and add correct wording
			ed.onPostRender.add(function(ed, cm) {
				var ele = jQuery('.mceAction .mce_bunyad_shortcodesList');
				ele.html('<?php _e('Shortcodes', 'bunyad-shortcodes'); ?>');
				
				ele.removeClass('mceAction').css({width: 'auto'});
				ele.parent().css({width: 'auto', 'line-height': '1.7em', 'padding': '1px 5px'});
			});			
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			
			var add_dialog = function(list, item, sub) {
			
				if (sub && sub.length > 0) {
					var sub_list = list.addMenu({title: item.title});
					
					for (i in sub) {
						add_dialog(sub_list, sub[i]);
					}
					
					return;
				}
				
				list.add({title: item.title, onclick: function() { // onclick
					
					// TODO: add cache
					if (item.dialog === true) {
						tb_show('<?php _e('Shortcode: ', 'bunyad-shortcodes')?>' + item.title, '<?php echo admin_url('admin-ajax.php') . '?action=bunyad_shortcode_popup&shortcode='; ?>' + item.id);
						
						jQuery('#TB_ajaxContent').css({width: 'auto', height: '90%'});
						
						var counter = 0,
							set_height = function() { 
						
							var height = jQuery('#TB_window').height();
							counter++;
							
							if (counter > 20) {
								return;
							}
							
							if (height < 100) {
								window.setTimeout(set_height, 500);
							}
							
							jQuery('#TB_ajaxContent').css('height', jQuery('#TB_window').height() - 45 + 'px');
						};
						
						set_height();
						
						
					}
					else { // just insert
					
						var shortcode, selected, 
							ed = tinyMCE.activeEditor;
						
						selected = ed.selection.getContent();
						
						if (!item.insert_code) {
							shortcode = '[' + item.id + ']%selected%[/' + item.id + ']'; 
						}

						if (jQuery.trim(selected) === '') {
							selected = '<?php _e('INSERT HERE', 'bunyad-shortcodes'); ?>'; 
						}

						shortcode = shortcode.replace('%selected%', selected);
						ed.execCommand('mceReplaceContent', false, shortcode);
					}
					
					// open window
					/*tinyMCE.activeEditor.windowManager.open({
						url: '<?php echo plugin_dir_url(__FILE__) . 'shortcode-popup.php?shortcode='; ?>' + item.id,
						inline: 1,
						width: 500,
						height: parseInt(jQuery(window).height()) * 0.8,
					});*/		
					
				}}); 
				
				return list;
			};
		
			if (n == 'bunyad_shortcodes') {
			
				var button = cm.createSplitButton('bunyad_shortcodesList', {
					title: '<?php echo esc_attr__('Shortcodes', 'bunyad-shortcodes'); ?>',
					icons: false
				});
				
				button.onRenderMenu.add(function(c, list) {
				
				<?php echo $shortcode_menu; ?>
					
				});
	
                // Return the new listbox instance
                return button;
             }
             
             return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Shortcode selector',
				author : 'asad',
				authorurl : 'http://twitter.com/asadkn',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bunyad_shortcodes', tinymce.plugins.bunyad_shortcodes);
	
	<?php endif; // end version check ?>
	
})();

<?php
 
	die(); // end ajax request

} // end function

?>