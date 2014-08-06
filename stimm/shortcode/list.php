<table class="pt-stimm">
<tr><th>Datum</th>
<th></th>
<?php
foreach ($gremium->mitglieder as $val) {
	if (is_array($mitglieder) && !in_array($val->id, $mitglieder)) continue;
	echo "<th class=\"mitglied\"><abbr title=\"".$val->name."\">".$val->kuerzel."</abbr></th><th></th>";
}
?>

<th>Ereignis</th><th></th></tr>

<?php

$daten = (array) $gremium->sitzungenByDatum;

krsort($daten);



foreach ($daten as $key0 => $val0) {
	foreach ($val0 as $key => $val) {

		echo "<tr class=\"sitzung\"><td><strong>".date("d.m.Y", $val->datum)."</strong></td><td></td>";
		foreach ($gremium->mitglieder as $valm) {
			if (is_array($mitglieder) && !in_array($valm->id, $mitglieder)) continue;
			echo "<td></td><td></td>";
		}
		echo "<td><strong>".$val->name."</strong>";
		if ($val->linkzurto) {
			echo " <small><a href=\"".$val->linkzurto."\">(Tagesordnung)</a></small>";
		}
		echo "</td><td></td></tr>";
		$c = 0;
		foreach ($val->punkte as $val2) {
			?>
			<tr>
			<td></td><td></td>
			<?php
			foreach ($gremium->mitglieder as $val3) {
				if (is_array($mitglieder) && !in_array(($val3->id), $mitglieder)) continue;
				$stimme = $val2->stimmen[$val3->id];
				if (!$stimme) $stimme = 0;
				$stimme = PT_stimm::$stimmen[$stimme];
				echo "<td class=\"mitglied\" title=\"".$stimme['name']."\" style=\"background-color:".$stimme['color'].";color:".$stimme['textcolor']."\">";
				echo $stimme['kuerzel'];
				echo "</td><td></td>";
			}
			?>
			<td><?=$val2->text;?></td>
			<td>
			<?php
			if ($val2->rislink != "") echo "<a title=\"Ratsinformationssystem\" href=\"".$val2->rislink."\">RIS</a>";
			?>
			</td>
			</tr>
			<?php
		}
	}
}

?>
</table>
