<?php 

/**
 * "loop" to display posts when using an existing query. Used for categories and arhives.
 * 
 * Uses loop-grid-overlay.php code. 
 */

Bunyad::registry()->loop_grid = 3;

$loop_type   = 'tall-overlay';
$loop_image  = 'tall-overlay';
$loop_column = (Bunyad::core()->get_sidebar() == 'none' ? 'one-fourth' : 'one-third');
$loop_grid_class =  (Bunyad::core()->get_sidebar() == 'none' ? 'grid-4' : '');

include locate_template('loop-grid-overlay.php');