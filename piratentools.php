<?php
/*
Plugin Name: Piraten-Tools
Plugin URI: https://github.com/stoppegp/Piraten-Tools
Description: Piraten-Tools
Version: 1.4.2
Author: @stoppegp
Author URI: https://github.com/stoppegp/Piraten-Tools
License: CC-BY-SA 3.0
*/

add_action( 'admin_menu', 'piratentools_main_menu' );
wp_enqueue_style( "piratentools", plugin_dir_url(__FILE__)."style.css" );
function piratentools_main_menu() {
	//add_options_page( 'My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
    add_menu_page( "Piraten-Tools", "Piraten-Tools", 0, "piratentools", "piratentools_main_options" );
	add_submenu_page( "piratentools", "Next Piratentreff", "Next Piratentreff", "manage_options", "pt_nextpiratentreff", array("PT_nextpiratentreff", "adminmenu") );
	add_submenu_page( "piratentools", "Wiki Import", "Wiki Import", "manage_options", "pt_wikiimport", array("PT_wikiimport", "adminmenu") );
	add_submenu_page( "piratentools", "Stimmverhalten", "Stimmverhalten", "manage_options", "pt_stimm", array("PT_stimm", "adminmenu") );
}

function piratentools_main_options() {
	echo '<div class="wrap">';
	echo '<h2>Piraten-Tools</h2>';
	?>
	
<h3>Next Piratentreff</h3>
<p>Aus einem iCal-Kalender wird der nächste Termin gesucht, der einen bestimmten Suchbegriff im Titel enthält. Das Ergebnis kann dann per Shortcode eingebaut werden.
Dadurch kann man z.B. automatisiert den nächsten Stammtisch-Termin ausgeben.
(Anleitung tbd)</p>

<h3>Wiki Import</h3>
<p>VOn einer externen Website kann der Inhalt eines div-Containers mit bekannter ID ausgelesen und ausgegeben werden.
Dadurch kann man zum Beispiel automatisiert die Tagesordnung des nächsten Stammtischs aus dem Piratenwiki ausgeben.
(Anleitung tbd)</p>

<h3>Stimmverhalten</h3>
<p>Text tbd</p>
	
	<?php
	echo '</div>';
}

require('nextpiratentreff/nextpiratentreff.php');
require('wikiimport/wikiimport.php');
require('stimm/stimm.php');
?>