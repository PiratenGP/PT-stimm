<?php
/*
Plugin Name: Piraten-Tools / Stimmverhalten
Plugin URI: https://github.com/PiratenGP/PT-stimm
Description: Piraten-Tools / Stimmverhalten
Version: 1.3.1
Author: @stoppegp
Author URI: http://stoppe-gp.de
License: CC-BY-SA 3.0
*/

global $PT_infos;
$PT_infos[] = array(
	'name'		=>		'Stimmverhalten',
	'desc'		=>		'Infos tbd',
);

require('mainmenu.php');

if (!function_exists("piratentools_main_menu")) {
	add_action( 'admin_menu', 'piratentools_main_menu');
	function piratentools_main_menu() {
		add_menu_page( "Piraten-Tools", "Piraten-Tools", 0, "piratentools" , "PT_main_menu");
	}
}

add_action( 'admin_menu', 'PT_stimm_main_menu' );
function PT_stimm_main_menu() {
	add_submenu_page( "piratentools", "Stimmverhalten", "Stimmverhalten", "manage_options", "pt_stimm", array("PT_stimm", "adminmenu") );
}

require('stimm/stimm.php');
?>