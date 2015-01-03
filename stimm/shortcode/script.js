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