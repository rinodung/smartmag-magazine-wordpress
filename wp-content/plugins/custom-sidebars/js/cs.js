/*! Custom Sidebars Free - v2.1.02
 * http://premium.wpmudev.org/project/the-pop-over-plugin/
 * Copyright (c) 2015; * Licensed GPLv2+ */
/*global window:false */
/*global document:false */
/*global wp:false */
/*global wpmUi:false */
/*global csSidebars:false */
/*global csSidebarsData:false */

/**
 * Javascript support for the free version of the plugin.
 */
jQuery(function init_free() {
	var $doc = jQuery( document ),
		$all = jQuery( '#widgets-right' );

	/**
	 * Moves the "Clone" button next to the save button.
	 */
	var init_widget = function init_widget( ev, el ) {
		var $widget = jQuery( el ).closest( '.widget' ),
			$btns = $widget.find( '.csb-pro-layer' ),
			$target = $widget.find( '.widget-control-actions .widget-control-save' ),
			$spinner = $widget.find( '.widget-control-actions .spinner' );

		if ( $widget.data( '_csb_free' ) === true ) {
			return;
		}

		$spinner.insertBefore( $target ).css({ 'float': 'left' });
		$btns.insertBefore( $target );

		$widget.data( '_csb_free', true );
	};

	$all.find( '.widget' ).each( init_widget );
	$doc.on( 'widget-added', init_widget );
});

/*-----  End of free version scripts  ------*/

/*
 * http://blog.stevenlevithan.com/archives/faster-trim-javascript
 */
function trim( str ) {
	str = str.replace(/^\s\s*/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}

/**
 * CsSidebar class
 *
 * This adds new functionality to each sidebar.
 *
 * Note: Before the first CsSidebar object is created the class csSidebars below
 * must be initialized!
 */
function CsSidebar(id, type) {
	var editbar;

	/**
	 * Replace % to fix bug http://wordpress.org/support/topic/in-wp-35-sidebars-are-not-collapsable-anymore?replies=16#post-3990447
	 * We'll use this.id to select and the original id for html
	 *
	 * @since  1.2
	 */
	this.id = id.split('%').join('\\%');

	/**
	 * Either 'custom' or 'theme'
	 *
	 * @since  2.0
	 */
	this.type = type;

	this.sb = jQuery('#' + this.id);
	this.widgets = '';
	this.name = trim(this.sb.find('.sidebar-name h3').text());
	this.description = trim(this.sb.find('.sidebar-description').text());

	// Add one of two editbars to each sidebar.
	if ( type === 'custom' ) {
		editbar = window.csSidebars.extras.find('.cs-custom-sidebar').clone();
	} else {
		editbar = window.csSidebars.extras.find('.cs-theme-sidebar').clone();
	}

	this.sb.parent().append(editbar);

	// Customize the links and label-tags.
	editbar.find('label').each(function(){
		var me = jQuery( this );
		window.csSidebars.addIdToLabel( me, id );
	});
}

/**
 * Returns the sidebar ID.
 *
 * @since  2.0
 */
CsSidebar.prototype.getID = function() {
	return this.id.split('\\').join('');
};


/**
 * =============================================================================
 *
 *
 * csSidebars class
 *
 * This is the collection of all CsSidebar objects.
 */
window.csSidebars = null;

(function($){
	window.csSidebars = {
		/**
		 * List of all CsSidebar objects.
		 * @type array
		 */
		sidebars: [],

		/**
		 * This is the same prefix as defined in class-custom-sidebars.php
		 * @type string
		 */
		sidebar_prefix: 'cs-',

		/**
		 * Form for the edit-sidebar popup.
		 * @type jQuery object
		 */
		edit_form: null,

		/**
		 * Form for the delete-sidebar popup.
		 * @type jQuery object
		 */
		delete_form: null,

		/**
		 * Form for the export/import popup.
		 * @type jQuery object
		 */
		export_form: null,

		/**
		 * Form for the location popup.
		 * @type jQuery object
		 */
		location_form: null,

		/**
		 * Shortcut to '#widgets-right'
		 * @type jQuery object
		 */
		right: null,

		/**
		 * Shortcut to '#cs-widgets-extra'
		 * @type jQuery object
		 */
		extras: null,

		/**
		 * Stores the callback functions associated with the toolbar actions.
		 * @see  csSidebars.handleAction()
		 * @see  csSidebars.registerAction()
		 * @type Object
		 */
		action_handlers: {},


		/*====================================*\
		========================================
		==                                    ==
		==           INITIALIZATION           ==
		==                                    ==
		========================================
		\*====================================*/

		init: function(){
			if ( 'undefined' === typeof( csSidebarsData ) ) {
				// Inside theme customizer we load the JS but have no widget-data.
				return;
			}

			csSidebars
				.initControls()
				.initScrollbar()
				.initTopTools()
				.initSidebars()
				.initToolbars()
				.initColumns();
		},

		/**
		 * =====================================================================
		 * Initialize DOM and find jQuery objects
		 *
		 * @since  1.0.0
		 */
		initControls: function(){
			csSidebars.right = jQuery( '#widgets-right' );
			csSidebars.extras = jQuery( '#cs-widgets-extra' );

			if ( null === csSidebars.edit_form ) {
				csSidebars.edit_form = csSidebars.extras.find( '.cs-editor' ).clone();
				csSidebars.extras.find( '.cs-editor' ).remove();
			}

			if ( null === csSidebars.delete_form ) {
				csSidebars.delete_form = csSidebars.extras.find( '.cs-delete' ).clone();
				csSidebars.extras.find( '.cs-delete' ).remove();
			}

			if ( null === csSidebars.export_form ) {
				csSidebars.export_form = csSidebars.extras.find( '.cs-export' ).clone();
				csSidebars.extras.find( '.cs-export' ).remove();
			}

			if ( null === csSidebars.location_form ) {
				csSidebars.location_form = csSidebars.extras.find( '.cs-location' ).clone();
				csSidebars.extras.find( '.cs-location' ).remove();
			}

			jQuery('#cs-title-options')
				.detach()
				.prependTo( csSidebars.right );

			return csSidebars;
		},

		/**
		 * =====================================================================
		 * Initialize the custom scrollbars on the right side.
		 *
		 * @since  1.0.0
		 */
		initScrollbar: function(){
			var right_side = jQuery( '.widget-liquid-right' ),
				wnd = jQuery( window ),
				viewport = null;

			csSidebars.right
				.addClass('overview')
				.wrap('<div class="viewport" />');

			right_side
				.height( wnd.height() )
				.prepend('<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>')
				.tinyscrollbar();

			viewport = jQuery( '.viewport' )
				.height( wnd.height() - 32 );

			// Re-calculate the scrollbar size.
			var update_scrollbars = function update_scrollbars() {
				right_side.data( 'plugin_tinyscrollbar' ).update( 'relative' );
			};

			wnd.resize(function() {
				right_side.height( wnd.height() );
				viewport.height( wnd.height() - 32 );
				update_scrollbars();
			});

			right_side.click(function(){
				window.setTimeout( update_scrollbars, 400 );
			});
			wnd.on( 'cs-resize', update_scrollbars );

			right_side.hover(
				function() {
					right_side.find( '.scrollbar' ).fadeIn();
				}, function() {
					right_side.find( '.scrollbar' ).fadeOut();
				}
			);

			// Update the scrollbars after short delay
			window.setTimeout( update_scrollbars, 1000 );

			return csSidebars;
		},

		/**
		 * =====================================================================
		 * Arrange sidebars in left/right columns.
		 * Left column: Custom sidebars. Right column: Theme sidebars.
		 *
		 * @since  2.0
		 */
		initColumns: function() {
			var col1 = csSidebars.right.find( '.sidebars-column-1' ),
				col2 = csSidebars.right.find( '.sidebars-column-2' ),
				title = jQuery( '<div class="cs-title"><h3></h3></div>' ),
				sidebars = csSidebars.right.find( '.widgets-holder-wrap' );

			if ( ! col2.length ) {
				col2 = jQuery( '<div class="sidebars-column-2"></div>' );
				col2.appendTo( csSidebars.right );
			}

			title
				.find( 'h3' )
				.append( '<span class="cs-title-val"></span>' );

			title
				.clone()
				.prependTo( col1 )
				.find('.cs-title-val')
				.text( csSidebarsData.custom_sidebars );

			title
				.clone()
				.prependTo( col2 )
				.find( '.cs-title-val' )
				.text( csSidebarsData.theme_sidebars );

			col1 = jQuery( '<div class="inner"></div>' ).appendTo( col1 );
			col2 = jQuery( '<div class="inner"></div>' ).appendTo( col2 );

			sidebars.each(function check_sidebar() {
				var me = jQuery( this ),
					sbar = me.find( '.widgets-sortables' );

				if ( csSidebars.isCustomSidebar( sbar) ) {
					me.appendTo( col1 );
				} else {
					me.appendTo( col2 );
				}
			});
		},

		/**
		 * =====================================================================
		 * Initialization function, creates a CsSidebar object for each sidebar.
		 *
		 * @since  1.0.0
		 */
		initSidebars: function(){
			csSidebars.right.find('.widgets-sortables').each(function() {
				var key, sb,
					state = false,
					me = jQuery( this ),
					id = me.attr('id');

				if ( me.data( 'cs-init' ) === true ) { return; }
				me.data( 'cs-init', true );

				if ( csSidebars.isCustomSidebar( this ) ) {
					sb = csSidebars.add( id, 'custom' );
				} else {
					sb = csSidebars.add( id, 'theme' );

					// Set correct "replaceable" flag for the toolbar.
					for ( key in csSidebarsData.replaceable ) {
						if ( ! csSidebarsData.replaceable.hasOwnProperty( key ) ) {
							continue;
						}
						if ( csSidebarsData.replaceable[key] === id ) {
							state = true;
							break;
						}
					}

					csSidebars.setReplaceable( sb, state, false );
				}
			});
			return csSidebars;
		},

		/**
		 * =====================================================================
		 * Initialize the top toolbar, above the sidebar list.
		 *
		 * @since  1.0.0
		 */
		initTopTools: function() {
			var btn_create = jQuery( '.btn-create-sidebar' ),
				btn_export = jQuery( '.btn-export' ),
				data = {};

			// Button: Add new sidebar.
			btn_create.click(function() {
				data.id = '';
				data.title = csSidebarsData.title_new;
				data.button = csSidebarsData.btn_new;
				data.description = '';
				data.name = '';

				csSidebars.showEditor( data );
			});

			// Button: Export sidebars.
			btn_export.click( csSidebars.showExport );

			return csSidebars;
		},

		/**
		 * =====================================================================
		 * Hook up all the functions in the sidebar toolbar.
		 * Toolbar is in the bottom of each sidebar.
		 *
		 * @since  1.0.0
		 */
		initToolbars: function() {
			var tool_action = function( ev ) {
				var me = jQuery( ev.target ).closest( '.cs-tool' ),
					action = me.data( 'action' ),
					id = csSidebars.getIdFromEditbar( me ),
					sb = csSidebars.find( id );

				// Return value False means: Execute the default click handler.
				return ! csSidebars.handleAction( action, sb );
			};

			csSidebars.registerAction( 'edit', csSidebars.showEditor );
			csSidebars.registerAction( 'location', csSidebars.showLocations );
			csSidebars.registerAction( 'delete', csSidebars.showRemove );
			csSidebars.registerAction( 'replaceable', csSidebars.setReplaceable );

			csSidebars.right.on('click', '.cs-tool', tool_action);

			return csSidebars;
		},

		/**
		 * Triggers the callback function for the specified toolbar action.
		 *
		 * @since  2.0
		 */
		handleAction: function( action, sb ) {
			if ( 'function' === typeof csSidebars.action_handlers[ action ] ) {
				return !! csSidebars.action_handlers[ action ]( sb );
			}
			return false;
		},

		/**
		 * Registers a new callback function that is triggered when the
		 * associated toolbar icon is clicked.
		 *
		 * @since  2.0
		 */
		registerAction: function( action, callback ) {
			csSidebars.action_handlers[ action ] = callback;
		},

		/**
		 * Displays a error notification that something has gone wrong.
		 *
		 * @since  2.0
		 * @param  mixed details Ajax response string/object.
		 */
		showAjaxError: function( details ) {
			var msg = {};

			msg.message = csSidebarsData.ajax_error;
			msg.details = details;
			msg.parent = '#widgets-right';
			msg.insert_after = '#cs-title-options';
			msg.id = 'editor';
			msg.type = 'err';

			wpmUi.message( msg );
		},


		/*============================*\
		================================
		==                            ==
		==           EDITOR           ==
		==                            ==
		================================
		\*============================*/

		/**
		 * =====================================================================
		 * Show the editor for a custom sidebar as a popup window.
		 *
		 * @since  2.0
		 * @param  Object data Data describing the popup window.
		 *           - id .. ID of the sidebar (text).
		 *           - name .. Value of field "name".
		 *           - description .. Value of field "description".
		 *           - title .. Text for the window title.
		 *           - button .. Caption of the save button.
		 *
		 *           or a CsSidebar object.
		 */
		showEditor: function( data ) {
			var popup = null,
				ajax = null;

			if ( data instanceof CsSidebar ) {
				data = {
					id: data.getID(),
					title: csSidebarsData.title_edit.replace( '[Sidebar]', data.name ),
					button: csSidebarsData.btn_edit
				};
			}

			// Hide the "extra" fields
			var hide_extras = function hide_extras() {
				popup.$().removeClass( 'csb-has-more' );
				popup.size( null, 215 );
			};

			// Show the "extra" fields
			var show_extras = function show_extras() {
				popup.$().addClass( 'csb-has-more' );
				popup.size( null, 545 );
			};

			// Toggle the "extra" fields based on the checkbox state.
			var toggle_extras = function toggle_extras() {
				if ( jQuery( this ).prop( 'checked' ) ) {
					show_extras();
				} else {
					hide_extras();
				}
			};

			// Populates the input fields in the editor with given data.
			var set_values = function set_values( data, okay, xhr ) {
				popup.loading( false );

				// Ignore error responses from Ajax.
				if ( ! data ) {
					return false;
				}

				if ( ! okay ) {
					popup.close();
					csSidebars.showAjaxError( data );
					return false;
				}

				if ( undefined !== data.sidebar ) {
					data = data.sidebar;
				}

				// Populate known fields.
				if ( undefined !== data.id ) {
					popup.$().find( '#csb-id' ).val( data.id );
				}
				if ( undefined !== data.name ) {
					popup.$().find( '#csb-name' ).val( data.name );
				}
				if ( undefined !== data.description ) {
					popup.$().find( '#csb-description' ).val( data.description );
				}
				if ( undefined !== data.before_title ) {
					popup.$().find( '#csb-before-title' ).val( data.before_title );
				}
				if ( undefined !== data.after_title ) {
					popup.$().find( '#csb-after-title' ).val( data.after_title );
				}
				if ( undefined !== data.before_widget ) {
					popup.$().find( '#csb-before-widget' ).val( data.before_widget );
				}
				if ( undefined !== data.after_widget ) {
					popup.$().find( '#csb-after-widget' ).val( data.after_widget );
				}
				if ( undefined !== data.button ) {
					popup.$().find( '.btn-save' ).text( data.button );
				}
			};

			// Close popup after ajax request
			var handle_done_save = function handle_done_save( resp, okay, xhr ) {
				var msg = {}, sb;

				popup
					.loading( false )
					.close();

				msg.message = resp.message;
				// msg.details = resp;
				msg.parent = '#widgets-right';
				msg.insert_after = '#cs-title-options';
				msg.id = 'editor';

				if ( okay ) {
					if ( 'update' === resp.action ) {
						// Update the name/description of the sidebar.
						sb = csSidebars.find( resp.data.id );
						csSidebars.updateSidebar( sb, resp.data );
					} else if ( 'insert' === resp.action ) {
						// Insert a brand new sidebar container.
						csSidebars.insertSidebar( resp.data );
					}
				} else {
					msg.type = 'err';
				}
				wpmUi.message( msg );
			};

			// Submit the data via ajax.
			var save_data = function save_data() {
				var form = popup.$().find( 'form' );


				// Start loading-animation.
				popup.loading( true );

				ajax.reset()
					.data( form )
					.ondone( handle_done_save )
					.load_json();

				return false;
			};

			// Show the popup.
			popup = wpmUi.popup()
				.modal( true )
				.title( data.title )
				.onshow( hide_extras )
				.content( csSidebars.edit_form );

			hide_extras();
			set_values( data, true, null );

			// Create new ajax object to get sidebar details.
			ajax = wpmUi.ajax( null, 'cs-ajax' );
			if ( data.id ) {
				popup.loading( true );
				ajax.reset()
					.data({
						'do': 'get',
						'sb': data.id
					})
					.ondone( set_values )
					.load_json();
			}

			popup.show();
			popup.$().find( '#csb-name' ).focus();

			// Add event hooks to the editor.
			popup.$().on( 'click', '#csb-more', toggle_extras );
			popup.$().on( 'click', '.btn-save', save_data );
			popup.$().on( 'click', '.btn-cancel', popup.close );

			return true;
		},

		/**
		 * Update the name/description of an existing sidebar container.
		 *
		 * @since  1.0.0
		 */
		updateSidebar: function( sb, data ) {
			// Update the title.
			sb.sb
				.find( '.sidebar-name h3' )
				.text( data.name );

			// Update description.
			sb.sb
				.find( '.sidebar-description' )
				.html( '<p class="description"></p>' )
				.find( '.description' )
				.text( data.description );

			return csSidebars;
		},

		/**
		 * Insert a brand new sidebar container.
		 *
		 * @since  1.0.0
		 */
		insertSidebar: function( data ) {
			var box = jQuery( '<div class="widgets-holder-wrap"></div>' ),
				inner = jQuery( '<div class="widgets-sortables ui-sortable"></div>' ),
				name = jQuery( '<div class="sidebar-name"><div class="sidebar-name-arrow"><br></div><h3></h3></div>' ),
				desc = jQuery( '<div class="sidebar-description"></div>' ),
				col = csSidebars.right.find( '.sidebars-column-1 > .inner:first' );

			// Set sidebar specific values.
			inner.attr( 'id', data.id );

			name
				.find( 'h3' )
				.text( data.name );

			desc
				.html( '<p class="description"></p>' )
				.find( '.description' )
				.text( data.description );

			// Assemble the new sidebar box in correct order.
			name.appendTo( inner );
			desc.appendTo( inner );
			inner.appendTo( box );

			// Display the new sidebar on screen.
			box.prependTo( col );

			// Remove hooks added by wpWidgets.init()
			jQuery( '#widgets-right .sidebar-name' ).unbind( 'click' );
			jQuery( '#widgets-left .sidebar-name' ).unbind( 'click' );
			jQuery( document.body ).unbind('click.widgets-toggle');
			jQuery('.widgets-chooser')
				.off( 'click.widgets-chooser' )
				.off( 'keyup.widgets-chooser' );
			jQuery( '#available-widgets .widget .widget-title' ).off( 'click.widgets-chooser' );
			jQuery( '.widgets-chooser-sidebars' ).empty();

			// Re-Init the page using wpWidgets.init()
			window.wpWidgets.init();

			// Add the plugin toolbar to the new sidebar.
			csSidebars.initSidebars();

			return csSidebars;
		},


		/*============================*\
		================================
		==                            ==
		==           EXPORT           ==
		==                            ==
		================================
		\*============================*/

		/**
		 * Shows a popup window with the export/import form.
		 *
		 * @since  2.0
		 */
		showExport: function() {
			var popup = null;

			// Show the popup.
			popup = wpmUi.popup()
				.modal( true )
				.size( 740, 480 )
				.title( csSidebarsData.title_export )
				.content( csSidebars.export_form )
				.show();

			return true;
		},


		/*============================*\
		================================
		==                            ==
		==           REMOVE           ==
		==                            ==
		================================
		\*============================*/

		/**
		 * =====================================================================
		 * Ask for confirmation before deleting a sidebar
		 */
		showRemove: function( sb ) {
			var popup = null,
				ajax = null,
				id = sb.getID(),
				name = sb.name;

			// Insert the sidebar name into the delete message.
			var insert_name = function insert_name( el ) {
				el.find('.name').text( name );
			};

			// Closes the delete confirmation.
			var close_popup = function close_popup() {
				popup
					.loading( false )
					.close();
			};

			// Handle response of the delete ajax-call.
			var handle_done = function handle_done( resp, okay, xhr ) {
				var msg = {};

				popup
					.loading( false )
					.close();

				msg.message = resp.message;
				// msg.details = resp;
				msg.parent = '#widgets-right';
				msg.insert_after = '#cs-title-options';
				msg.id = 'editor';

				if ( okay ) {
					// Remove the Sidebar from the page.
					csSidebars.right
						.find('#' + id)
						.closest('.widgets-holder-wrap')
						.remove();

					// Remove object from internal collection.
					csSidebars.remove( id );
				} else {
					msg.type = 'err';
				}

				wpmUi.message( msg );
			};

			// Deletes the sidebar and closes the confirmation popup.
			var delete_sidebar = function delete_sidebar() {
				popup.loading( true );

				ajax.reset()
					.data({
						'do': 'delete',
						'sb': id
					})
					.ondone( handle_done )
					.load_json();
			};

			// Show the popup.
			popup = wpmUi.popup()
				.modal( true )
				.size( null, 160 )
				.title( csSidebarsData.title_delete )
				.content( csSidebars.delete_form )
				.onshow( insert_name )
				.show();

			// Create new ajax object.
			ajax = wpmUi.ajax( null, 'cs-ajax' );

			popup.$().on( 'click', '.btn-cancel', close_popup );
			popup.$().on( 'click', '.btn-delete', delete_sidebar );

			return true;
		},


		/*==============================*\
		==================================
		==                              ==
		==           LOCATION           ==
		==                              ==
		==================================
		\*==============================*/

		/**
		 * =====================================================================
		 * Show popup to assign sidebar to default categories.
		 *
		 * @since  2.0
		 */
		showLocations: function( sb ){
			var popup = null,
				ajax = null,
				form = null,
				id = sb.getID();

			// Display the location data after it was loaded by ajax.
			var handle_done_load = function handle_done_load( resp, okay, xhr ) {
				var theme_sb, opt, name, msg = {}; // Only used in error case.

				popup.loading( false );

				if ( ! okay ) {
					popup.close();
					csSidebars.showAjaxError( resp );
					return;
				}

				// Display the sidebar name.
				popup.$().find( '.sb-name' ).text( resp.sidebar.name );
				var sb_id = resp.sidebar.id;

				// Only show settings for replaceable sidebars
				var sidebars = popup.$().find( '.cs-replaceable' );
				sidebars.hide();
				resp.replaceable = wpmUi.obj( resp.replaceable );
				for ( var key0 in resp.replaceable ) {
					if ( ! resp.replaceable.hasOwnProperty( key0 ) ) {
						continue;
					}
					sidebars.filter( '.' + resp.replaceable[key0] ).show();
				}

				// Add a new option to the replacement list.
				var _add_option = function _add_option( item, lists, key ) {
					var opt = jQuery( '<option></option>' );
					opt.attr( 'value', key ).text( item.name );
					lists.append( opt );
				};

				// Check if the current sidebar is a replacement in the list.
				var _select_option = function _select_option( replacement, sidebar, key, lists ) {
					var row = lists
							.closest( '.cs-replaceable' )
							.filter('.' + sidebar),
						option = row
							.find( 'option[value="' + key + '"]' ),
						group = row.find( 'optgroup.used' ),
						check = row.find( '.detail-toggle' );

					if ( replacement === sb_id ) {
						option.prop( 'selected', true );
						if ( true !== check.prop( 'checked' ) ) {
							check.prop( 'checked', true );
							row.addClass( 'open' );

							// Upgrade the select list with chosen.
							wpmUi.upgrade_multiselect( row );
						}
					} else {
						if ( ! group.length ) {
							group = jQuery( '<optgroup class="used">' )
								.attr( 'label', row.data( 'lbl-used' ) )
								.appendTo( row.find( '.details select' ) );
						}
						option.detach().appendTo( group );
					}
				};

				// ----- Category ----------------------------------------------
				// Refresh list for single categories and category archives.
				var lst_cat = popup.$().find( '.cs-datalist.cs-cat' );
				var lst_act = popup.$().find( '.cs-datalist.cs-arc-cat' );
				var data_cat = resp.categories;
				lst_act.empty();
				lst_cat.empty();
				// Add the options
				for ( var key1 in data_cat ) {
					_add_option( data_cat[ key1 ], lst_act, key1 );
					_add_option( data_cat[ key1 ], lst_cat, key1 );
				}

				// Select options
				for ( var key2 in data_cat ) {
					if ( data_cat[ key2 ].single ) {
						for ( theme_sb in data_cat[ key2 ].single ) {
							_select_option(
								data_cat[ key2 ].single[ theme_sb ],
								theme_sb,
								key2,
								lst_cat
							);
						}
					}
					if ( data_cat[ key2 ].archive ) {
						for ( theme_sb in data_cat[ key2 ].archive ) {
							_select_option(
								data_cat[ key2 ].archive[ theme_sb ],
								theme_sb,
								key2,
								lst_act
							);
						}
					}
				}

				// ----- Post Type ---------------------------------------------
				// Refresh list for single posttypes.
				var lst_pst = popup.$().find( '.cs-datalist.cs-pt' );
				var data_pst = resp.posttypes;
				lst_pst.empty();
				// Add the options
				for ( var key3 in data_pst ) {
					opt = jQuery( '<option></option>' );
					name = data_pst[ key3 ].name;

					opt.attr( 'value', key3 ).text( name );
					lst_pst.append( opt );
				}

				// Select options
				for ( var key4 in data_pst ) {
					if ( data_pst[ key4 ].single ) {
						for ( theme_sb in data_pst[ key4 ].single ) {
							_select_option(
								data_pst[ key4 ].single[ theme_sb ],
								theme_sb,
								key4,
								lst_pst
							);
						}
					}
				}

				// ----- Archives ----------------------------------------------
				// Refresh list for archive types.
				var lst_arc = popup.$().find( '.cs-datalist.cs-arc' );
				var data_arc = resp.archives;
				lst_arc.empty();
				// Add the options
				for ( var key5 in data_arc ) {
					opt = jQuery( '<option></option>' );
					name = data_arc[ key5 ].name;

					opt.attr( 'value', key5 ).text( name );
					lst_arc.append( opt );
				}

				// Select options
				for ( var key6 in data_arc ) {
					if ( data_arc[ key6 ].archive ) {
						for ( theme_sb in data_arc[ key6 ].archive ) {
							_select_option(
								data_arc[ key6 ].archive[ theme_sb ],
								theme_sb,
								key6,
								lst_arc
							);
						}
					}
				}

			}; // end: handle_done_load()

			// User clicks on "replace <sidebar> for <category>" checkbox.
			var toggle_details = function toggle_details( ev ) {
				var inp = jQuery( this ),
					row = inp.closest( '.cs-replaceable' ),
					sel = row.find( 'select' );

				if ( inp.prop( 'checked' ) ) {
					row.addClass( 'open' );

					// Upgrade the select list with chosen.
					wpmUi.upgrade_multiselect( row );

					// Tell the select list to render the contents again.
					sel.trigger( 'change.select2' );
				} else {
					row.removeClass( 'open' );

					// Remove all selected options.
					sel.val( [] );
				}
			};

			// After saving data via ajax is done.
			var handle_done_save = function handle_done_save( resp, okay, xhr ) {
				var msg = {};

				popup
					.loading( false )
					.close();

				msg.message = resp.message;
				// msg.details = resp;
				msg.parent = '#widgets-right';
				msg.insert_after = '#cs-title-options';
				msg.id = 'editor';

				if ( ! okay ) {
					msg.type = 'err';
				}

				wpmUi.message( msg );
			};

			// Submit the data and close the popup.
			var save_data = function save_data() {
				popup.loading( true );

				ajax.reset()
					.data( form )
					.ondone( handle_done_save )
					.load_json();
			};

			// Show the popup.
			popup = wpmUi.popup()
				.modal( true )
				.size( null, 560 )
				.title( csSidebarsData.title_location )
				.content( csSidebars.location_form )
				.show();

			popup.loading( true );
			form = popup.$().find( '.frm-location' );
			form.find( '.sb-id' ).val( id );

			// Initialize ajax object.
			ajax = wpmUi.ajax( null, 'cs-ajax' );
			ajax.reset()
				.data({
					'do': 'get-location',
					'sb': id
				})
				.ondone( handle_done_load )
				.load_json();

			// Attach events.
			popup.$().on( 'click', '.detail-toggle', toggle_details );
			popup.$().on( 'click', '.btn-save', save_data );
			popup.$().on( 'click', '.btn-cancel', popup.close );

			return true;
		},

		/*======================================*\
		==========================================
		==                                      ==
		==           REPLACEABLE FLAG           ==
		==                                      ==
		==========================================
		\*======================================*/

		/**
		 * =====================================================================
		 * Change the replaceable flag
		 *
		 * @since  1.0.0
		 */
		setReplaceable: function( sb, state, do_ajax ) {
			var ajax,
				theme_sb = csSidebars.right.find( '.sidebars-column-2 .widgets-holder-wrap' ),
				the_bar = jQuery( sb.sb ).closest( '.widgets-holder-wrap' ),
				chk = the_bar.find( '.cs-toolbar .chk-replaceable' ),
				marker = the_bar.find( '.replace-marker' ),
				btn_replaceable = the_bar.find( '.cs-toolbar .btn-replaceable' );

			// After changing a sidebars "replaceable" flag.
			var handle_done_replaceable = function handle_done_replaceable( resp, okay, xhr ) {
				// Adjust the "replaceable" flag to match the data returned by the ajax request.
				if ( resp instanceof Object && typeof resp.replaceable === 'object' ) {
					csSidebarsData.replaceable = wpmUi.obj( resp.replaceable );

					theme_sb.find( '.widgets-sortables' ).each(function() {
						var _state = false,
							_me = jQuery( this ),
							_id = _me.attr( 'id' ),
							_sb = csSidebars.find( _id );

						for ( var key in csSidebarsData.replaceable ) {
							if ( ! csSidebarsData.replaceable.hasOwnProperty( key ) ) {
								continue;
							}
							if ( csSidebarsData.replaceable[key] === _id ) {
								_state = true;
								break;
							}
						}
						csSidebars.setReplaceable( _sb, _state, false );
					});
				}

				// Enable the checkboxes again after the ajax request is handled.
				theme_sb.find( '.cs-toolbar .chk-replaceable' ).prop( 'disabled', false );
				theme_sb.find( '.cs-toolbar .btn-replaceable' ).removeClass( 'wpmui-loading' );
			};

			if ( undefined === state ) { state = chk.prop( 'checked' ); }
			if ( undefined === do_ajax ) { do_ajax = true; }

			if ( chk.data( 'active' ) === state ) {
				return false;
			}
			chk.data( 'active', state );
			chk.prop( 'checked', state );

			if ( state ) {
				if ( ! marker.length ) {
					jQuery( '<div></div>' )
						.appendTo( the_bar )
						.attr( 'data-label', csSidebarsData.lbl_replaceable )
						.addClass( 'replace-marker' );
				}
				the_bar.addClass( 'replaceable' );
			} else {
				marker.remove();
				the_bar.removeClass( 'replaceable' );
			}

			if ( do_ajax ) {
				// Disable the checkbox until ajax request is done.
				theme_sb.find( '.cs-toolbar .chk-replaceable' ).prop( 'disabled', true );
				theme_sb.find( '.cs-toolbar .btn-replaceable' ).addClass( 'wpmui-loading' );

				ajax = wpmUi.ajax( null, 'cs-ajax' );
				ajax.reset()
					.data({
						'do': 'replaceable',
						'state': state,
						'sb': sb.getID()
					})
					.ondone( handle_done_replaceable )
					.load_json();
			}

			/**
			 * This function is called by csSidebars.handleAction. Return value
			 * False means that the default click event should be executed after
			 * this function was called.
			 */
			return false;
		},


		/*=============================*\
		=================================
		==                             ==
		==           HELPERS           ==
		==                             ==
		=================================
		\*=============================*/


		/**
		 * =====================================================================
		 * Find the specified CsSidebar object.
		 *
		 * @since  1.0.0
		 */
		find: function(id){
			return csSidebars.sidebars[id];
		},

		/**
		 * =====================================================================
		 * Create a new CsSidebar object.
		 *
		 * @since  1.0.0
		 */
		add: function(id, type){
			csSidebars.sidebars[id] = new CsSidebar(id, type);
			return csSidebars.sidebars[id];
		},

		/**
		 * =====================================================================
		 * Removes a new CsSidebar object.
		 *
		 * @since  2.0
		 */
		remove: function(id){
			delete csSidebars.sidebars[id];
		},

		/**
		 * =====================================================================
		 * Returns true when the specified ID is recognized as a sidebar
		 * that was created by the custom sidebars plugin.
		 *
		 * @since  2.0
		 */
		isCustomSidebar: function( el ) {
			var id = jQuery( el ).attr('id'),
				prefix = id.substr(0, csSidebars.sidebar_prefix.length);

			return prefix === csSidebars.sidebar_prefix;
		},

		/**
		 * =====================================================================
		 * Append the specified sidebar ID to the label and input element.
		 *
		 * @since  2.0
		 */
		addIdToLabel: function( $obj, id ){
			if ( true !== $obj.data( 'label-done' ) ) {
				var prefix = $obj.attr('for');
				$obj.attr( 'for', prefix + id );
				$obj.find( '.has-label' ).attr( 'id', prefix + id );
				$obj.data( 'label-done', true );
			}
		},

		/**
		 * =====================================================================
		 * Returns the sidebar ID based on the sidebar DOM object.
		 *
		 * @since  2.0
		 * @param  jQuery $obj Any DOM object inside the Sidebar HTML structure.
		 * @return string The sidebar ID
		 */
		getIdFromEditbar: function( $obj ){
			var wrapper = $obj.closest( '.widgets-holder-wrap' ),
				sb = wrapper.find( '.widgets-sortables:first' ),
				id = sb.attr( 'id' );
			return id;
		}
	};

	jQuery(function($){
		$('#csfooter').hide();
		if ( $('#widgets-right').length > 0 ) {
			csSidebars.init();
		}
		$('.defaultsContainer').hide();

		$( '#widgets-right .widgets-sortables' ).on( "sort", function(event, ui) {
			var topx = $('#widgets-right').top;
			ui.position.top = - $('#widgets-right').css('top');
		});
	});
})(jQuery);

/**
 * jQuery.fn.sortElements
 * --------------
 * @param Function comparator:
 *   Exactly the same behaviour as [1,2,3].sort(comparator)
 *
 * @param Function getSortable
 *   A function that should return the element that is
 *   to be sorted. The comparator will run on the
 *   current collection, but you may want the actual
 *   resulting sort to occur on a parent or another
 *   associated element.
 *
 *   E.g. $('td').sortElements(comparator, function(){
 *      return this.parentNode;
 *   })
 *
 *   The <td>'s parent (<tr>) will be sorted instead
 *   of the <td> itself.
 *
 * @see http://james.padolsey.com/javascript/sorting-elements-with-jquery/
 */
jQuery.fn.sortElements = (function(){

    var sort = [].sort;

    return function(comparator, getSortable) {

        getSortable = getSortable || function(){return this;};

        var placements = this.map(function(){

            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,

                // Since the element itself will change position, we have
                // to have some way of storing its original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );

            return function() {

                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }

                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);

            };

        });

        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });

    };

})();
