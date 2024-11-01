=== Super Host Speed Benchmark ===
Contributors: wsec1
Tags: Speed,benchmark,performance,speed benchmark,speed test,slow
Requires PHP: 5.2.4
Requires at least: 4.6
Tested up to: 5.2.3
Stable tag: trunk

Test and benchmark the speed of your hosting provider, based on the speed of their mysql database, which tends to be the main cause of Wordpress being slow on some hosts. 

== Description ==

### Wordpress Speed Benchmark and Testing

Test and benchmark the speed of your hosting provider, based on the speed of their mysql database, which tends to be the main cause of Wordpress being slow on some hosts. A score of less than 40 is bad and a score of more than 100 is very good. Scores will be reported to our server in future versions so you can compare speeds with others. 


== Installation ==

=== Add/Upload Zip ===

1. Upload the `superhostspeedbenchmark.zip` file via the Add Plugin option
2. Activate the plugin
3. Go to "after activation" below.

=== Manually ===

1. Upload the `superhostspeedbenchmark` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "after activation" below.

=== After activation ===

1. Goto Tools/Speed Benchmark , the test will run immediately and show your score. It takes about 20 seconds.

== Screenshots ==

1. Result of a speed test

== Changelog ==
0.1 Initial Release

0.2 Added Gauge, settings and reporting for benchmark tables

0.3 Fixed SQL error in benchmark

0.4 Added caching machanism for score using wpcron

0.5 Change to bechmark formula to cater for Innodb vs MyIsam

0.6 Fixed error with table create not using prefix
0.6.1 Fixed select error where less inserts where done
0.6.2 Change to speed formula
0.6.3 Report version number and stats
0.6.4 Added ttfb calculation (calculated on latest blog post url)
0.6.5 ttfb calculation (calculated on latest page or blog post url)
0.6.6 Fix typo
0.6.7 Updated compatability


