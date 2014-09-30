<table>
<?php
foreach (PT_stimm::$stimmen as $stimme) {
	echo "<tr>";
	echo "<td title=\"".$stimme['name']."\" class=\"mitglied pt-stimm-stimme ".$stimme['class']."\">";
	echo $stimme['kuerzel'];
	echo "</td>";
	echo "<td>".$stimme['name']."</td>";
	echo "</tr>";
}
?>

</table>