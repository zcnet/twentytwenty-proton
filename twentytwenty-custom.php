<?php
/*
Plugin Name: TwentyTwenty Proton
Plugin URI: http://references.zc.dtdns.net
Description: Before/After Gallery Plugin for Wordpress using Proton
Version: 1.0
Author: m.Schreiber
Author URI: http://references.zc.dtdns.net
*/

/**
 * Globald Debug Function
 * @param mixed $val value to output
 * @return null
 */
function pr($val)
{
	echo "<pre>";
	print_r($val);
	echo "</pre>";
}

//Add CSS and JS by wp_head hook
add_action('wp_head', 'twentytwentyproton_head');
/**
 * Output CSS and JS Requirements - hooked to wp_head
 * @return none
 */
function twentytwentyproton_head()
{
	echo "
		<link href='".plugin_dir_url(__FILE__)."bower_components/twentytwenty/css/twentytwenty.css' rel='stylesheet' type='text/css'>
		<script src='".plugin_dir_url(__FILE__)."bower_components/twentytwenty/js/jquery.event.move.js'></script>
		<script src='".plugin_dir_url(__FILE__)."bower_components/twentytwenty/js/jquery.twentytwenty.js'></script>

		<script src='".plugin_dir_url(__FILE__)."bower_components/Proton/build/proton-1.0.0.min.js'></script>

		<link href='".plugin_dir_url(__FILE__)."twentytwenty-proton/css/twentytwenty-proton.css' rel='stylesheet' type='text/css'>
		<script src='".plugin_dir_url(__FILE__)."twentytwenty-proton/js/jquery.twentytwenty-proton.js'></script>
	";
}

//Override Gallery Shortcode
remove_shortcode('gallery');
add_shortcode("gallery", "twentytwentyproton");
/**
 * Gallery Shortcode Function
 * @param array $attrs Shortcode Attributes
 * @return none
 */
function twentytwentyproton($attrs)
{
	//fallback to previous gallery shortcode
	if ($attrs["link"] != "twentytwentyproton")
	{
		return(gallery_shortcode($attrs));
	}
	//check if image ids are set
	if (!empty($attrs["ids"]))
	{
		//explode ids and make sure there are exactly two
		$ids = explode(",",$attrs["ids"]);
		if (count($ids) == 2)
		{
			pr($attrs);
			//get images info
			$images = array(
				wp_get_attachment_image_src($ids[0],$attrs["size"]),
				wp_get_attachment_image_src($ids[1],$attrs["size"]),
			);
			//generate unique id for dom elements
			$uniqid = uniqid();
			//initialize markup and javascript
			$content = "
				<script type='text/javascript'>
					window.addEventListener('load',function(){
						jQuery('#".$uniqid."').show().twentytwentyproton({
							particle:'".plugin_dir_url(__FILE__)."twentytwenty-proton/img/particle.png',
							fullscreen:false
						});
					});
				</script>
				<div id='".$uniqid."' style='display:none; width:".max($images[0][1],$images[1][1])."px;'>
					<img src='".$images[0][0]."' width='".$images[0][1]."' height='".$images[0][2]."' />
					<img src='".$images[1][0]."' width='".$images[1][1]."' height='".$images[1][2]."' />
				</div>
			";
		}
	}
	return($content);
}

//hook to modidy wordpress backend image editor
add_action('admin_print_scripts','twentytwentyproton_admin_print_scripts');
/**
 * Add "Before/After"-Option to Wordpress Media Gallery link Options
 * @return none
 */
function twentytwentyproton_admin_print_scripts()
{
	echo '
		<script type="text/javascript">
			window.addEventListener("load",function(){
				if(wp.media.view.Settings.Gallery){
					wp.media.view.Settings.Gallery = wp.media.view.Settings.extend({
						className: "collection-settings gallery-settings",
						template: wp.media.template("gallery-settings"),
						render:	function() {
							wp.media.View.prototype.render.apply( this, arguments );
							var $s = this.$("select.link-to");
							if(!$s.find("option[value=\"twentytwentyproton\"]").length)
							{
								$s.append("<option value=\"twentytwentyproton\">TwentyTwentyProton</option>");
							}
							// Select the correct values.
							_( this.model.attributes ).chain().keys().each( this.update, this );
							return this;
						}
					});
				}
			});
		</script>
	';
}
?>