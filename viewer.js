/**
 * See: http://www.css-101.org/articles/ken-burns_effect/css-transition.php
 */

/**
 * The idea is to cycle through the images to apply the "fx" class to them every n seconds. 
 * We can't simply set and remove that class though, because that would make the previous image move back into its original position while the new one fades in. 
 * We need to keep the class on two images at a time (the two that are involved with the transition).
 */

(function(){
 
	// url request 
	var request = new XMLHttpRequest();
	request.open('GET', 'http://localhost:8888/crawl/json.php', true);
	var data;

	var imgArray = [];
	var visibleImgArray = [];
	var clock;


// the third variable is to keep track of where we are in the loop
// if it is set to 1 (instead of 0) it is because the first image is styled when the page loads
  var images          = document.getElementById('slideshow').getElementsByTagName('img'),
      numberOfImages  = images.length,
      i               = 1;


	var slideshow = document.getElementById('slideshow');
	var imgString ="";
	var speed = 3000;

	request.onload = function() {
		if (request.status >= 200 && request.status < 400) {
		    // Success!
		    data = JSON.parse(request.responseText);
		    // console.dir(data);
		 	populateSlideShow();
		  } else {
		    // We reached our target server, but it returned an error

		 }
	};

	request.onerror = function() {
	  // There was a connection error of some sort
	};

	
	request.send();

	populateSlideShow = function () {

		// console.log(data.data.length);
		Array.prototype.forEach.call(data.data, function(el, i){
				imgURL = el.file.replace(/ /g, '');
				imgString  = "<img src='pics/"+imgURL+"'></img>";
				// console.log(imgString, "lalalaal");
				imgArray.push(imgString);
		});
		// big string with imgs;  
		// slideshow.innerHTML = imgString;
		initKenBurns();
	}

	initKenBurns = function () {


	// the third variable is to keep track of where we are in the loop
// if it is set to 1 (instead of 0) it is because the first image is styled when the page loads
 		images          = document.getElementById('slideshow').getElementsByTagName('img');
		numberOfImages  = imgArray.length;
    	// slideshow.innerHTML = imgArray[0];

		// we set the 'fx' class on the first image when the page loads
		// slideshow.getElementsByTagName('img')[0].className = "fx";
		// this calls the kenBurns function
		// you can increase or decrease this value to get different effects
		window.setInterval(kenBurns, speed);		

	}


	function kenBurns() {
		if(i==numberOfImages){ i = 0;}

			// console.log(i,numberOfImages,images);
			// slideshow.innerHTML = imgArray[i];
			$($($( "#slideshow img" )[0])[0]).removeClass("fx");


			$( "#slideshow" ).prepend( imgArray[Math.round(Math.random()*imgArray.length)] );
			 // console.log($( "#slideshow img" ).last()[0]);

			setTimeout(function(){  

					$($($( "#slideshow img" )[0])[0]).addClass("fx");
			 		// $($( "#slideshow img" ).last()[0]).addClass("fx");
			 }, 100);
			

			// console.log($($( "#slideshow img" )[1])[0]);
			// console.log("console 1: ", $( "#slideshow img" )[0],"console 2: ",$( "#slideshow img" )[1],"console 3: ", $( "#slideshow img" )[2]);

			if($("#slideshow img").length == 3 ) {

					$($($( "#slideshow img" )[2])[0]).remove();
			}
			// console.log($( "#slideshow" ).find("img"));
			// $("#slideshow").appendHtML( imgArray[i]);
			// images = document.getElementById('slideshow').getElementsByTagName('img');

			 // images[i-1].className = "fx";

			// images[i].className = "";

	
	// // we can't remove the class from the previous element or we'd get a bouncing effect so we clean up the one before last
	// // (there must be a smarter way to do this though)
	// 	  if(i===0){
	// 	  	images[numberOfImages-2].className = "";
	// 		// slideshow.removeChild(images[i]);
	// 	}
	// 	  if(i===1){ 
	// 	  	images[numberOfImages-1].className = "";
	// 	  	// slideshow.removeChild(images[i]);

	// 	}
	// 	  if(i>1){
	// 	   images[numberOfImages-2].className = "";
	// 	   // slideshow.removeChild(images[i]);


	// }
	  i++;

  }



})();




