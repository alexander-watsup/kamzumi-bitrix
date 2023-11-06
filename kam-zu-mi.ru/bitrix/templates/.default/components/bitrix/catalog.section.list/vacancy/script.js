$(document).ready(function() {
	$("html").on( "click", ".vacancyNameBlock", function() {
		$(this).find(".arrow").toggleClass("opend");
		$(this).next().toggle('slow');
	});
});