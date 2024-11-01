<?php
/**
* Plugin Name: Super Host Speed Benchmark
* Plugin URI: https://superhostspeed.com/
* Description: Test and benchmark the speed of your hosting provider, based on the speed of their mysql database, which tends to be the main cause of Wordpress being slow on some hosts. A score of less than 40 is bad and a score of more than 100 is very good. Scores will be reported to our server in future versions so you can compare speeds with others. See Tools/Speed Benchmark
* Version: 0.6.7
* License: GPL2+
* Copyright 2019 Anthony Walker
* Author: Anthony Walker
* Author URI:  https://www.quickwpsite.com/
**/


/******/


add_action( 'admin_menu', 'shsb_add_admin_menu' );
add_action( 'admin_init', 'shsb_settings_init' );




function shsb_add_admin_menu(  ) { 

	add_options_page( 'super-host-speed-benchmark', 'Super Host Speed Benchmark', 'manage_options', 'super-host-speed-benchmark', 'shsb_options_page' );

}


function shsb_settings_init(  ) { 

	register_setting( 'pluginPage', 'shsb_settings' );

	add_settings_section(
		'shsb_pluginPage_section', 
		__( 'Settings', 'wordpress' ), 
		'shsb_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'shsb_text_field_0', 
		__( 'Hosting provider name', 'wordpress' ), 
		'shsb_text_field_0_render', 
		'pluginPage', 
		'shsb_pluginPage_section' 
	);

	add_settings_field( 
		'shsb_text_field_1', 
		__( 'Hosting provider package', 'wordpress' ), 
		'shsb_text_field_1_render', 
		'pluginPage', 
		'shsb_pluginPage_section' 
	);

	add_settings_field( 
		'shsb_checkbox_field_2', 
		__( 'DO NOT send benchmark stats to us for comparison', 'wordpress' ), 
		'shsb_checkbox_field_2_render', 
		'pluginPage', 
		'shsb_pluginPage_section' 
	);


}


function shsb_text_field_0_render(  ) { 

	$options = get_option( 'shsb_settings' );
	?>
	<input type='text' name='shsb_settings[shsb_text_field_0]' value='<?php echo $options['shsb_text_field_0']; ?>'>
	<?php

}


function shsb_text_field_1_render(  ) { 

	$options = get_option( 'shsb_settings' );
	?>
	<input type='text' name='shsb_settings[shsb_text_field_1]' value='<?php echo $options['shsb_text_field_1']; ?>'>
	<?php

}


function shsb_checkbox_field_2_render(  ) { 

	$options = get_option( 'shsb_settings' );
	?>
	<input type='checkbox' name='shsb_settings[shsb_checkbox_field_2]' <?php checked( $options['shsb_checkbox_field_2'], 1 ); ?> value='1'>
	<?php

}


function shsb_settings_section_callback(  ) { 

	echo __( '', 'wordpress' );

}


function shsb_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Super Host Speed Benchmark</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}



/********/

// Hook for adding admin menus
add_action('admin_menu', 'shsb_add_pages');

function shsb_calculate_score()
{
    global $wpdb,$ttfb;
    $charset_collate = $wpdb->get_charset_collate();
//$wpdb->show_errors();

$table_name=$wpdb->prefix ."speedtest202";
    $sql="CREATE TABLE `$table_name` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `priority` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`task_id`),
  KEY `idx` (`priority`)
)  $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );


$wpdb->get_results("delete from $table_name");

    $ictr=0;
    $t=time();
    while (time()-$t<5)
    {
        $ictr++;
        //echo ".";
        $sheet = $wpdb->get_results("insert into $table_name (title,status,priority,description) values ('test',1,$ictr,'description')");
        
    }
    $time_start = microtime(TRUE);
   $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    if ($count<$ictr) echo "WARNING: Count does not match $ictr <> $count <br>";
    $t=time();
    $rctr=0;
    while (time()-$t<5)
    {
         $r=mt_rand(0,$count);
         $sheet = $wpdb->get_results("select task_id from $table_name where priority=$r");
         $rctr++;
    }
    $time_end = microtime(TRUE);
    $tm = $time_end - $time_start;
    
    $wpdb->get_results("delete from $table_name");
    
   /* 
  $time_start = microtime(TRUE); 
   
    $pi = 4; $top = 4; $bot = 3; $minus = TRUE;
$accuracy = 1000000;

for($i = 0; $i < $accuracy; $i++)
{
  $pi += ( $minus ? -($top/$bot) : ($top/$bot) );
  $minus = ( $minus ? FALSE : TRUE);
  $bot += 2;
}

$time_end = microtime(TRUE);
$time = $time_end - $time_start;

//print "Pi ~=: " . $pi. " time $time";

*/
    
    //$dscore=$ictr/1000;
    //$score=$ictr/500+($tm*20);
    $s1=$ictr/1000;
    $s2=($rctr/1000);
    $score=$s1+$s2;
    echo "Score: $score  | inserts in 5 seconds:$ictr | reads in 5 seconds: $rctr";
    //$score=$tm;
    
    $options = get_option( 'shsb_settings' );
    
    
    //get_admin_url
    
   
    
  
    
    
    
    $args = array(
        'number'     => 1,
        'offset'          => 0,
        'orderby'         => 'post_date',
        'order'           => 'DESC',
        'post_status'     => 'publish' );
    $sorted_posts = get_posts( $args );
    $url = get_permalink($sorted_posts[0]->ID);
    
    if (!$url)
    {
        $args = array(
        'numberposts'     => 1,
        'offset'          => 0,
        'orderby'         => 'post_date',
        'order'           => 'DESC',
        'post_status'     => 'publish' );
    $sorted_posts = get_posts( $args );
    $url = get_permalink($sorted_posts[0]->ID);
    }
    
    
    
    $ttfb=shsb_get_ttfb($url);
    echo " | ttfb: $ttfb";
    
    
    if ($options['shsb_checkbox_field_2']==0)
    {
    $plugin_data = get_plugin_data( __FILE__ );
    $v = $plugin_data['Version'];
    $url="https://superhostspeed.com/benchmark.php?score=$score&h=".urlencode($options['shsb_text_field_0'])."&o=".urlencode($options['shsb_text_field_1'])."&host=".gethostname()."&ip=".$_SERVER['SERVER_ADDR']."&httphost=".$_SERVER['HTTP_HOST']."&version=$v&w=$ictr&r=$rctr&ttfb=$ttfb";
    //echo $url;
    $r=file_get_contents($url);
    }

return $score;    
    
}

// action function for above hook
function shsb_add_pages() {
       // Add a new submenu under Tools:
    add_management_page( __('Speed Benchmark','menu-test'), __('Speed Benchmark','menu-test'), 'manage_options', 'speedbenchmark', 'shsb_tools_page');
    
}
// shsb_tools_page() displays the page content for the Test Tools submenu

function shsb_get_ttfb($url)
{
    
     if (!function_exists('curl_version')) return 99999;
    
   
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_NOBODY, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
//var_dump($info);
//echo "<hr>\n";
return $info['starttransfer_time'];
}

function shsb_tools_page() {
   
    global $wpdb;
    
    $shsb_score_time=get_option( "shsb_score_time",0);
    $shsb_score_time=0;
    if ($shsb_score_time==0 || time()-$shsb_score_time>60*60*12)
    {
    
    echo "<h2>" . __( 'Calculating benchmark....', 'menu-test' ) . "</h2>";
    
    $score=shsb_calculate_score();

    add_option( 'shsb_score', $score, '', 'no' );
    add_option( 'shsb_score_time', time(), '', 'no' );
    }
    else
    {
        $score=get_option( "shsb_score",0);
    }
    
    
    $purl= plugins_url();
   echo <<<EOT
   
   <script src='$purl/super-host-speed-benchmark/gauge.min.js'></script>
   
   <style>
  
   #gauge-txt{
 
  text-align: center; font-size: 2em; font-weight: bold;
  color: black; font-family: 'Amaranth', sans-serif;
}
</style>

 <div id="preview" align="center">

  	<canvas width=380 height=200 id="gauge" align="center"></canvas>
  	<div id="gauge-txt" align="center"></div>
   <div>0-50 Slow  ; 50-70 OK ; 71+ Excellent</div>
 
  </div>

<script>
 var opts = {
  angle: -0.2, // The span of the gauge arc
  lineWidth: 0.2, // The line thickness
  radiusScale: 1, // Relative radius
  pointer: {
    length: 0.6, // // Relative to gauge radius
    strokeWidth: 0.035, // The thickness
    color: '#000000' // Fill color
  },
  limitMax: false,     // If false, max value increases automatically if value > maxValue
  limitMin: false,     // If true, the min value of the gauge will be fixed
  colorStart: '#6FADCF',   // Colors
  colorStop: '#8FC0DA',    // just experiment with them
  strokeColor: '#E0E0E0',  // to see which ones work best for you
  generateGradient: true,
  highDpiSupport: true,     // High resolution support
  
  staticLabels: {
  font: "10px sans-serif",  // Specifies font
  labels: [10, 20, 30, 40, 50, 60, 70 ,80 , 90, 100,150,200],  // Print labels at these values
  color: "#000000",  // Optional: Label text color
  fractionDigits: 0  // Optional: Numerical precision. 0=round off.
},

staticZones: [
   {strokeStyle: "#FF0000", min: 0, max: 50}, // Red 
   {strokeStyle: "#0000FF", min: 51, max: 70}, // Blue
   {strokeStyle: "#00FF00", min: 71, max: 200} // Green
   
],

};
var target = document.getElementById('gauge'); // your canvas element
var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
gauge.maxValue = 200; // set max gauge value
gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
gauge.animationSpeed = 32; // set animation speed (32 is default value)
gauge.set($score); // set actual value
//gauge.setTextField($score);
gauge.setTextField(document.getElementById("gauge-txt"));
  
</script>

<h2>
<a href='https://superhostspeed.com/' style="color:blue;text-align:center" target='_blank'>View ranking table<a>
</h2>

EOT;

	
    
    
    
}



function shsb_cron_run()
{
    
    $shsb_score_time=get_option( "shsb_score_time",0);
    if ($shsb_score_time==0 || time()-$shsb_score_time>60*60*11)
    {
    $score=shsb_calculate_score();

    add_option( 'shsb_score', $score, '', 'no' );
    add_option( 'shsb_score_time', time(), '', 'no' ); 
    }
    
}

function shsb_activate() {

    // Activation code here...
         
    if( !wp_next_scheduled( 'shsb_cron' ) )
    {
    wp_schedule_event( time(), 'hourly', 'shsb_cron' );
    }

}

add_action ('shsb_cron',  'shsb_cron_run' );

function shsb_deactivate()
{
    
wp_clear_scheduled_hook('shsb_cron');
delete_option( 'shsb_score' );
delete_option( 'shsb_score_time' );

}

function shsb_uninstal()
{
     global $wpdb;
     
     $table_name=$wpdb->prefix ."speedtest202";
     $the_removal_query = "DROP TABLE IF EXISTS {$table_name}";
     $wpdb->query( $the_removal_query );

}

register_activation_hook( __FILE__, 'shsb_activate' );



register_deactivation_hook( __FILE__, 'shsb_deactivate' );

register_uninstall_hook(__FILE__, 'shsb_uninstall');

add_filter( 'auto_update_plugin', '__return_true' );
 
?>