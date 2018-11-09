function openmenu() {	
	var x = document.getElementById("navelements");
	if (x.className === "components")
	{
		x.className += "opennav";
		$(".menu-open").replaceWith('<i class="fas fa-times menu-open" onclick="openmenu()"></i>');
	}
	else
	{
		x.className = "components";
		$(".menu-open").replaceWith('<i class="fas fa-bars menu-open" onclick="openmenu()"></i>');
	}
}

$ ( document ).ready(function() {

	if($(window).width() >= 900) //non-mobile size
	{
		$('.menu-open').removeClass("fas fa-bars");
	}
	else
	{
		$('.menu-open').addClass("fas fa-bars ");
	}

});

$( window ).resize(function() {
	if($(window).width() >= 900) //non-mobile size
	{
		$('.menu-open').removeClass("fas fa-bars");
	}
	else
	{
		$('.menu-open').addClass("fas fa-bars ");
	}
});