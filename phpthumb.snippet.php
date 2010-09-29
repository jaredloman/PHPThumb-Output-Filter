<?php
  // PHPThumb Output Filter
  //
  // By Jared Loman a.k.a jtech - http://www.jaredloman.com
  //
  // GitHub: http://github.com/jaredloman/PHPthumb-Output-Filter
  //
  // Revision by Djordje Dimitrijev (dj13 on Modx forum)
  //
  // Revision Notes:
  //
  // Choose the output from a script:
  // 1. Full image tag.
  // 2. Only src value (additional attributes will not be used).
  // Added possibility use or not high security password from phpThumb config.
  // Possibility to add as much as you want default image attributes through the array.     
  // New way to add attributes in filter:
  // attribute_html_name || attribute_value
  //
  // Version 0.6 beta
  //
  // Current Version Notes:
  // Fixed the removal of source only option (oops!)
  // Added support to make image urls generated W3C compliant 
  //
  // Previous Version Notes:
  // v.0.5: Now utilizes the default phpthumb included with Revo!
  // v.0.5: I'm not sure if the high security options continue to be functional.
  // v.0.4: Added Source only option (by DJ13)
  // v.0.3: Added possibility use or not high security password from phpThumb config.
  // v.0.3: Possibility to add as much as you want default image attributes through the array.     
  // v.0.3: New way to add attributes in filter:
  // v.0.3: attribute_html_name || attribute_value
  //
  // v.0.2: Added array to handle "class" & "alt" options with defaults.
  // v.0.2: Added a space removal fool-proof in case someone trys to skip the class and add an alt.
  // v.0.2: Added Readme
  // v.0.2: Still haven't tested all phpthumb modifiers.
  //
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
  //
  // IMPORTANT:
  // TV Output Type MUST be "TEXT"!!
  //
  // Examples:
  //
  // Filter full image tag: [[*mytvImage:phpthumb=`&w=100&h=50&zc=1,class||myClass,title||myTitle,style||border: 1px solid #333`]]
  // Filter only src output: [[*mytvImage:phpthumb=`&w=100&h=50&zc=1,onlysrc||1`]]
  //
  // Snippet full image tag: [[phpthumb? &input=`files/myImage.jpg` &options=`&w=100&h=50&zc=1,class||myClass,title||myTitle`]]
  // Snippet only src output v1 (best way if You want only src): [[phpthumb? &input=`files/myImage.jpg` &options=`&w=100&h=50&zc=1` &onlysrc=`1`]]
  // Snippet only src output v2: [[phpthumb? &input=`files/myImage.jpg` &options=`&w=100&h=50&zc=1,onlysrc||1`]]
  //
 
  $phpthumb_path = 'connectors/system';
  
  // Define attribute name - value separator
  $attribute_sep = '||';

  $base = $modx->config['base_url'];

  // Default array of attributes with key name like html attribute name. Lowercase for keys.
  // Examples:
  // $attributes['style'] = 'border:1px solid #ccc;'
  // $attributes['title'] = 'My Title';
  // $attributes['class'] = 'myClass';
  //
  // IMPORTANT! If You set:
  // $output_onlysrc = 1;
  // all returns from snippet will be only src

  $output_onlysrc = false;

  $error = "You need to specify at least one option, otherwise this filter is pointless!";
  if(!empty($options)){$phpthmbOptions = explode(",", $options);}else{$output = $error; return $output;}

  // Include phpThumb config file.
  // require_once($modx->getOption('base_path') . $phpthumb_path . '/phpThumb.config.php');

  // Not sure if this is even required anymore?
  if(isset($PHPTHUMB_CONFIG['high_security_password'])) $_SESSION['high_security_password'] = $PHPTHUMB_CONFIG['high_security_password'];
  if(isset($PHPTHUMB_CONFIG['high_security_enabled'])) $_SESSION['high_security_enabled'] = $PHPTHUMB_CONFIG['high_security_enabled'];

  $att_out = '';
  $src = '';

  // From snippet call
  if($onlysrc == 1) {
    $output_onlysrc = 1;
  }

  // If onlysrc is set in snippet call or as default skip secound explode and adding rest of attributes
  if($output_onlysrc !=1 ) {

    foreach($phpthmbOptions as $k => $v){

      // Skip phpThumb modifications
      if($k==0) continue;

      // Explode rest of options
      $m = explode($attribute_sep, $v);  

      $m_att = strtolower(trim($m[0]));

      // Search for onlysrc in options
      if($m_att=='onlysrc' && trim($m[1])==1) {
        $output_onlysrc = 1;
        break;
      }

      // Join attributes except src
      $att_out .= ' '.$m_att.'="'.trim($m[1]).'"';

      // Remove default attribute if exist in phpThumb options
      if(isset($attributes[$m_att])) unset($attributes[$m_att]);
    }

    // Add rest of unused default attributes
    if(isset($attributes)) {
        foreach($attributes as $k => $v){
            $att_out .= ' '.$k.'="'.$v.'"';
        }
    }
  }

  // Image url + phpthumb modifications
  $src = 'src=' . $base . $input . htmlspecialchars($phpthmbOptions[0]);

  // If high security is enabled define hash
  if($_SESSION['high_security_enabled']){
    $output = $base . $phpthumb_path . '/phpthumb.php?' . $src . '&hash=' . md5($src.$_SESSION['high_security_password']);
  } else {
    $output = $base . $phpthumb_path . '/phpthumb.php?' . $src ;
  }

  // Final join and output only src or full image tag
  if($output_onlysrc ==1) {
    return $output;
  } else {
    $output = '<img src="' . $output .'"'. $att_out.'>';
    return $output;
  }
?>
