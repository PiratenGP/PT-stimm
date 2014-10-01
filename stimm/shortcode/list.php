<script type="text/javascript">
$(document).ready(function(){
const linkcolor = $("tr.sitzung td a").css("color");

$('tr.sitzung').each(function() {
	$(this).css("cursor", "pointer");
	const show = $(this).data("show");
	const sitzung = $(this).attr("data-sitzung");
	if (!show) {
		$(this).find(".pt-stimm-pfeil").html("▼");
		$("tr").each(
			function(){
				if ($(this).data("sitzung2") == sitzung) {
					$(this).css("display", "none");
				}
			}
		);
	} else {
	$(this).find(".pt-stimm-pfeil").html("▲");
	}
	
	$(this).hover(  function () {
           $(this).css({"background-color":linkcolor});
         }, 
         function () {
           $(this).css({"background-color":"transparent"});
         });
		 
	$(this).click(function(){
		const sitzung = $(this).attr("data-sitzung");
		const show = $(this).data("show");
		
		if (show) {
			$(this).find(".pt-stimm-pfeil").html("▼");
		} else {
			$(this).find(".pt-stimm-pfeil").html("▲");
		}
		
		$("tr").each(
			function(){
				if ($(this).data("sitzung2") == sitzung) {
					if (show) {
						$(this).css("display", "none");
					} else {
						$(this).css("display", "table-row");
					}
				}
			}
		);
		$(this).data("show", !show);
	}
	);
}

);
});
</script>

<table class="pt-stimm">
<tr><th class="pt-stimm-datum">Datum</th>
<th class="pt-stimm-separator"></th>
<?php
foreach ($gremium->mitglieder as $val) {
	if (is_array($mitglieder) && !in_array($val->id, $mitglieder)) continue;
	echo "<th class=\"mitglied\"><abbr title=\"".$val->name."\">".$val->kuerzel."</abbr></th><th class=\"pt-stimm-separator\"></th>";
}
?>

<th>Ereignis</th><th></th></tr>

<?php

$daten = (array) $gremium->sitzungenByDatum;

krsort($daten);


$cg = 0;
foreach ($daten as $key0 => $val0) {
	foreach ($val0 as $key => $val) {
		$cg++;
		if (($hidefrom > 0) && ($cg >= $hidefrom)) {
			$datashow = "false";
		} else {
			$datashow = "true";
		}
		echo "<tr class=\"sitzung\" data-show=\"".$datashow."\" data-sitzung=\"".$key0."-".$key."\"><td><strong>".date("d.m.Y", $val->datum)."</strong></td><td></td>";
		foreach ($gremium->mitglieder as $valm) {
			if (is_array($mitglieder) && !in_array($valm->id, $mitglieder)) continue;
			echo "<td></td><td></td>";
		}
		echo "<td><strong>".$val->name."</strong>";
		if ($val->linkzurto) {
			echo " <small><a href=\"".$val->linkzurto."\">(Tagesordnung)</a></small>";
		}
		echo "</td><td class=\"pt-stimm-pfeil\"></td></tr>";
		$c = 0;
		foreach ($val->punkte as $val2) {
			?>
			<tr data-sitzung2="<?=$key0;?>-<?=$key;?>">
			<td></td><td></td>
			<?php
			foreach ($gremium->mitglieder as $val3) {
				if (is_array($mitglieder) && !in_array(($val3->id), $mitglieder)) continue;
				$stimme = $val2->stimmen[$val3->id];
				if (!$stimme) $stimme = 0;
				$stimme = PT_stimm::$stimmen[$stimme];
				echo "<td title=\"".$stimme['name']."\" class=\"mitglied pt-stimm-stimme ".$stimme['class']."\">";
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
