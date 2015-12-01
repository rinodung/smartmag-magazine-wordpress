<?php

add_action( 'cs_init', array( 'CustomSidebarsExplain', 'instance' ) );

/**
 * Adds some additional information to the page output which explain why which
 * Sidebar/widgets were added to the current page.
 *
 * =================================== USAGE ===================================
 *
 * Activate the explanation mode via URL parameter: "?cs-explain=on"
 * Deactiavte by setting the parameter to "off"
 *
 * The explanation is only displayed for the user that did activate it, other
 * users will not see anything.
 *
 * Explain-mode will possibly break the layout of the page, but it makes it
 * much easier to understand which sidebars and widgets are displayed and why.
 * It is meant for temporary debugging only and should be turned off when not
 * needed anymore.
 *
 * =============================================================================
 *
 */
class CustomSidebarsExplain extends CustomSidebars {

	/**
	 * Infos added via cs_explain.
	 * @var array
	 */
	private $infos = array();

	/**
	 * Returns the singleton object.
	 *
	 * @since  2.0.9.1
	 */
	public static function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new CustomSidebarsExplain();
		}

		return $Inst;
	}

	/**
	 * Constructor is private -> singleton.
	 *
	 * @since  2.0.9.1
	 */
	private function __construct() {
		if ( ! session_id() ) {
			session_start();
		}
		if ( isset( $_GET['cs-explain'] ) ) {
			self::set_explain( $_GET['cs-explain'] );
		}

		if ( is_admin() ) {
			add_action(
				'cs_widget_header',
				array( $this, 'widget_header' )
			);

			add_action(
				'cs_ajax_request',
				array( $this, 'handle_ajax' )
			);
		} else {
			if ( self::do_explain() ) {
				add_action(
					'cs_explain',
					array( $this, 'add_info' ),
					10, 2
				);

				add_action(
					'wp_footer',
					array( $this, 'show_infos' )
				);

				add_action(
					'dynamic_sidebar_before',
					array( $this, 'before_sidebar' ),
					0, 2
				);

				add_action(
					'dynamic_sidebar_after',
					array( $this, 'after_sidebar' ),
					0, 2
				);
			}
		}
	}

	/**
	 * Called by action 'cs_widget_header'. Output the export/import button in
	 * the widget header.
	 *
	 * @since  2.0.9.1
	 */
	public function widget_header() {
		/*
		$state = self::do_explain() ? 'on' : 'off';
		?>
		<a href="#"
			class="cs-action btn-explain"
			data-status="<?php echo esc_attr( $state ); ?>"
			data-label-off="<?php _e( 'Show explanations', CSB_LANG ); ?>"
			data-label-on="<?php _e( 'Hide explanations', CSB_LANG ); ?>">
		</a>
		<?php
		*/
	}

	/**
	 * When the custom sidebars section is visible we see if export-action
	 * needs to be processed.
	 *
	 * @since  2.0.9.1
	 */
	public function handle_ajax( $ajax_action ) {
		$handle_it = false;
		$req = (object) array(
			'status' => 'ERR',
		);

		switch ( $ajax_action ) {
			case 'explain':
				$handle_it = true;
				break;
		}

		if ( ! $handle_it ) {
			return false;
		}

		$state = @$_POST['state'];

		switch ( $ajax_action ) {
			case 'explain':
				self::set_explain( $state );
				$req->status = 'OK';
				$req->state = self::do_explain() ? 'on' : 'off';
				break;
		}

		self::json_response( $req );
	}

	/**
	 * Returns true if the "explain mode" is enabled.
	 * Explain mode will display additional information in the front-end of the
	 * website on why which sidebar/widget is displayed.
	 * This is a per-user option (stored in current session)
	 *
	 * @since  2.0.9.1
	 * @return boolean
	 */
	public static function do_explain() {
		return 'on' == @$_SESSION['cs-explain'];
	}

	/**
	 * Sets the explain state
	 *
	 * @since 2.0.9.1
	 * @param string $state [on|off]
	 */
	public static function set_explain( $state ) {
		if ( $state != 'on' ) {
			$state = 'off';
		}
		$_SESSION['cs-explain'] = $state;
	}

	/**
	 * Adds an info to the explanation output.
	 *
	 * @since 2.0.9.1
	 */
	public function add_info( $info, $new_item = false ) {
		if ( $new_item ) {
			$this->infos[] = $info;
		} else {
			$this->infos[ count( $this->infos ) - 1 ] .= '<br />' . $info;
		}
	}

	/**
	 * Outputs the collected information to the webpage.
	 *
	 * @since  2.0.9.1
	 */
	public function show_infos() {
		#global $wp_registered_sidebars;
		#var_dump( $wp_registered_sidebars );
		?>
		<div class="cs-infos" style="width:600px;margin:10px auto;padding:10px;color:#666;background:#FFF;">
			<style>
			.cs-infos > ul { list-style:none; padding: 0; margin: 0; }
			.cs-infos > ul > li { margin: 0; padding: 10px 0 10px 30px; border-bottom: 1px solid #eee; }
			.cs-infos h4 { color: #600; margin: 10px 0 0 -30px; }
			.cs-infos h5 { color: #006; margin: 10px 0 0 -15px; }
			</style>
			<h3>Sidebar Infos</h3>
			<a href="?cs-explain=off" style="float:right;color:#009">Turn off explanations</a>
			<ul>
				<?php foreach ( $this->infos as $info ) : ?>
					<li><?php echo $info; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Returns a random hex color.
	 *
	 * @since  2.0.9.1
	 * @return [type] [description]
	 */
	static public function get_color() {
		$r = rand( 40, 140 );
		$g = rand( 40, 140 );
		$b = rand( 40, 140 );
		return '#' . dechex( $r ) . dechex( $g ) . dechex( $b );
	}

	/**
	 * Adds a border/title to the sidebar to better illustrate the position/ID.
	 *
	 * @since  2.0.9.1
	 */
	public function before_sidebar( $index, $has_widgets ) {
		global $wp_registered_sidebars;
		$col = self::get_color();
		$w_col = self::get_color();

		$wp_registered_sidebars[$index]['before_widget'] =
			'<div style="border:2px solid ' . $w_col . ';margin:2px;width:auto;clear:both">' .
			'<div style="font-size:12px;padding:1px 4px 1px 6px;float:right;background-color:' . $w_col . ';color:#FFF">%1$s</div>' .
			@$wp_registered_sidebars[$index]['before_widget'];
		$wp_registered_sidebars[$index]['after_widget'] =
			@$wp_registered_sidebars[$index]['after_widget'] .
			'<div style="clear:both"> </div>' .
			'</div>';
		?>
		<div style="border:2px solid <?php echo esc_attr( $col ); ?>;position:relative;">
			<div style="font-size:12px;padding:1px 4px 1px 6px;float:right;background-color:<?php echo esc_attr( $col ); ?>;margin-bottom:2px;color:#FFF"><?php echo esc_html( $index ); ?></div>
		<?php
	}

	/**
	 * Closes the border around sidebar.
	 *
	 * @since  2.0.9.1
	 */
	public function after_sidebar( $index, $has_widgets ) {
		?>
		<div style="clear:both"> </div>
		</div>
		<?php
	}

};