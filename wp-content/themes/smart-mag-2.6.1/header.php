<!DOCTYPE html>

<!--[if IE 8]> <html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]> <html class="ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>

<?php 
/**
 * Match wp_head() indent level
 */
?>

<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php wp_title(''); // stay compatible with SEO plugins ?></title>

<?php if (!Bunyad::options()->no_responsive): // don't add if responsiveness disabled ?> 
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php endif; ?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
<?php if (Bunyad::options()->favicon): ?>
<link rel="shortcut icon" href="<?php echo esc_attr(Bunyad::options()->favicon); ?>" />	
<?php endif; ?>

<?php if (Bunyad::options()->apple_icon): ?>
<link rel="apple-touch-icon-precomposed" href="<?php echo esc_attr(Bunyad::options()->apple_icon); ?>" />
<?php endif; ?>
	
<?php wp_head(); ?>
	
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

</head>


<body <?php body_class(); ?>>

<div class="main-wrap">

	<?php 

	/**
	 * Get the selected header template
	 * 
	 * Note: Default is partials/header/layout.php
	 */
	get_template_part('partials/header/layout', Bunyad::options()->header_style);
	
	?>
	
<?php if (!Bunyad::options()->disable_breadcrumbs): ?>
	<div <?php Bunyad::markup()->attribs('breadcrumbs-wrap', array('class' => array('breadcrumbs-wrap'))); ?>>
		
		<div class="wrap">
		<?php Bunyad::core()->breadcrumbs(); ?>
		</div>
		
	</div>
<?php endif; ?>

<?php do_action('bunyad_pre_main_content'); ?>