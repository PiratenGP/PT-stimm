<div class="wrap pt-stimm">
<h2>PT-Stimmverhalten</h2>
<hr>
<h3>Gremien</h3>
<table>
<tr>
<th>ID</th><th>Name</th><th>Shortcode</th><th>Daten</th><th></th>
</tr>
<?php
foreach($options['gremien'] as $val) {
	

	$url = plugin_dir_url( __FILE__ );
	$url = substr( $url, 0, strrpos( $url, '/'));
	$url = substr( $url, 0, strrpos( $url, '/'));
	echo "<tr>";
	echo "<td>".$val->getID()."</td>";
	echo "<td>".$val->name."</td>";
	echo "<td><pre>[pt-stimm id=".$val->getID()."]</pre></td>";
	echo "<td><a href=\"".$url."/getjson.php?id=".$val->getID()."\">Daten</a></td>";
	echo "<td><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-page\" value=\"gremium-edit\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><button type=\"submit\" value=\"Bearbeiten\">Bearbeiten</button></form></td>";
	echo "<td><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"gremium-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><input type=\"checkbox\" value=\"doit\" name=\"valdel".$val->getID()."\" id=\"valdel".$val->getID()."\" /> <label for=\"valdel".$val->getID()."\">Löschen</label> <button class=\"del\" type=\"submit\" value=\"Löschen\">Löschen</button></form></td>";
	echo "</tr>";
	//echo "<li>".$val->getID().": ".$val->name." | <!--<form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"gremium-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><input type=\"submit\" value=\"Löschen\"></form> | --><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-page\" value=\"gremium-edit\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><input type=\"submit\" value=\"Bearbeiten\"></form></li>";
}
?>
</table>


<form method="post">

<h4>Neues Gremium</h4>
Name: <input name="pt-stimm-name">
<input type="hidden" name="pt-stimm-action" value="gremium-add"><button type="submit">Gremium hinzufügen</button>

</form>
<form method="post" enctype="multipart/form-data">
<h4>Gremium importieren <span style="color:red;font-weight:bold;">(BETA!)</span></h4>
Datei: <input type="file" name="pt-stimm-import-file">
<input type="hidden" name="pt-stimm-action" value="gremium-import-file"><button type="submit">Gremium hinzufügen</button>

</form>

<hr>
<h3>Externe Gremien</h3>
<table>
<tr>
<th>ID</th><th>URL</th><th>Shortcode</th><th></th>
</tr>
<?php
if (is_array($options['gremien-ext']) && (count($options['gremien-ext']) > 0)) {
foreach($options['gremien-ext'] as $key => $val) {
	echo "<tr>";
	echo "<td>".$key."</td>";
	echo "<td>".$val['url']."</td>";
	echo "<td><pre>[pt-stimm ext=".$key."]</pre></td>";
	echo "<td><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"gremium-ext-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$key."\"><button class=\"del\" type=\"submit\" value=\"Löschen\">Löschen</button></form></td>";
	echo "</tr>";
	//echo "<li>".$val->getID().": ".$val->name." | <!--<form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-action\" value=\"gremium-del\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><input type=\"submit\" value=\"Löschen\"></form> | --><form method=\"post\"><input type=\"hidden\" name=\"pt-stimm-page\" value=\"gremium-edit\"><input type=\"hidden\" name=\"pt-stimm-gremium-id\" value=\"".$val->getID()."\"><input type=\"submit\" value=\"Bearbeiten\"></form></li>";
}
}
?>
</table>


<form method="post">

<h4>Neues externes Gremium</h4>
URL: <input name="pt-stimm-gremium-ext-url">
<input type="hidden" name="pt-stimm-action" value="gremium-ext-add">
<button type="submit">externes Gremium hinzufügen</button>

</form>

<hr>
<h3>Custom Stylesheet</h3>
<form method="post">
<textarea style="height:400px;width: 400px;" name="pt-stimm-style"><?php echo htmlspecialchars($style); ?></textarea><br>
<input type="hidden" name="pt-stimm-action" value="style-edit">
<button type="submit">Stylesheet ändern</button>
</form>

</div>