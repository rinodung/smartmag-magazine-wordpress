<?php

add_filter( 'wp_calculate_image_srcset', 'wr2x_wp_calculate_image_srcset', 1000, 2 );
//add_filter( 'wp_calculate_image_sizes', 'wr2x_wp_calculate_image_sizes', 1000, 2 );
//add_filter( 'wp_get_attachment_image_attributes', 'wr2x_wp_get_attachment_image_attributes', 1000, 2 );

/**
 *
 * SUPPORT FOR WP 4.4
 *
 */

function wr2x_wp_calculate_image_srcset( $srcset, $size ) {
  if ( wr2x_getoption( "disable_responsive", "wr2x_basics", false ) )
    return null;
  $count = 0;
  $total = 0;
  $retinized_srcset = $srcset;
  foreach ( $srcset as $s => $cfg ) {
    $total++;
    $retina = wr2x_get_retina_from_url( $cfg['url'] );
    if ( !empty( $retina ) ) {
      $count++;
      $retinized_srcset[(int)$s * 2] = array(
        'url' => $retina,
        'descriptor' => 'w',
        'value' => (int)$s * 2 );
    }
  }
  wr2x_log( "WP's srcset: " . $count . " retina files added out of " . $total . " image sizes" );
  return $retinized_srcset;
}

function wr2x_wp_calculate_image_sizes( $srcset, $size ) {
}

function wr2x_wp_get_attachment_image_attributes( $srcset, $size ) {
}

?>
