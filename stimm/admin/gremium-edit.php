<div class="wrap pt-stimm">

<h2>Gremium: <?php echo $gremium->name; ?></h2>
<hr>
<form method="post">

Name des Gremiums: <input type="text" name="pt-stimm-gremium-name" value="<?php echo $gremium->name; ?>"> <button type="submit">Name ändern</button>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-action" value="gremium-edit">
</form>
<hr>
<h3>Mitglieder</h3>

<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Kürzel</th>
<th>Partei</th>
<th></th>
</tr>
<?php
foreach($gremium->mitglieder as $val) {

	echo "<tr>";
	echo "<td>".$val->getID()."</td>";
	echo "<td>".$val->name."</td>";
	echo "<td>".$val->kuerzel."</td>";
	echo "<td>".$val->partei."</td>";
	echo "<td><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-page\" value=\"mitglied-edit\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$gremiumid."\"><input type=\"hidden\" name=\"pt-stimm-mitglied-id\" value=\"".$val->getID()."\"><button type=\"submit\" value=\"Bearbeiten\">Bearbeiten</button></form><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"mitglied-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$gremiumid."\"><input type=\"hidden\" name=\"pt-stimm-mitglied-id\" value=\"".$val->getID()."\"><button class=\"del\" type=\"submit\" value=\"Löschen\">Löschen</button></form></td>";
	echo "</tr>";

}
?>
</table>

<form method="post">

<h4>Neues Mitglied</h4>
Name: <input name="pt-stimm-mitglied-name">
Kürzel: <input name="pt-stimm-mitglied-kuerzel">
Partei: <input name="pt-stimm-mitglied-partei">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-action" value="mitglied-add">
<button type="submit">Mitglied hinzufügen</button>

</form>

<hr>

<h3>Sitzungen</h3>

<table border="1">
<tr>
<th>Datum</th>
<th>Name</th>
<th></th>
</tr>
<?php


foreach($gremium->sitzungen as $val) {
	echo "<tr>";
	echo "<td>".date("d.m.Y", $val->datum)."</td>";
	echo "<td>".$val->name."</td>";
	echo "<td><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$gremiumid."\"><input type=\"hidden\" name=\"pt-stimm-sitzung-id\" value=\"".$val->getID()."\"><input type=\"hidden\" name=\"pt-stimm-page\" value=\"sitzung-edit\"><button type=\"submit\" value=\"Bearbeiten\">Bearbeiten</button></form>";
	echo "<form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$gremiumid."\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"sitzung-del\"><input type=\"hidden\" name=\"pt-stimm-sitzung-id\" value=\"".$val->getID()."\"><button class=\"del\" type=\"submit\" value=\"Löschen\">Löschen</button></form></td>";
	echo "</tr>";
}
?>
</table>

<h4>Neue Sitzung</h4>
<form method="post">
Name: <input name="pt-stimm-sitzung-name">
Datum: <input name="pt-stimm-sitzung-datum">
Link zur TO: <input name="pt-stimm-sitzung-linkzurto">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-action" value="sitzung-add">
<button type="submit">Sitzung hinzufügen</button>

</form>
<hr>
<form method="post">
<input type="hidden" name="pt-stimm-page" value="home">
<button type="submit">Zurück</button>

</form>

</div>