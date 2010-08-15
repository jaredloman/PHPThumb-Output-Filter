<?php
	// PHPThumb Output Filter - The Dumb way (I'm sure)
	//
	// By Jared Loman a.k.a jtech - http://www.jaredloman.com
	// Version 0.2 beta
	//
	// Current Version Notes: 
	// Added array to handle "class" & "alt" options with defaults.
	// Added a space removal fool-proof in case someone trys to skip the class and add an alt. 
	// Added Readme
	// Still haven't tested all phpthumb modifiers.
	//
	// Previous Version Notes:
	// v.0.1: I haven't tested all of the phpthumb modifiers, but I think they should work
	
	// ReadMe:
	//
	// Notes:
	// I created the PHPThumb output filter so that I could have some flexibility with my images that an output modifier
	// couldn't offer me. This output filter is essentially useless for anything other than images (Obviously!), but  I
	// figured I'd at least mention this.
	//
	// This does not support PHPThumbs ability to create new images from nothing. It would be kind of silly to use a 
	// TV and Output filter for this purpose anyhow.
	//
	// INSTRUCTIONS:
	// Step 1.) Create a TV with an input type of "IMAGE" and an output type of "TEXT".
	// Step 2.) Add an image to the TV.
	// Step 3.) Add your TV to your page content or template and modify in the format of the example.
	// 
	// OPTIONS: 
	// Option 1.) The first option is the phpthumb modifications. You can add any modifier that phpthumb supports. (AFIK)
	// Option 2.) Image Class (optional) - Supply a class for your image. Defaults to "phpthumb" if you don't put anything in.
	// Option 3.) Image Alt (optional) - Supply text for the alt tag. Defaults to "phpthumb" if you don't put anything in.
	//
	// IMPORTANT:
	// Options MUST be put in specified order! 
	// You Must specify a class if you would like a custom ALT. (e.g. [[*myTV:phpthumb=`&w=100,My Alt Text`]] ) will
	// return <img src=".." class="MyAltText" alt="phpthumb" />.. Sorry, it's just the nature of the beast.
	//
	// TV Output Type MUST be "TEXT"!!
	//
	// Example: [[*mytvImage:phpthumb=`&w=100&h=50&zc=1,imgClass,imgAlt`]]
	
	$base = $modx->config['base_url'];
	$error = "You need to specify at least one option, otherwise this filter is pointless!";
	
	if(!empty($options)){$phpthmbOptions = explode(",", $options);}else{$output = $error; return $output;}
	if(!isset($phpthmbOptions[1])){$phpthmbOptions[1] = "phpthumb";}else{$phpthmbOptions[1] = str_replace(" ", "", $phpthmbOptions[1]);}
	if(!isset($phpthmbOptions[2])){$phpthmbOptions[2] = "phpthumb";}

	$start = "<img src='assets/components/phpthumb/phpThumb.php?src=";
	$end = "' class='" . $phpthmbOptions[1] . "' alt='" . $phpthmbOptions[2] . "' />";
	// Build Image with specified options
	$output = "<img src='assets/components/phpthumb/phpThumb.php?src=";
	$output .= $base . $input;
	$output .= $phpthmbOptions[0];
	$output .= "' class='";
	$output .= $phpthmbOptions[1];
	$output .= "' alt='";
	$output .= $phpthmbOptions[2];
	$output .= "' />";
	return $output;
?>