<?php
/*
Plugin Name: TwentyTwenty Proton
Plugin URI: http://references.zc.dtdns.net
Description: Before/After Gallery using a custom version of jquerys twentytwenty plugin
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
		<link href='/wp-content/plugins/twentytwenty-proton/bower_components/twentytwenty/css/twentytwenty.css' rel='stylesheet' type='text/css'>
		<script src='/wp-content/plugins/twentytwenty-proton/bower_components/twentytwenty/js/jquery.event.move.js'></script>
		<script src='/wp-content/plugins/twentytwenty-proton/bower_components/twentytwenty/js/jquery.twentytwenty.js'></script>

		<script src='/wp-content/plugins/twentytwenty-proton/bower_components/Proton/build/proton-1.0.0.min.js'></script>

		<link href='/wp-content/plugins/twentytwenty-proton/twentytwenty-proton/css/twentytwenty-proton.css' rel='stylesheet' type='text/css'>
		<script src='/wp-content/plugins/twentytwenty-proton/twentytwenty-proton/js/jquery.twentytwenty-proton.js'></script>
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
		//request images by id
		$images = twentytwentyproton_get_images($attrs["ids"]);
		//check if there are exactly two images
		if (count($images) == 2)
		{
			//generate unique id for dom elements
			$uniqid = uniqid();
			//initialize markup and javascript
			$content = "
				<script type='text/javascript'>
					window.addEventListener('load',function(){
						jQuery('#".$uniqid."').show().twentytwentyproton({fullscreen:false});
					});
				</script>
				<div id='".$uniqid."' style='display:none; width:100%;'>
					<img src='".$images[0]["guid"]."' />
					<img src='".$images[1]["guid"]."' />
				</div>
			";
		}
	}

	return($content);
}

/**
 * Load Images by id from database
 * @param type $ids 
 * @return type
 */
function twentytwentyproton_get_images($ids)
{
	//sainitize input data
	$ids = mysql_real_escape_string($ids);
	//prepare images array
	$images = array();
	//construct sql query
	$query = "
		SELECT *
		FROM wp_posts
		WHERE
			id IN (".$ids.")
		ORDER BY
			(id=".implode(") DESC,(id=",explode(",",$ids)).") DESC
		LIMIT 2
	";
	//execute sql query
	$res = mysql_query($query);
	//loop query results
	while($node = mysql_fetch_assoc($res))
	{
		//push query results to images array
		array_push($images,$node);
	}
	//return images array
	return($images);
}

//hook to modidy wordpress backend image editor
add_action('admin_print_scripts','twentytwentyproton_admin_print_scripts');
//**
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