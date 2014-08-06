<div class="wrap pt-stimm">

<h2>Gremium: <?php echo $gremium->name; ?><br>
Sitzung: <?php echo date("d.m.Y", $sitzung->datum); ?> - <?php echo $sitzung->name; ?>
</h2>
<hr>
<form method="post">

Name der Sitzung: <input type="text" name="pt-stimm-sitzung-name" value="<?php echo $sitzung->name; ?>"> Datum der Sitzung: <input type="text" name="pt-stimm-sitzung-datum" value="<?php echo date("d.m.Y", $sitzung->datum); ?>"> Link zur TO: <input type="text" name="pt-stimm-sitzung-linkzurto" value="<?php echo $sitzung->linkzurto; ?>"> <button type="submit">Sitzung ändern</button>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<input type="hidden" name="pt-stimm-action" value="sitzung-edit">
</form>
<hr>
<form method="post">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<input type="hidden" name="pt-stimm-action" value="toimport">
Tagesordnung von URL importieren: <input type="text" name="pt-stimm-toimport" value="<?php echo $sitzung->linkzurto; ?>"> <button type="submit">Importieren</button>
</form>
<hr>
<h2>Punkte</h2>
<form method="post">
<table>
<tr><th>Text</th><th></th><th></th>
<?php
$cc = 0;
foreach ($gremium->mitglieder as $val) {
	$cc++;
	echo "<th><abbr title=\"".$val->name."\">".$val->kuerzel."</abbr></th>";
}
?>

</tr>
<?php
$c0 = 0;

foreach($sitzung->punkte as $val) {
	$c0++;
	echo "<tr>";
	echo "<td><abbr title='".htmlspecialchars($val->text)."'>".substr($val->text, 0, 100)."</abbr></td>";
	echo "<td><button type=\"submit\" name=\"pt-stimm-punkt-edit\" value=\"".$val->getID()."\">Bearbeiten</button> <button class=\"del\" type=\"submit\" name=\"pt-stimm-punkt-del\" value=\"".$val->getID()."\">Löschen</button></form></td>";
	echo "<td>&nbsp;&nbsp;</td>";
	foreach ($gremium->mitglieder as $val2) {
		echo "<td style=\"background-color:#dd9;\">";
		echo "<select name=\"pt-stimm-stimmen[".$val->getID()."][".$val2->getID()."]\">";
		foreach (PT_stimm::$stimmen as $key3 => $val3) {
			if ($val->stimmen[$val2->getID()] == $key3) $sel = "selected=\"selected\"";
			else $sel = "";
			echo "<option $sel style=\"background-color:".$val3['color'].";color:".$val3['textcolor']."\" value=\"".$key3."\">".$val3['name']."</option>";
		}
		echo "</select>";
		echo "</td>";
	}
	
	
	echo "</tr>";
	//echo "<li>".$val->getID().": ".$val->text." | <form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"mitglied-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$gremiumid."\"><input type=\"hidden\" name=\"pt-stimm-mitglied-id\" value=\"".$val->getID()."\"><input type=\"submit\" value=\"Löschen\"></form>";
}
if ($c0>0) {
?>
<tr>
<td></td><td></td><td></td>
<td style="text-align: center;background-color:#dd9;" colspan="<?=$cc;?>"><button type="submit">Änderungen speichern</button></td>
</tr>
<?php } ?>
</table>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-action" value="stimmen-edit">

</form>
<h3>Neuer Punkt</h3>
<form method="post">
Text: <textarea name="pt-stimm-punkt-text"></textarea><br>
Link zum RIS: <input name="pt-stimm-punkt-rislink"><br>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<input type="hidden" name="pt-stimm-action" value="punkt-add">
<button type="submit">Punkt hinzufügen</button>

</form>

</form>
<hr>
<form method="post">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<button type="submit">Zurück</button>

</form>

</div>
