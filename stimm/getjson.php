<?php 
/* Short and sweet */
define('WP_USE_THEMES', false);
require('../../../../wp-blog-header.php');

$options = get_option("pt_stimm");

$id = $_GET['id'];
if (!is_numeric($id)) exit;
header('Content-type: application/json');
header('Content-Disposition: attachment; filename="ptstimm-'.$id.'.json"');
echo JSON_encode($options['gremien'][$id]);

?>