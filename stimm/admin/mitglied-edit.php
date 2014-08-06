<div class="wrap pt-stimm">

<h2>Gremium: <?php echo $gremium->name; ?></h2>
<hr>
<h3>Mitglied: <?php echo $mitglied->name; ?></h3>


<form method="post">

Name: <input name="pt-stimm-mitglied-name" value="<?=$mitglied->name;?>"><br>
Kürzel: <input name="pt-stimm-mitglied-kuerzel" value="<?=$mitglied->kuerzel;?>"><br>
Partei: <input name="pt-stimm-mitglied-partei" value="<?=$mitglied->partei;?>"><br>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-mitglied-id" value="<?=$mitgliedid;?>">
<input type="hidden" name="pt-stimm-action" value="mitglied-edit">
<button type="submit">Mitglied bearbeiten</button>

</form>
<hr>
<form method="post">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<button type="submit">Zurück</button>

</form>


</div>