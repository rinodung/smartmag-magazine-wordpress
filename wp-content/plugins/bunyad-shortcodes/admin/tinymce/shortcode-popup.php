<?php

/*require_once('../../../../../wp-load.php');
require_once('../../../../../wp-admin/includes/admin.php');

// check auth
if (!is_user_logged_in() OR !current_user_can('edit_pages') OR !current_user_can('edit_posts')) {
	die('You do not have the right type of authorization. You must be logged in and be able to edit pages and posts.');
}

do_action('admin_init');
*/

function bunyad_shortcode_popup()
{
	
	if (!is_user_logged_in() OR !current_user_can('edit_pages') OR !current_user_can('edit_posts')) {
		die('You do not have the right type of authorization. You must be logged in and be able to edit pages and posts.');
	}
	
	$shortcode   = array_merge(array('label' => null, 'child' => null), Bunyad_ShortCodes::getInstance()->get_one($_GET['shortcode']));
	$dialog_file = dirname(__FILE__) . '/shortcode-dialogs/' . $shortcode['dialog'] . '.php';
	
	if ($shortcode['dialog'] && file_exists($dialog_file)) {
		$shortcode_file = $dialog_file;
	}


?>
<!DOCTYPE html>
<html>
<head>
	
</head>

<body>

<style type="text/css">

.bunyad-sc-visual { margin-top: 10px; }
.bunyad-sc-visual .element-control { 
	position: relative;
	margin-bottom: 5px;
}

.bunyad-sc-visual .element-control:after {
	content: " "; 
	display: block; 
	clear: both;
}

.bunyad-sc-visual label {
	width: 150px;
	float: left;
	display: block;
	font-weight: bold;
	padding-right: 10px;
	line-height: 2em;
}

.bunyad-sc-visual .element-control input {
	float: left;
	display: block;
	width: 100%;
	max-width: 200px;
}

.bunyad-sc-visual textarea { 
	width: 400px;
	height: 150px;
}

.bunyad-sc-visual .buttons {
	padding-top: 20px;
}

.bunyad-sc-visual .help {
	line-height: 2.5em;
	padding-left: 10px;
	color: gray;
	font-size: 80%;
}

.element-control .color-picker-element {
	left: 375px; 
	top: 0;
}

.divider-or {
	position: relative;
	width: 100%;
	text-align: center;
	margin: 10px 0;
}

.divider-or span {
	background: #fff;
	padding: 0 5px;
}

.divider-or:before {
	display: block;
	position: absolute;
	top: 50%;
	content: " ";
	border-top: 1px solid #ccc;
	width: 100%;
	z-index: -1;
}

</style>

<script type="text/javascript">

	function Bunyad_Shortcodes_Helper($) {

		var self = this;
		this.form;
		this.shortcode;
		this.child_shortcode;
		
		this.init = function() {

			this.form = $('form.bunyad-sc-visual');
			
			// register simple shortcode handler by default
			this.form.submit(this.simple_shortcodes);
			$('#add-more-groups').click(this.insert_group);
		};

		/*
		 * Handler for simple shortcodes. Generator form will just have key => field pairs to use 
		 * as attributes in the shortcode.
		 *
		 * Creates shortcodes of form: [shortcode field1="value1" field2="value2"]content[/shortcode]
		 */
		this.simple_shortcodes = function(e) 
		{
			if (e.isPropagationStopped()) {
				return;
			}

			var params  = self.form_to_attribs($(this)),
				attribs = params[0].join(' '),
				enclose = params[1];
		

			var shortcode   = "<?php echo esc_attr($_GET['shortcode']); ?>";
			var insert_code = '[' + shortcode + (attribs ? ' ' + attribs : '') + ']' + enclose + '[/' + shortcode + ']';

			self.insert_close(insert_code);
			
			return false;
		};

		this.form_to_attribs = function(form, enclose) 
		{
			var params = form.serializeArray();
			var attribs = [], enclose = '';
	
			$.each(params, function(k, v) {
	
				// ignore the hidden field and empty values
				if (v.name === 'shortcode' || v.name.indexOf('[') !== -1 || $.trim(v.value) === '') {
					return;
				}
									
				if (v.name === 'enclose') {
					enclose = v.value.replace(/\r?\n/gi, '<br />'); // possibly multi-line 
				}
				else {
					attribs.push(v.name + '="' + v.value + '"');
				}
			});

			return [attribs, enclose];
		};
		
		this.advanced_shortcodes = function(e) {

			if (e.isPropagationStopped()) {
				return;
			}
			
			var group = [], parent = self.shortcode, shortcode = self.child_shortcode || parent;
			$(this).find('[name^="content["], [name^="sc-group["]').each(function() {

				var group_id = ($(this).attr('name')).replace(/.*\[([0-9]+)\]/, '$1'),
					attribs  = [],
					the_content,
					found_content = false;
				
				$(this).parent().parent().find('[name$="[' + group_id + ']"]').each(function() {
				
					var name = ($(this).attr('name')).replace(/(.*)\[([0-9]+)\]/, '$1');

					if (name == 'content') {
						found_content = true; 
						the_content = $(this).val();
						return;
					}
					else if (name == 'sc-group') {
						return;
					}

					if ($(this).val()) {
						attribs.push(name + '="' + $(this).val() + '"');
					}
				});
				
				// have title and content?
				if (!found_content || the_content) {
					group.push({attribs: attribs.join(' '), content: the_content || ''});
				}
			});

			var insert_code = '';

			// create one shortcode for each group
			$.each(group, function(k, v) 
			{
				var content = v.content.replace(/\r?\n/gi, '<br />'); // multi-line 
				insert_code += '[' + shortcode + (v.attribs ? ' ' + v.attribs : '') + ']' + (content ? content + '[/' + shortcode + "]" : '') + '<br />';  
			});

			// add wrapping parent tag if both shortcode (child) and parent available
			if (self.child_shortcode && parent) {

				var attribs = self.form_to_attribs($(this));
				attribs = attribs[0].join(' ');
				
				
				insert_code = '[' + parent + (attribs ? ' ' + attribs : '') + "]<br />" + insert_code + '[/' + parent + ']';
			}

			self.insert_close(insert_code);

			e.stopPropagation();
			return false;
		};

		this.insert_close = function(code) {
			// insert shortcode and remove popup
			tinyMCE.activeEditor.execCommand("mceInsertContent", false, code);
			tb_remove();
		};

		this.set_handler = function(handler) 
		{
			
			if (handler == 'advanced') {
				this.form.unbind('submit').submit(this.advanced_shortcodes);
			}
			else if ($.isFunction(handler)) {
				this.form.unbind('submit').submit(handler);
			}
		};

		this.insert_group = function(e) {

			// current tabs count
			var tabs_count = $(this).parent().data('bunyad_tabs') || 0;
			if (tabs_count === 0) {
				tabs_count = $(this).parent().parent().find('.title').length;
			}

			if (tabs_count >= 6) { 
				alert('Woah, slow down. Do you really wish to be adding that many tabs?');
			}
			
			tabs_count++;

			// get our template and modify it
			var html = $(this).parent().parent().find('.template-group-options').html();

			html = $(html);
			html.find('span').each(function() {
				var span_html = $(this).html();
				$(this).html(span_html.replace('%number%', tabs_count));
			});

			html.find('[name*="[%number%]"]').each(function() {
				var attr = $(this).attr('name');
				$(this).attr('name', attr.replace('%number%', tabs_count));
			});
			
			$(this).parent().before(html);

			// update counter
			$(this).parent().data('bunyad_tabs', tabs_count);

			if (Bunyad_Options && $.farbtastic) {
				$('.colorpicker').wpColorPicker();
				Bunyad_Options.init_color_pickers();
			}

			return false;	
		};

		$(function() {
			self.init();
		});
	}

	var Bunyad_Shortcodes_Helper = new Bunyad_Shortcodes_Helper(jQuery);

	// set shortcodes
	Bunyad_Shortcodes_Helper.shortcode = '<?php echo esc_attr(strip_tags($_GET['shortcode'])); ?>';
	Bunyad_Shortcodes_Helper.child_shortcode = '<?php echo esc_attr($shortcode['child']); ?>';

</script>

<form method="post" class="bunyad-sc-visual">
	<input type="hidden" name="shortcode" value="<?php echo esc_attr($_GET['shortcode']); ?>" />
	<?php
	if ($shortcode_file) {
		include_once $shortcode_file; 
	}
	?>

	<div class="buttons">
		<input type="submit" value="<?php _e('Insert ' . $shortcode['label'], 'bunyad-shortcodes'); ?>" class="button-primary" />
	</div>
	
</form>

<link rel="stylesheet" type="text/css" href="<?php echo admin_url() .'/css/farbtastic.css'; ?>" />
<script src="<?php echo admin_url() .'/js/farbtastic.js'; ?>"></script>
<script src="<?php echo get_template_directory_uri() .'/admin/js/options.js'; ?>"></script>
</body>
</html><?php

die;

} // function
?>