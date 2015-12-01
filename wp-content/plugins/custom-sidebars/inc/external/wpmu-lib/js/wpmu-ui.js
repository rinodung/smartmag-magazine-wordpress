/*! WPMU Dev code library - v1.0.17
 * http://premium.wpmudev.org/
 * Copyright (c) 2014; * Licensed GPLv2+ */
/*!
 * WPMU Dev UI library
 * (Philipp Stracker for WPMU Dev)
 *
 * This library provides a Javascript API via the global wpmUi object.
 *
 * @version  1.0.0
 * @author   Philipp Stracker for WPMU Dev
 * @link     http://appendto.com/2010/10/how-good-c-habits-can-encourage-bad-javascript-habits-part-1/
 * @requires jQuery
 */
/*global jQuery:false */
/*global window:false */
/*global document:false */
/*global XMLHttpRequest:false */

(function( wpmUi ) {

	/**
	 * The document element.
	 *
	 * @type   jQuery object
	 * @since  1.0.0
	 * @private
	 */
	var _doc = null;

	/**
	 * The html element.
	 *
	 * @type   jQuery object
	 * @since  1.0.0
	 * @private
	 */
	var _html = null;

	/**
	 * The body element.
	 *
	 * @type   jQuery object
	 * @since  1.0.0
	 * @private
	 */
	var _body = null;

	/**
	 * Modal overlay, created by this object.
	 *
	 * @type   jQuery object
	 * @since  1.0.0
	 * @private
	 */
	var _modal_overlay = null;


	// ==========
	// == Public UI functions ==================================================
	// ==========


	/**
	 * Opens a new popup layer.
	 *
	 * @since  1.0.0
	 * @return WpmUiWindow A new popup window.
	 */
	wpmUi.popup = function popup() {
		_init();
		return new WpmUiWindow();
	};

	/**
	 * Creates a new formdata object.
	 * With this object we can load or submit data via ajax.
	 *
	 * @since  1.0.0
	 * @param  string ajaxurl URL to the ajax handler.
	 * @param  string default_action The action to use when an ajax function
	 *                does not specify an action.
	 * @return WpmUiAjaxData A new formdata object.
	 */
	wpmUi.ajax = function ajax( ajaxurl, default_action ) {
		_init();
		return new WpmUiAjaxData( ajaxurl, default_action );
	};

	/**
	 * Upgrades normal multiselect fields to chosen-input fields.
	 *
	 * This function is a bottle-neck in Firefox -> el.chosen() takes quite long
	 *
	 * @since  1.0.0
	 * @param  jQuery|string base All children of this base element will be
	 *                checked. If empty then the body element is used.
	 */
	wpmUi.upgrade_multiselect = function upgrade_multiselect( base ) {
		_init();
		base = jQuery( base || _body );

		var items = base.find( 'select[multiple]' ),
			ajax_items = base.find( 'input[data-select-ajax]' );

		// When an DOM container is *cloned* it may contain markup for a select2
		// listbox that is not attached to any event handler. Clean this up.
		var clean_ghosts = function clean_ghosts( el ) {
			var id = el.attr( 'id' ),
				s2id = '#s2id_' + id,
				ghosts = el.parent().find( s2id );

			ghosts.remove();
		};

		// Initialize normal select or multiselect list.
		var upgrade_item = function upgrade_item() {
			var el = jQuery( this ),
				options = {
					'closeOnSelect': false,
					'width': '100%'
				};

			if ( el.data( 'wpmui-select' ) === '1' ) { return; }
			el.data( 'wpmui-select', '1' );
			clean_ghosts( el );

			// Prevent lags during page load by making this asynchronous.
			window.setTimeout( function() {
				el.select2(options);
			}, 1);
		};

		// Initialize select list with ajax source.
		var upgrade_ajax = function upgrade_ajax() {
			var format_item = function format_item( item ) {
				return item.val;
			};

			var get_id = function get_id( item ) {
				return item.key;
			};

			var init_selection = function init_selection( me, callback ) {
				var vals = me.val(),
					data = [],
					plain = [];

				jQuery( vals.split(',') ).each(function () {
					var item = this.split('::');
					plain.push( item[0] );
					data.push( { key: item[0], val: item[1] } );
				});

				me.val( plain.join(',') );
				callback( data );
			};

			var el = jQuery( this ),
				options = {
					'closeOnSelect': false,
					'width': '100%',
					'multiple': true,
					'minimumInputLength': 1,
					'ajax': {
						url: el.attr( 'data-select-ajax' ),
						dataType: 'json',
						quietMillis: 100,
						cache: true,
						data: function(term, page) {
							return {
								q: term,
							};
						},
						results: function(data, page) {
							return {
								results: data.items
							};
						}
					},
					'id': get_id,
					'formatResult': format_item,
					'formatSelection': format_item,
					'initSelection': init_selection
				};

			if ( el.data( 'wpmui-select' ) === '1' ) { return; }
			el.data( 'wpmui-select', '1' );
			clean_ghosts( el );

			// Prevent lags during page load by making this asynchronous.
			window.setTimeout( function() {
				el.select2(options);
			}, 1);
		};

		if ( 'function' === typeof jQuery.fn.each2 ) {
			items.each2( upgrade_item );
			ajax_items.each2( upgrade_ajax );
		} else {
			items.each( upgrade_item );
			ajax_items.each( upgrade_ajax );
		}

	};

	/**
	 * Displays a WordPress-like message to the user.
	 *
	 * @since  1.0.0
	 * @param  string|object args Message options object or message-text.
	 *             args: {
	 *               'message': '...'
	 *               'type': 'ok|err'  // Style
	 *               'close': true     // Show close button?
	 *               'parent': '.wrap' // Element that displays the message
	 *               'insert_after': 'h2' // Inside the parent the message
	 *                                    // will be displayed after the
	 *                                    // first element of this type.
	 *                                    // Set to false to insert at top.
	 *                'id': 'msg-ok'   // When set to a string value then the
	 *                                 // the first call to "message()" will
	 *                                 // insert a new message and the next
	 *                                 // call will update the existing element.
	 *                'class': 'msg1'  // Additional CSS class.
	 *                'details': obj   // Details for error-type message.
	 *             }
	 */
	wpmUi.message = function message( args ) {
		var parent, msg_box, btn_close, need_insert, debug;
		_init();

		// Hides the message again, e.g. when user clicks the close icon.
		var hide_message = function hide_message( ev ) {
			ev.preventDefault();
			msg_box.remove();
			return false;
		};

		// Toggle the error-details
		var toggle_debug = function toggle_debug( ev ) {
			var me = jQuery( this ).closest( '.wpmui-msg' );
			me.find( '.debug' ).toggle();
		};

		if ( 'undefined' === typeof args ) { return false; }

		if ( 'string' === typeof args || args instanceof Array ) {
			args = { 'message': args };
		}

		if ( args['message'] instanceof Array ) {
			args['message'] = args['message'].join( '<br />' );
		}

		if ( ! args['message'] ) { return false; }

		args['type'] = undefined === args['type'] ? 'ok' : args['type'].toString().toLowerCase();
		args['close'] = undefined === args['close'] ? true : args['close'];
		args['parent'] = undefined === args['parent'] ? '.wrap' : args['parent'];
		args['insert_after'] = undefined === args['insert_after'] ? 'h2' : args['insert_after'];
		args['id'] = undefined === args['id'] ? '' : args['id'].toString().toLowerCase();
		args['class'] = undefined === args['class'] ? '' : args['class'].toString().toLowerCase();
		args['details'] = undefined === args['details'] ? false : args['details'];

		if ( args['type'] === 'error' || args['type'] === 'red' ) { args['type'] = 'err'; }
		if ( args['type'] === 'success' || args['type'] === 'green' ) { args['type'] = 'ok'; }

		parent = jQuery( args['parent'] ).first();
		if ( ! parent.length ) { return false; }

		if ( args['id'] && jQuery( '.wpmui-msg[data-id="' + args['id'] + '"]' ).length ) {
			msg_box = jQuery( '.wpmui-msg[data-id="' + args['id'] + '"]' ).first();
			need_insert = false;
		} else {
			msg_box = jQuery( '<div><p></p></div>' );
			if ( args['id'] ) { msg_box.attr( 'data-id', args['id'] ); }
			need_insert = true;
		}
		msg_box.find( 'p' ).html( args['message'] );

		if ( args['type'] === 'err' && args['details'] && window.JSON ) {
			jQuery( '<div class="debug" style="display:none"></div>' )
				.appendTo( msg_box )
				.text( JSON.stringify( args['details'] ) );
			jQuery( '<i class="dashicons dashicons-editor-help light"></i>' )
				.prependTo( msg_box.find( 'p:first' ) )
				.click( toggle_debug )
				.after( ' ' );
		}

		msg_box.removeClass().addClass( 'updated wpmui-msg ' + args['class'] );
		if ( 'err' === args['type'] ) {
			msg_box.addClass( 'error' );
		}

		if ( need_insert ) {
			if ( args['close'] ) {
				btn_close = jQuery( '<a href="#" class="wpmui-close">&times;</a>' );
				btn_close.prependTo( msg_box );

				btn_close.click( hide_message );
			}

			if ( args['insert_after'] && parent.find( args['insert_after'] ).length ) {
				parent = parent.find( args['insert_after'] ).first();
				parent.after( msg_box );
			} else {
				parent.prepend( msg_box );
			}
		}

		return true;
	};

	/**
	 * Displays confirmation box to the user.
	 *
	 * The layer is displayed in the upper half of the parent element and is by
	 * default modal.
	 * Note that the confirmation is asynchronous and the functions return value
	 * only indicates if the confirmation message was created, and not the users
	 * response!
	 *
	 * Also this is a "disponsable" function which does not create DOM elements
	 * that can be re-used. All elements are temporary and are removed when the
	 * confirmation is closed. Only 1 confirmation should be displayed at a time.
	 *
	 * @since  1.0.14
	 * @param  object args {
	 *     Confirmation options.
	 *
	 *     string message
	 *     bool modal
	 *     string layout 'fixed' or 'absolute'
	 *     jQuery parent A jQuery object or selector
	 *     array buttons Default is ['OK']
	 *     function(key) callback Receives array-index of the pressed button
	 * }
	 * @return bool True if the confirmation is created correctly.
	 */
	wpmUi.confirm = function confirm( args ) {
		var parent, modal, container, el_msg, el_btn, ind, item, primary_button;

		if ( ! args instanceof Object ) { return false; }
		if ( undefined === args['message'] ) { return false; }

		args['modal'] = undefined === args['modal'] ? true : args['modal'];
		args['layout'] = undefined === args['layout'] ? 'fixed' : args['layout'];
		args['parent'] = undefined === args['parent'] ? _body : args['parent'];
		args['buttons'] = undefined === args['buttons'] ? ['OK'] : args['buttons'];
		args['callback'] = undefined === args['callback'] ? false : args['callback'];

		parent = jQuery( args['parent'] );

		function handle_close() {
			var me = jQuery( this ),
				key = parseInt( me.data( 'key' ) );

			modal.remove();
			container.remove();

			if ( 'function' === typeof args['callback'] ) {
				args['callback']( key );
			}
		}

		if ( args['modal'] ) {
			modal = jQuery( '<div class="wmui-confirm-modal"></div>' )
				.css( { 'position': args['layout'] } )
				.appendTo( parent );
		}

		container = jQuery( '<div class="wpmui-confirm-box"></div>' )
			.css( { 'position': args['layout'] } )
			.appendTo( parent );

		el_msg = jQuery( '<div class="wpmui-confirm-msg"></div>' )
			.html( args['message'] );

		el_btn = jQuery( '<div class="wpmui-confirm-btn"></div>' );
		primary_button = true;
		for ( ind = 0; ind < args['buttons'].length; ind += 1 ) {
			item = jQuery( '<button></button>' )
				.html( args['buttons'][ind] )
				.addClass( primary_button ? 'button-primary' : 'button-secondary' )
				.data( 'key', ind )
				.click( handle_close )
				.prependTo( el_btn );
			primary_button = false;
		}

		el_msg.appendTo( container );
		el_btn.appendTo( container );

		return true;
	};

	/**
	 * Attaches a tooltip to the specified element.
	 *
	 * @since  1.0.0
	 * @param jQuery el The host element that receives the tooltip.
	 * @param object|string args The tooltip options. Either a string containing
	 *                the toolip message (HTML code) or an object with details:
	 *                - content
	 *                - trigger [hover|click]
	 *                - pos [top|bottom|left|right]
	 *                - class
	 */
	wpmUi.tooltip = function tooltip( el, args ) {
		var tip, parent;
		_init();

		// Positions the tooltip according to the function args.
		var position_tip = function position_tip( tip ) {
			var tip_width = tip.outerWidth(),
				tip_height = tip.outerHeight(),
				tip_padding = 5,
				el_width = el.outerWidth(),
				el_height = el.outerHeight(),
				pos = {};

			pos['left'] = (el_width - tip_width) / 2;
			pos['top'] = (el_height - tip_height) / 2;
			pos[ args['pos'] ] = 'auto';

			switch ( args['pos'] ) {
				case 'top':    pos['bottom'] = el_height + tip_padding; break;
				case 'bottom': pos['top'] = el_height + tip_padding; break;
				case 'left':   pos['right'] = el_width + tip_padding; break;
				case 'right':  pos['left'] = el_width + tip_padding; break;
			}
			tip.css(pos);
		};

		// Make the tooltip visible.
		var show_tip = function show_tip( ev ) {
			var tip = jQuery( this )
				.closest( '.wpmui-tip-box' )
				.find( '.wpmui-tip' );

			tip.addClass( 'wpmui-visible' );
			tip.show();
			position_tip( tip );
			window.setTimeout( function() { position_tip( tip ); }, 35 );
		};

		// Hide the tooltip.
		var hide_tip = function hide_tip( ev ) {
			var tip = jQuery( this )
				.closest( '.wpmui-tip-box' )
				.find( '.wpmui-tip' );

			tip.removeClass( 'wpmui-visible' );
			tip.hide();
		};

		// Toggle the tooltip state.
		var toggle_tip = function toggle_tip( ev ) {
			if ( tip.hasClass( 'wpmui-visible' ) ) {
				hide_tip.call(this, ev);
			} else {
				show_tip.call(this, ev);
			}
		};

		if ( 'string' === typeof args ) {
			args = { 'content': args };
		}
		if ( undefined === args['content'] ) {
			return false;
		}
		el = jQuery( el );
		if ( ! el.length ) {
			return false;
		}

		args['trigger'] = undefined === args['trigger'] ? 'hover' : args['trigger'].toString().toLowerCase();
		args['pos'] = undefined === args['pos'] ? 'top' : args['pos'].toString().toLowerCase();
		args['class'] = undefined === args['class'] ? '' : args['class'].toString().toLowerCase();

		parent = el.parent();
		if ( ! parent.hasClass( 'wpmui-tip-box' ) ) {
			parent = el
				.wrap( '<span class="wpmui-tip-box"></span>' )
				.parent()
				.addClass( args['class'] + '-box' );
		}

		tip = parent.find( '> .wpmui-tip' );
		el.off();

		if ( ! tip.length ) {
			tip = jQuery( '<div class="wpmui-tip"></div>' );
			tip
				.addClass( args['class'] )
				.addClass( args['pos'] )
				.appendTo( el.parent() )
				.hide();

			if ( ! isNaN( args['width'] ) ) {
				tip.width( args['width'] );
			}
		}

		if ( 'hover' === args['trigger'] ) {
			el.on( 'mouseenter', show_tip ).on( 'mouseleave', hide_tip );
		} else if ( 'click' === args['trigger'] ) {
			el.on( 'click', toggle_tip );
		}

		tip.html( args['content'] );

		return true;
	};

	/**
	 * Checks the DOM and creates tooltips for the DOM Elements that specify
	 * tooltip details.
	 *
	 * Function can be called repeatedly and will refresh the tooltip contents
	 * if they changed since last call.
	 *
	 * @since  1.0.8
	 */
	wpmUi.upgrade_tooltips = function upgrade_tooltips() {
		var el = jQuery( '[data-tooltip]' );

		el.each(function() {
			var me = jQuery( this ),
				args = {
					'content': me.attr( 'data-tooltip' ),
					'pos': me.attr( 'data-pos' ),
					'trigger': me.attr( 'data-trigger' ),
					'class': me.attr( 'data-class' ),
					'width': me.attr( 'data-width' )
				};

			wpmUi.tooltip( me, args );
		});
	};

	/*
	 * Converts any value to an object.
	 * Typically used to convert an array to an object.
	 *
	 * @since  1.0.6
	 * @param  mixed value This value is converted to an JS-object.
	 * @return object
	 */
	wpmUi.obj = function( value ) {
		var obj = {};

		if ( value instanceof Object ) {
			obj = value;
		}
		else if ( value instanceof Array ) {
			if ( typeof value.reduce === 'function' ) {
				obj = value.reduce(function(o, v, i) {
					o[i] = v;
					return o;
				}, {});
			} else {
				for ( var i = value.length - 1; i > 0; i -= 1 ) {
					if ( value[i] !== undefined ) {
						obj[i] = value[i];
					}
				}
			}
		}
		else if ( typeof value === 'string' ) {
			obj.scalar = value;
		}
		else if ( typeof value === 'number' ) {
			obj.scalar = value;
		}
		else if ( typeof value === 'boolean' ) {
			obj.scalar = value;
		}

		return obj;
	};


	// ==========
	// == Private helper functions =============================================
	// ==========


	/**
	 * Initialize the object
	 *
	 * @since  1.0.0
	 * @private
	 */
	function _init() {
		if ( null !== _html ) { return; }

		_doc = jQuery( document );
		_html = jQuery( 'html' );
		_body = jQuery( 'body' );

		_init_boxes();
		_init_tabs();

		if ( ! _body.hasClass( 'no-auto-init' ) ) {
			wpmUi.upgrade_multiselect();
			wpmUi.upgrade_tooltips();
		}

		wpmUi.binary = new WpmUiBinary();
	}

	/**
	 * Shows a modal background layer
	 *
	 * @since  1.0.0
	 * @private
	 */
	function _make_modal() {
		if ( null === _modal_overlay ) {
			_modal_overlay = jQuery( '<div></div>' )
				.addClass( 'wpmui-overlay' )
				.appendTo( _body );
		}
		_body.addClass( 'wpmui-has-overlay' );
		_html.addClass( 'wpmui-no-scroll' );
	}

	/**
	 * Closes the modal background layer again.
	 *
	 * @since  1.0.0
	 * @private
	 */
	function _close_modal() {
		_body.removeClass( 'wpmui-has-overlay' );
		_html.removeClass( 'wpmui-no-scroll' );
	}

	/**
	 * Initialize the WordPress-ish accordeon boxes:
	 * Open or close boxes when user clicks the toggle icon.
	 *
	 * @since  1.0.0
	 */
	function _init_boxes() {
		// Toggle the box state (open/closed)
		var toggle_box = function toggle_box( ev ) {
			var box = jQuery( this ).closest( '.wpmui-box' );
			ev.preventDefault();

			// Don't toggle the box if it is static.
			if ( box.hasClass( 'static' ) ) { return false; }

			box.toggleClass( 'closed' );
			return false;
		};

		_body.on( 'click', '.wpmui-box > h3', toggle_box );
		_body.on( 'click', '.wpmui-box > h3 > .toggle', toggle_box );
	}

	/**
	 * Initialize the WordPress-ish tab navigation:
	 * Change the tab on click.
	 *
	 * @since  1.0.0
	 */
	function _init_tabs() {
		// Toggle the box state (open/closed)
		var activate_tab = function activate_tab( ev ) {
			var tab = jQuery( this ),
				all_tabs = tab.closest( '.wpmui-tabs' ),
				content = all_tabs.next( '.wpmui-tab-contents' ),
				active = all_tabs.find( '.active.tab' ),
				sel_tab = tab.attr( 'href' ),
				sel_active = active.attr( 'href' ),
				content_tab = content.find( sel_tab ),
				content_active = content.find( sel_active );

			// Close previous tab.
			if ( ! tab.hasClass( 'active' ) ) {
				active.removeClass( 'active' );
				content_active.removeClass( 'active' );
			}

			// Open selected tab.
			tab.addClass( 'active' );
			content_tab.addClass( 'active' );

			ev.preventDefault();
			return false;
		};

		_body.on( 'click', '.wpmui-tabs .tab', activate_tab );
	}

	// Initialize the object.
	jQuery(function() {
		_init();
	});









	/*============================*\
	================================
	==                            ==
	==           WINDOW           ==
	==                            ==
	================================
	\*============================*/



	/**
	 * Popup window.
	 *
	 * @type   WpmUiWindow
	 * @since  1.0.0
	 */
	var WpmUiWindow = function() {

		/**
		 * Backreference to the WpmUiWindow object.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _me = this;


		/**
		 * Stores the state of the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _visible = false;

		/**
		 * Defines if a modal background should be visible.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _modal = false;

		/**
		 * Size of the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _width = 740;

		/**
		 * Size of the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _height = 400;

		/**
		 * Title of the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _title = 'Window';

		/**
		 * Content of the window. Either a jQuery selector/object or HTML code.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _content = '';

		/**
		 * Class names to add to the popup window
		 *
		 * @since  1.0.14
		 * @private
		 */
		var _classes = '';

		/**
		 * Is set to true when new content is assigned to the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _content_changed = false;

		/**
		 * Flag is set to true when the window size was changed.
		 * After the window was updated we will additionally check if it is
		 * visible in the current viewport.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _need_check_size = false;


		/**
		 * Called after the window is made visible.
		 *
		 * @type  Callback function.
		 * @since  1.0.0
		 * @private
		 */
		var _onshow = null;

		/**
		 * Called after the window was hidden.
		 *
		 * @type  Callback function.
		 * @since  1.0.0
		 * @private
		 */
		var _onhide = null;

		/**
		 * Called after the window was hidden + destroyed.
		 *
		 * @type  Callback function.
		 * @since  1.0.0
		 * @private
		 */
		var _onclose = null;


		/**
		 * The popup window element.
		 *
		 * @type  jQuery object.
		 * @since  1.0.0
		 * @private
		 */
		var _wnd = null;

		/**
		 * Title bar inside the window.
		 *
		 * @type  jQuery object.
		 * @since  1.0.0
		 * @private
		 */
		var _el_title = null;

		/**
		 * Close button inside the title bar.
		 *
		 * @type  jQuery object.
		 * @since  1.0.0
		 * @private
		 */
		var _btn_close = null;

		/**
		 * Content section of the window.
		 *
		 * @type  jQuery object.
		 * @since  1.0.0
		 * @private
		 */
		var _el_content = null;

		/**
		 * Window status: visible, hidden, closing
		 *
		 * @type   string
		 * @since  1.0.14
		 * @private
		 */
		var _status = 'hidden';


		// ==============================
		// == Public functions ==========


		/**
		 * Sets the modal property.
		 *
		 * @since  1.0.0
		 */
		this.modal = function modal( state ) {
			_modal = ( state ? true : false );

			_update_window();
			return _me;
		};

		/**
		 * Sets the window size.
		 *
		 * @since  1.0.0
		 */
		this.size = function size( width, height ) {
			var new_width = Math.abs( parseFloat( width ) ),
				new_height = Math.abs( parseFloat( height ) );

			if ( ! isNaN( new_width ) ) { _width = new_width; }
			if ( ! isNaN( new_height ) ) { _height = new_height; }

			_need_check_size = true;
			_update_window();
			return _me;
		};

		/**
		 * Sets the window title.
		 *
		 * @since  1.0.0
		 */
		this.title = function title( new_title ) {
			_title = new_title;

			_update_window();
			return _me;
		};

		/**
		 * Sets the window content.
		 *
		 * @since  1.0.0
		 */
		this.content = function content( data ) {
			_content = data;
			_need_check_size = true;
			_content_changed = true;

			_update_window();
			return _me;
		};

		/**
		 * Sets optional classes for the main window element.
		 *
		 * @since  1.0.14
		 */
		this.set_class = function set_class( class_names ) {
			_classes = class_names;
			_content_changed = true;

			_update_window();
			return _me;
		};

		/**
		 * Define a callback that is executed after popup is made visible.
		 *
		 * @since  1.0.0
		 */
		this.onshow = function onshow( callback ) {
			_onshow = callback;
			return _me;
		};

		/**
		 * Define a callback that is executed after popup is hidden.
		 *
		 * @since  1.0.0
		 */
		this.onhide = function onhide( callback ) {
			_onhide = callback;
			return _me;
		};

		/**
		 * Define a callback that is executed after popup was destroyed.
		 *
		 * @since  1.0.0
		 */
		this.onclose = function onclose( callback ) {
			_onclose = callback;
			return _me;
		};

		/**
		 * Add a loading-overlay to the popup or remove the overlay again.
		 *
		 * @since  1.0.0
		 * @param  bool state True will add the overlay, false removes it.
		 */
		this.loading = function loading( state ) {
			if ( state ) {
				_wnd.addClass( 'wpmui-loading' );
			} else {
				_wnd.removeClass( 'wpmui-loading' );
			}
			return _me;
		};

		/**
		 * Shows a confirmation box inside the popup
		 *
		 * @since  1.0.14
		 * @param  object args Message options
		 */
		this.confirm = function confirm( args ) {
			if ( _status !== 'visible' ) { return _me; }
			if ( ! args instanceof Object ) { return _me; }

			args['layout'] = 'absolute';
			args['parent'] = _wnd;

			wpmUi.confirm( args );

			return _me;
		};

		/**
		 * Show the popup window.
		 *
		 * @since  1.0.0
		 */
		this.show = function show() {
			_visible = true;
			_need_check_size = true;
			_status = 'visible';

			_update_window();

			if ( typeof _onshow === 'function' ) {
				_onshow.apply( _me, [ _me.$() ] );
			}
			return _me;
		};

		/**
		 * Hide the popup window.
		 *
		 * @since  1.0.0
		 */
		this.hide = function hide() {
			_visible = false;
			_status = 'hidden';

			_update_window();

			if ( typeof _onhide === 'function' ) {
				_onhide.apply( _me, [ _me.$() ] );
			}
			return _me;
		};

		/**
		 * Completely removes the popup window.
		 *
		 * @since  1.0.0
		 */
		this.close = function close() {
			// Prevent infinite loop when calling .close inside onclose handler.
			if ( _status === 'closing' ) { return; }

			_me.hide();

			_status = 'closing';

			if ( typeof _onclose === 'function' ) {
				_onclose.apply( _me, [ _me.$() ] );
			}

			_unhook();
			_wnd.remove();
			_wnd = null;
		};

		/**
		 * Returns the jQuery object of the window
		 *
		 * @since  1.0.0
		 */
		this.$ = function $() {
			return _wnd;
		};


		// ==============================
		// == Private functions =========


		/**
		 * Create the DOM elements for the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _init() {
			// Create the DOM elements.
			_wnd = jQuery( '<div class="wpmui-wnd"></div>' );
			_el_title = jQuery( '<div class="wpmui-wnd-title"><span class="the-title"></span></div>' );
			_btn_close = jQuery( '<a href="#" class="wpmui-wnd-close"><i class="dashicons dashicons-no-alt"></i></a>' );
			_el_content = jQuery( '<div class="wpmui-wnd-content"></div>' );

			// Attach the window to the current page.
			_el_title.appendTo( _wnd );
			_el_content.appendTo( _wnd );
			_btn_close.appendTo( _el_title );
			_wnd.appendTo( _body ).hide();

			// Add event handlers.
			_hook();

			// Refresh the window layout.
			_visible = false;
			_update_window();
		}

		/**
		 * Add event listeners.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _hook() {
			if ( _wnd ) {
				_wnd.on( 'click', '.wpmui-wnd-close', _me.close );
				_wnd.on( 'click', 'thead .check-column :checkbox', _toggle_checkboxes );
				_wnd.on( 'click', 'tfoot .check-column :checkbox', _toggle_checkboxes );
				_wnd.on( 'click', 'tbody .check-column :checkbox', _check_checkboxes );
				jQuery( window ).on( 'resize', _check_size );
			}
		}

		/**
		 * Remove all event listeners.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _unhook() {
			if ( _wnd ) {
				_wnd.off( 'click', '.wpmui-wnd-close', _me.close );
				_wnd.off( 'click', '.check-column :checkbox', _toggle_checkboxes );
				jQuery( window ).off( 'resize', _check_size );
			}
		}

		/**
		 * Updates the size and position of the window.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _update_window( width, height ) {
			if ( ! _wnd ) { return false; }

			width = width || _width;
			height = height || _height;

			var styles = {
				'width': width,
				'height': height,
				'margin-left': -1 * (width / 2),
				'margin-top': -1 * (height / 2)
			};

			// Window title.
			_el_title.find( '.the-title' ).text( _title );

			// Display a copy of the specified content.
			if ( _content_changed ) {
				// Remove the current button bar.
				_wnd.find( '.buttons' ).remove();
				_wnd.removeClass();
				_wnd.addClass( 'wpmui-wnd no-buttons' );

				// Update the content.
				if ( _content instanceof jQuery ) {
					_el_content.html( _content.html() );
				} else {
					_el_content.html( jQuery( _content ).html() );
				}

				// Move the buttons out of the content area.
				var buttons = _el_content.find( '.buttons' );
				if ( buttons.length ) {
					buttons.appendTo( _wnd );
					_wnd.removeClass( 'no-buttons' );
				}

				// Add custom class to the popup.
				_wnd.addClass( _classes );

				_content_changed = false;
			}

			// Size and position.
			if ( _wnd.is( ':visible' ) ) {
				_wnd.animate(styles, 200);
			} else {
				_wnd.css(styles);
			}

			if ( _modal_overlay instanceof jQuery ) {
				_modal_overlay.off( 'click', _modal_close );
			}

			// Show or hide the window and modal background.
			if ( _visible ) {
				_wnd.show();
				if ( _modal ) { _make_modal(); }
				_modal_overlay.on( 'click', _modal_close );

				if ( _need_check_size ) {
					_need_check_size = false;
					_check_size();
				}
			} else {
				_wnd.hide();
				_close_modal();
			}
		}

		/**
		 * Closes the window when user clicks on the modal overlay
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _modal_close() {
			if ( ! _wnd ) { return false; }
			if ( ! _modal_overlay instanceof jQuery ) { return false; }

			_modal_overlay.off( 'click', _modal_close );
			_me.close();
		}


		/**
		 * Makes sure that the popup window is not bigger than the viewport.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _check_size() {
			if ( ! _wnd ) { return false; }

			var me = jQuery( this ), // this is jQuery( window )
				window_width = me.innerWidth(),
				window_height = me.innerHeight(),
				real_width = _width,
				real_height = _height;

			if ( window_width < _width ) {
				real_width = window_width;
			}
			if ( window_height < _height ) {
				real_height = window_height;
			}
			_update_window( real_width, real_height );
		}

		/**
		 * Toggle all checkboxes in a WordPress-ish table when the user clicks
		 * the check-all checkbox in the header or footer.
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _toggle_checkboxes( ev ) {
			var chk = jQuery( this ),
				c = chk.prop( 'checked' ),
				toggle = (ev.shiftKey);

			// Toggle checkboxes inside the table body
			chk
				.closest( 'table' )
				.children( 'tbody, thead, tfoot' )
				.filter( ':visible' )
				.children()
				.children( '.check-column' )
				.find( ':checkbox' )
				.prop( 'checked', c);
		}

		/**
		 * Toggle the check-all checkexbox in the header/footer in a
		 * WordPress-ish table when a single checkbox in the body is changed.
		 *
		 * @since  1.0.0
		 */
		function _check_checkboxes( ev ) {
			var chk = jQuery( this ),
				unchecked = chk
					.closest( 'tbody' )
					.find( ':checkbox' )
					.filter( ':visible' )
					.not( ':checked' );

			chk
				.closest( 'table' )
				.children( 'thead, tfoot' )
				.find( ':checkbox' )
				.prop( 'checked',  ( 0 === unchecked.length ) );

			return true;
		}

		// Initialize the popup window.
		_me = this;
		_init();

	}; /* ** End: WpmUiWindow ** */









	/*===============================*\
	===================================
	==                               ==
	==           AJAX-DATA           ==
	==                               ==
	===================================
	\*===============================*/





	/**
	 * Form Data object that is used to load or submit data via ajax.
	 *
	 * @type   WpmUiAjaxData
	 * @since  1.0.0
	 */
	var WpmUiAjaxData = function( _ajaxurl, _default_action ) {

		/**
		 * Backreference to the WpmUiAjaxData object.
		 *
		 * @since  1.0.0
		 * @private
		 */
		var _me = this;

		/**
		 * An invisible iframe with name "wpmui_void", created by this object.
		 *
		 * @type   jQuery object
		 * @since  1.0.0
		 * @private
		 */
		var _void_frame = null;

		/**
		 * Data that is sent to the server.
		 *
		 * @type   Object
		 * @since  1.0.0
		 * @private
		 */
		var _data = {};

		/**
		 * Progress handler during upload/download.
		 * Signature function( progress )
		 *     - progress .. Percentage complete or "-1" for "unknown"
		 *
		 * @type  Callback function.
		 * @since  1.0.0
		 * @private
		 */
		var _onprogress = null;

		/**
		 * Receives the server response after ajax call is finished.
		 * Signature: function( response, okay, xhr )
		 *     - response .. Data received from the server.
		 *     - okay .. bool; false means an error occured.
		 *     - xhr .. XMLHttpRequest object.
		 *
		 * @type  Callback function.
		 * @since  1.0.0
		 * @private
		 */
		var _ondone = null;

		/**
		 * Feature detection: HTML5 upload/download progress events.
		 *
		 * @type  bool
		 * @since  1.0.0
		 * @private
		 */
		var _support_progress = false;

		/**
		 * Feature detection: HTML5 file API.
		 *
		 * @type  bool
		 * @since  1.0.0
		 * @private
		 */
		var _support_file_api = false;

		/**
		 * Feature detection: HTML5 FormData object.
		 *
		 * @type  bool
		 * @since  1.0.0
		 * @private
		 */
		var _support_form_data = false;


		// ==============================
		// == Public functions ==========


		/**
		 * Define the data that is sent to the server.
		 *
		 * @since  1.0.0
		 * @param  mixed Data that is sent to the server. Either:
		 *                - Normal javascript object interpreted as key/value pairs.
		 *                - A jQuery object of the whole form element
		 *                - An URL-encoded string ("key=val&key2=val2")
		 */
		this.data = function data( obj ) {
			_data = obj;
			return _me;
		};

		/**
		 * Returns an ajax-compatible version of the data object passed in.
		 * This data object can be any of the values that is recognized by the
		 * data() method above.
		 *
		 * @since  1.0.7
		 * @param  mixed obj
		 * @return Object
		 */
		this.extract_data = function extract_data( obj ) {
			_data = obj;
			return _get_data( '', false );
		};

		/**
		 * Define the upload/download progress callback.
		 *
		 * @since  1.0.0
		 * @param  function callback Progress handler.
		 */
		this.onprogress = function onprogress( callback ) {
			_onprogress = callback;
			return _me;
		};

		/**
		 * Callback that receives the server response of the ajax request.
		 *
		 * @since  1.0.0
		 * @param  function callback
		 */
		this.ondone = function ondone( callback ) {
			_ondone = callback;
			return _me;
		};

		/**
		 * Reset all configurations.
		 *
		 * @since  1.0.0
		 */
		this.reset = function reset() {
			_data = {};
			_onprogress = null;
			_ondone = null;
			return _me;
		};

		/**
		 * Submit the specified data to the ajaxurl and pass the response to a
		 * callback function. Server response can be any string.
		 *
		 * @since  1.0.0
		 * @param  action string The ajax action to execute.
		 */
		this.load_text = function load_text( action ) {
			action = action || _default_action;
			_load( action, 'text' );

			return _me;
		};

		/**
		 * Submit the specified data to the ajaxurl and pass the response to a
		 * callback function. Server response must be a valid JSON string!
		 *
		 * @since  1.0.0
		 * @param  action string The ajax action to execute.
		 */
		this.load_json = function load_json( action ) {
			action = action || _default_action;
			_load( action, 'json' );

			return _me;
		};

		/**
		 * Submit the specified data to the ajaxurl and let the browser process
		 * the response.
		 * Use this function for example when the server returns a file that
		 * should be downloaded.
		 *
		 * @since  1.0.0
		 * @param  string target Optional. The frame to target.
		 * @param  string action Optional. The ajax action to execute.
		 */
		this.load_http = function load_http( target, action ) {
			target = target || 'wpmui_void';
			action = action || _default_action;
			_form_submit( action, target );

			return _me;
		};


		// ==============================
		// == Private functions =========


		/**
		 * Initialize the formdata object
		 *
		 * @since  1.0.0
		 * @private
		 */
		function _init() {
			// Initialize missing Ajax-URL: Use WordPress ajaxurl if possible.
			if ( ! _ajaxurl && typeof window.ajaxurl === 'string') {
				_ajaxurl = window.ajaxurl;
			}

			// Initialize an invisible iframe for file downloads.
			_void_frame = _body.find( '#wpmui_void' );

			if ( ! _void_frame.length ) {
				/**
				 * Create the invisible iframe.
				 * Usage: <form target="wpmui_void">...</form>
				 */
				_void_frame = jQuery('<iframe></iframe>')
					.attr( 'name', 'wpmui_void' )
					.attr( 'id', 'wpmui_void' )
					.css({
						'width': 1,
						'height': 1,
						'display': 'none',
						'visibility': 'hidden',
						'position': 'absolute',
						'left': -1000,
						'top': -1000
					})
					.hide()
					.appendTo( _body );
			}

			// Find out what HTML5 feature we can use.
			_what_is_supported();

			// Reset all configurations.
			_me.reset();
		}

		/**
		 * Feature detection
		 *
		 * @since  1.0.0
		 * @private
		 * @return bool
		 */
		function _what_is_supported() {
			var inp = document.createElement( 'INPUT' );
			var xhr = new XMLHttpRequest();

			// HTML 5 files API
			inp.type = 'file';
			_support_file_api = 'files' in inp;

			// HTML5 ajax upload "progress" events
			_support_progress = !! (xhr && ( 'upload' in xhr ) && ( 'onprogress' in xhr.upload ));

			// HTML5 FormData object
			_support_form_data = !! window.FormData;
		}

		/**
		 * Creates the XMLHttpReqest object used for the jQuery ajax calls.
		 *
		 * @since  1.0.0
		 * @private
		 * @return XMLHttpRequest
		 */
		function _create_xhr() {
			var xhr = new window.XMLHttpRequest();

			if ( _support_progress ) {
				// Upload progress
				xhr.upload.addEventListener( "progress", function( evt ) {
					if ( evt.lengthComputable ) {
						var percentComplete = evt.loaded / evt.total;
						_call_progress( percentComplete );
					} else {
						_call_progress( -1 );
					}
				}, false );

				// Download progress
				xhr.addEventListener( "progress", function( evt ) {
					if ( evt.lengthComputable ) {
						var percentComplete = evt.loaded / evt.total;
						_call_progress( percentComplete );
					} else {
						_call_progress( -1 );
					}
				}, false );
			}

			return xhr;
		}

		/**
		 * Calls the "onprogress" callback
		 *
		 * @since  1.0.0
		 * @private
		 * @param  float value Percentage complete / -1 for "unknown"
		 */
		function _call_progress( value ) {
			if ( _support_progress && typeof _onprogress === 'function' ) {
				_onprogress( value );
			}
		}

		/**
		 * Calls the "onprogress" callback
		 *
		 * @since  1.0.0
		 * @private
		 * @param  response mixed The parsed server response.
		 * @param  okay bool False means there was an error.
		 * @param  xhr XMLHttpRequest
		 */
		function _call_done( response, okay, xhr ) {
			_call_progress( 100 );
			if ( typeof _ondone === 'function' ) {
				_ondone( response, okay, xhr );
			}
		}

		/**
		 * Returns data object containing the data to submit.
		 * The data object is either a plain javascript object or a FormData
		 * object; this depends on the parameter "use_formdata" and browser-
		 * support for FormData.
		 *
		 * @since  1.0.0
		 * @private
		 * @param  string action
		 * @param  boolean use_formdata If set to true then we return FormData
		 *                when the browser supports it. If support is missing or
		 *                use_formdata is not true then the response is an object.
		 * @return Object or FormData
		 */
		function _get_data( action, use_formdata ) {
			var data = {};
			use_formdata = use_formdata && _support_form_data;

			if ( _data instanceof jQuery ) {

				// ===== CONVERT <form> to data object.

				// WP-Editor needs some special attention first:
				_data.find( '.wp-editor-area' ).each(function() {
					var id = jQuery( this ).attr( 'id' ),
						sel = '#wp-' + id + '-wrap',
						container = jQuery( sel ),
						editor = window.tinyMCE.get( id );

					if ( editor && container.hasClass( 'tmce-active' ) ) {
						editor.save(); // Update the textarea content.
					}
				});

				if ( use_formdata ) {
					data = new window.FormData( _data[0] );
				} else {
					data = {};

					// Convert a jQuery object to data object.

					// ----- Start: Convert FORM to OBJECT
					// http://stackoverflow.com/a/8407771/313501
					var push_counters = {},
						patterns = {
							"validate": /^[a-zA-Z][a-zA-Z0-9_-]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
							"key":      /[a-zA-Z0-9_-]+|(?=\[\])/g,
							"push":     /^$/,
							"fixed":    /^\d+$/,
							"named":    /^[a-zA-Z0-9_-]+$/
						};

					var _build = function( base, key, value ) {
						base[key] = value;
						return base;
					};

					var _push_counter = function( key ) {
						if ( push_counters[key] === undefined ) {
							push_counters[key] = 0;
						}
						return push_counters[key]++;
					};

					jQuery.each( _data.serializeArray(), function() {
						// skip invalid keys
						if ( ! patterns.validate.test( this.name ) ) { return; }

						var k,
							keys = this.name.match(patterns.key),
							merge = this.value,
							reverse_key = this.name;

						while ( ( k = keys.pop() ) !== undefined ) {

							// adjust reverse_key
							reverse_key = reverse_key.replace( new RegExp( "\\[" + k + "\\]$" ), '' );

							// push
							if ( k.match( patterns.push ) ) {
								merge = _build( [], _push_counter( reverse_key ), merge );
							}

							// fixed
							else if ( k.match( patterns.fixed ) ) {
								merge = _build([], k, merge);
							}

							// named
							else if ( k.match( patterns.named ) ) {
								merge = _build( {}, k, merge );
							}
						}

						data = jQuery.extend( true, data, merge );
					});

					// ----- End: Convert FORM to OBJECT

					// Add file fields
					_data.find( 'input[type=file]' ).each( function() {
						var me = jQuery( this ),
							name = me.attr( 'name' ),
							inp = me.clone( true )[0];
						data[':files'] = data[':files'] || {};
						data[':files'][name] = inp;
					});
				}
			} else if ( typeof _data === 'string' ) {

				// ===== PARSE STRING to data object.

				var temp = _data.split( '&' ).map( function (kv) {
					return kv.split( '=', 2 );
				});

				data = ( use_formdata ? new window.FormData() : {} );
				for ( var ind in temp ) {
					var name = decodeURI( temp[ind][0] ),
						val = decodeURI( temp[ind][1] );

					if ( use_formdata ) {
						data.append( name, val );
					} else {
						if ( undefined !== data[name]  ) {
							if ( 'object' !== typeof data[name] ) {
								data[name] = [ data[name] ];
							}
							data[name].push( val );
						} else {
							data[name] = val;
						}
					}
				}
			} else if ( typeof _data === 'object' ) {

				// ===== USE OBJECT to populate data object.

				if ( use_formdata ) {
					data = new window.FormData();
					for ( var data_key in _data ) {
						if ( _data.hasOwnProperty( data_key ) ) {
							data.append( data_key, _data[data_key] );
						}
					}
				} else {
					data = jQuery.extend( {}, _data );
				}
			}

			if ( data instanceof window.FormData ) {
				data.append('action', action);
			} else {
				data.action = action;
			}

			return data;
		}

		/**
		 * Submit the data.
		 *
		 * @since  1.0.0
		 * @private
		 * @param  string action The ajax action to execute.
		 */
		function _load( action, type ) {
			var data = _get_data( action, true ),
				ajax_args = {},
				response = null,
				okay = false;

			if ( type !== 'json' ) { type = 'text'; }

			_call_progress( -1 );

			ajax_args = {
				url: _ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: data,
				xhr: _create_xhr,
				success: function( resp, status, xhr ) {
					okay = true;
					response = resp;
					if ( 'json' === type ) {
						try {
							response = jQuery.parseJSON( resp );
						} catch(ignore) {
							response = { 'status': 'ERR', 'data': resp };
						}
					}
				},
				error: function( xhr, status, error ) {
					okay = false;
					response = error;
				},
				complete: function( xhr, status ) {
					if ( response instanceof Object && 'ERR' === response.status ) {
						okay = false;
					}
					_call_done( response, okay, xhr );
				}
			};

			if ( data instanceof window.FormData ) {
				ajax_args.processData = false;  // tell jQuery not to process the data
				ajax_args.contentType = false;  // tell jQuery not to set contentType
			}

			jQuery.ajax(ajax_args);
		}

		/**
		 * Send data via a normal form submit targeted at the invisible iframe.
		 *
		 * @since  1.0.0
		 * @private
		 * @param  string action The ajax action to execute.
		 * @param  string target The frame to refresh.
		 */
		function _form_submit( action, target ) {
			var data = _get_data( action, false ),
				form = jQuery( '<form></form>' ),
				ajax_action = '';

			// Append all data fields to the form.
			for ( var name in data ) {
				if ( data.hasOwnProperty( name ) ) {
					if ( name === ':files' ) {
						for ( var file in data[name] ) {
							var inp = data[name][file];
							form.append( inp );
						}
					} else if ( name === 'action') {
						ajax_action = name + '=' + data[name].toString();
					} else {
						jQuery('<input type="hidden" />')
							.attr( 'name', name )
							.attr( 'value', data[name] )
							.appendTo( form );
					}
				}
			}

			if ( _ajaxurl.indexOf( '?' ) === -1 ) {
				ajax_action = '?' + ajax_action;
			} else {
				ajax_action = '&' + ajax_action;
			}

			// Set correct form properties.
			form.attr( 'action', _ajaxurl + ajax_action )
				.attr( 'method', 'POST' )
				.attr( 'enctype', 'multipart/form-data' )
				.attr( 'target', target )
				.hide()
				.appendTo( _body );

			// Submit the form.
			form.submit();
		}


		// Initialize the formdata object
		_me = this;
		_init();

	}; /* ** End: WpmUiAjaxData ** */









	/*===============================*\
	===================================
	==                               ==
	==           UTF8-DATA           ==
	==                               ==
	===================================
	\*===============================*/





	/**
	 * Handles conversions of binary <-> text.
	 *
	 * @type   WpmUiBinary
	 * @since  1.0.0
	 */
	var WpmUiBinary = function() {
		var map = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

		WpmUiBinary.utf8_encode = function utf8_encode( string ) {
			if ( typeof string !== 'string' ) {
				return string;
			} else {
				string = string.replace(/\r\n/g, "\n");
			}
			var output = '', i = 0, charCode;

			for ( i; i < string.length; i++ ) {
				charCode = string.charCodeAt(i);

				if ( charCode < 128 ) {
					output += String.fromCharCode( charCode );
				} else if ( (charCode > 127) && (charCode < 2048) ) {
					output += String.fromCharCode( (charCode >> 6) | 192 );
					output += String.fromCharCode( (charCode & 63) | 128 );
				} else {
					output += String.fromCharCode( (charCode >> 12) | 224 );
					output += String.fromCharCode( ((charCode >> 6) & 63) | 128 );
					output += String.fromCharCode( (charCode & 63) | 128 );
				}
			}

			return output;
		};

		WpmUiBinary.utf8_decode = function utf8_decode( string ) {
			if ( typeof string !== 'string' ) {
				return string;
			}

			var output = '', i = 0, charCode = 0;

			while ( i < string.length ) {
				charCode = string.charCodeAt(i);

				if ( charCode < 128 ) {
					output += String.fromCharCode( charCode );
					i += 1;
				} else if ( (charCode > 191) && (charCode < 224) ) {
					output += String.fromCharCode(((charCode & 31) << 6) | (string.charCodeAt(i + 1) & 63));
					i += 2;
				} else {
					output += String.fromCharCode(((charCode & 15) << 12) | ((string.charCodeAt(i + 1) & 63) << 6) | (string.charCodeAt(i + 2) & 63));
					i += 3;
				}
			}

			return output;
		};

		/**
		 * Converts a utf-8 string into an base64 encoded string
		 *
		 * @since  1.0.15
		 * @param  string input A string with any encoding.
		 * @return string
		 */
		WpmUiBinary.base64_encode = function base64_encode( input ) {
			if ( typeof input !== 'string' ) {
				return input;
			}
			else {
				input = WpmUiBinary.utf8_encode( input );
			}
			var output = '', a, b, c, d, e, f, g, i = 0;

			while ( i < input.length ) {
				a = input.charCodeAt(i++);
				b = input.charCodeAt(i++);
				c = input.charCodeAt(i++);
				d = a >> 2;
				e = ((a & 3) << 4) | (b >> 4);
				f = ((b & 15) << 2) | (c >> 6);
				g = c & 63;

				if ( isNaN( b ) ) {
					f = g = 64;
				} else if ( isNaN( c ) ) {
					g = 64;
				}

				output += map.charAt( d ) + map.charAt( e ) + map.charAt( f ) + map.charAt( g );
			}

			return output;
		};

		/**
		 * Converts a base64 string into the original (binary) data
		 *
		 * @since  1.0.15
		 * @param  string input Base 64 encoded text
		 * @return string
		 */
		WpmUiBinary.base64_decode = function base64_decode( input ) {
			if ( typeof input !== 'string' ) {
				return input;
			} else {
				input.replace(/[^A-Za-z0-9\+\/\=]/g, '');
			}
			var output = '', a, b, c, d, e, f, g, i = 0;

			while ( i < input.length ) {
				d = map.indexOf( input.charAt( i++ ) );
				e = map.indexOf( input.charAt( i++ ) );
				f = map.indexOf( input.charAt( i++ ) );
				g = map.indexOf( input.charAt( i++ ) );

				a = (d << 2) | (e >> 4);
				b = ((e & 15) << 4) | (f >> 2);
				c = ((f & 3) << 6) | g;

				output += String.fromCharCode( a );
				if ( f !== 64 ) {
					output += String.fromCharCode( b );
				}
				if ( g !== 64 ) {
					output += String.fromCharCode( c );
				}
			}

			return WpmUiBinary.utf8_decode( output );
		};

	}; /* ** End: WpmUiBinary ** */

}( window.wpmUi = window.wpmUi || {} ));

