<?php
/**
 * Description tab
 * 
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $post;

$heading = esc_html( apply_filters( 'woocommerce_product_description_heading', _x('Product Description', 'woocommerce', 'bunyad') ) );
?>

<h2><?php echo $heading; ?></h2>

<div class="post-content"><?php the_content(); ?></div>