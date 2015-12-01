<?php
/**
 * Metabox inside posts/pages where user can define custom sidebars for an
 * individual post.
 *
 * Uses:
 *   $selected
 *   $wp_registered_sidebars
 *   $post_id
 */

$available = $wp_registered_sidebars;
$sidebars = CustomSidebars::get_options( 'modifiable' );

$is_front = $post_id == get_option( 'page_on_front' );
$is_blog = $post_id == get_option( 'page_for_posts' );
?>

<?php if ( $is_front || $is_blog ) : ?>
	<p>
		<?php printf(
			__(
				'<strong>To change the sidebar for static Front-Page or ' .
				'Posts-Page</strong>:<ul>' .
				'<li>Go to the <a href="%1$s">Widgets page</a></li>' .
				'<li>Click on "Sidebar Location"</li>' .
				'<li>Open the "Archive-Types" tab</li>' .
				'<li>Choose "Front-Page" or "Post-Index"</li></ul>',
				CSB_LANG
			),
			admin_url( 'widgets.php' )
		); ?>
	</p>

	<img src="<?php echo esc_url( CSB_IMG_URL . 'frontpage-info.png' ); ?>" style="width:274px;margin:0 0 -14px -10px;" />

	<?php foreach ( $sidebars as $s ) { ?>
		<input type="hidden"
			name="cs_replacement_<?php echo esc_attr( $s ); ?>"
			value="<?php echo esc_attr( $selected[ $s ] ); ?>" />
	<?php } ?>

<?php else : ?>

	<p>
		<?php _e(
			'Here you can replace the default sidebars. Simply select what ' .
			'sidebar you want to show for this post!', CSB_LANG
		); ?>
	</p>

	<?php if ( ! empty( $sidebars ) ) { ?>
		<?php foreach ( $sidebars as $s ) { ?>
			<?php $sb_name = $available[ $s ]['name']; ?>
			<p>
				<label for="cs_replacement_<?php echo esc_attr( $s ); ?>">
					<b><?php echo esc_html( $sb_name ); ?></b>:
				</label>
				<select name="cs_replacement_<?php echo esc_attr( $s ); ?>"
					id="cs_replacement_<?php echo esc_attr( $s ); ?>"
					class="cs-replacement-field <?php echo esc_attr( $s ); ?>">
					<option value=""></option>
					<?php foreach ( $available as $a ) : ?>
					<option value="<?php echo esc_attr( $a['id'] ); ?>" <?php selected( $selected[ $s ], $a['id'] ); ?>>
						<?php echo esc_html( $a['name'] ); ?>
					</option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php
		}
	} else {
		?>
		<p id="message" class="updated">
			<?php _e(
				'All sidebars have been locked, you cannot replace them. ' .
				'Go to <a href="widgets.php">the widgets page</a> to unlock a ' .
				'sidebar', CSB_LANG
			); ?>
		</p>
		<?php
	}

endif;
