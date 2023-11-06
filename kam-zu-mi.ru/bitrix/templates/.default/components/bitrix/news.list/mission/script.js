$( document ).ready(function() {
	$("html").on( "click", ".mission-block .item", function() {
		$(this).toggleClass("hover");
	});
});