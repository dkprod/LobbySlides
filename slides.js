
var lastUpdate = "";

var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
		var myObj = JSON.parse(this.responseText);
		if((lastUpdate != this.responseText) && ("items" in myObj)) {
			lastUpdate = this.responseText;
			
			afterLoad(myObj);
		} else {
			console.info("Same Slides");
	
			if("refresh" in myObj) {
				console.info("Refreshing in "+(myObj.refresh/1000)+" seconds.")
				setTimeout(function(){ LoadSlides(); }, (myObj.refresh));
			}
		}
    }
};

var wHeight = $(window).height();

$(window).on('resize', function (){
	var item = $('.carousel-item');
	wHeight = $(window).height();
	item.height(wHeight);
});

function LoadSlides() {
	console.info("Loading Slides");
	xmlhttp.open("GET", "slides.php", true);
	xmlhttp.send();
}

$('.carousel').carousel({
	interval: 10000
})

LoadSlides();

function afterLoad(myObj) {
	console.info("Got New Slides");
	$('.carousel-inner').empty();

	var mySlides = myObj.items;
	var currentSlide = Math.floor((Math.random() * mySlides.length));
	for(var i=0 ; i< mySlides.length ; i++) {
		var active = (currentSlide == i) ? " active" : "";
		$('<div class="carousel-item'+active+'"><img src="'+mySlides[i]+'"></div>').appendTo('.carousel-inner');
	}

	$('.carousel img').each(function() {
		var src = $(this).attr('src');
		var color = $(this).attr('data-color');
		$(this).parent().height(wHeight);
		$(this).parent().addClass('full-screen');
		$(this).parent().css({
			'background-image' : 'url("' + src + '")',
			'background-color' : color
		});
		$(this).remove();
	});
	
	if("interval" in myObj) {
		console.info("Interval is "+(myObj.interval/1000)+" seconds.")
		const options = $(".carousel").data()['bs.carousel']["_config"];
		options.interval = myObj.interval;
	}
	
	if("refresh" in myObj) {
		console.info("Refreshing in "+(myObj.refresh/1000)+" seconds.")
		setTimeout(function(){ LoadSlides(); }, (myObj.refresh));
	}
}