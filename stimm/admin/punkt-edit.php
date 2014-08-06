<div class="wrap pt-stimm">

<h2>Gremium: <?php echo $gremium->name; ?><br>
Sitzung: <?php echo date("d.m.Y", $sitzung->datum); ?> - <?php echo $sitzung->name; ?>
</h2>
<hr>
<h3>Punkt: <?php echo $punkt->getID(); ?></h3>


<form method="post">
Text: <textarea name="pt-stimm-punkt-text"><?php echo htmlspecialchars($punkt->text); ?></textarea><br>
Link zum RIS: <input name="pt-stimm-punkt-rislink" value="<?=$punkt->rislink;?>"><br>
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-page" value="gremium-edit">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<input type="hidden" name="pt-stimm-punkt-id" value="<?=$punktid;?>">
<input type="hidden" name="pt-stimm-action" value="punkt-edit">
<button type="submit">Punkt bearbeiten</button>
</form>
<hr>
<form method="post">
<input type="hidden" name="pt-stimm-page" value="sitzung-edit">
<input type="hidden" name="pt-stimm-gremium-id" value="<?=$gremiumid;?>">
<input type="hidden" name="pt-stimm-sitzung-id" value="<?=$sitzungid;?>">
<button type="submit">Zurück</button>

</form>


</div>