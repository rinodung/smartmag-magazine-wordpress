<?php

	if ( !function_exists( 'jordy_meow_flattr' ) ) {
		if ( !defined( 'WP_HIDE_DONATION_BUTTONS' ) )
			add_action( 'admin_head', 'jordy_meow_flattr', 1 );
		function jordy_meow_flattr () {
			?>
				<script type="text/javascript">
					/* <![CDATA[ */
					    (function() {
					        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
					        s.type = 'text/javascript';
					        s.async = true;
					        s.src = '//api.flattr.com/js/0.6/load.js?mode=auto&uid=TigrouMeow&popout=0';
					        t.parentNode.insertBefore(s, t);
					    })();
					/* ]]> */
				</script>
			<?php
		}
		function by_jordy_meow() {
			echo '<div><span style="font-size: 13px; position: relative; top: -6px;">Developed by <a style="text-decoration: none;" target="_blank" href="http://apps.meow.fr">Jordy Meow</a></span>';
			if ( !defined( 'WP_HIDE_DONATION_BUTTONS' ) )
				echo ' <a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" title="Jordy Meow" href="http://profiles.wordpress.org/TigrouMeow/"></a>';
			echo '</div>';
		}
	}

	if ( !function_exists( 'jordy_meow_donation' ) ) {
		function jordy_meow_donation( $showWPE = true ) {
			if ( defined( 'WP_HIDE_DONATION_BUTTONS' ) && WP_HIDE_DONATION_BUTTONS == true )
				return;
			if ( $showWPE ) {
				echo '<a style="float: right;" target="_blank" href="http://shareasale.com/r.cfm?b=786112&amp;u=767054&amp;m=41388&amp;urllink=&amp;afftrack="><img src="http://static.shareasale.com/image/41388/ShareASale-4Ss-728x90.jpg" height="60" border="0" /></a>';
			}
		}
	}

	if ( !function_exists('jordy_meow_footer') ) {
		function jordy_meow_footer() {
			?>
			<div style=" color: #32595E; border: 1px solid #DFDFDF; position: absolute;margin-right: 20px;right: 0px;left: 0px;font-family: Tahoma;z-index: 10;background: white;margin-top: 15px;font-size: 7px;padding: 0px 10px;">
			<p style="font-size: 11px; font-family: Tahoma;"><b>This plugin is actively developed and maintained by <a href='http://www.meow.fr'>Jordy Meow</a></b>.<br />More of my tools are available here: <a href="http://apps.meow.fr">Meow Apps</a>. I am also a photographer in Japan: <a href='http://www.totorotimes.com'>Totoro Times</a>.
			</div>
			<?php
		}
	}
?>
