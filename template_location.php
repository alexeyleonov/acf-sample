<?php 
/* Template Name: Location */
?>

<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="image/x-icon" />
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="image/x-icon" />

<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<!--[if (gte IE 6)&(lte IE 8)]>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/selectivizr.min.js"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
	
	/* Map settings */
	$map_api_key = get_option('options_api_key');
	if ( empty($map_api_key) ) {
		$map_api_key = 'AIzaSyDbx659witbbVO1GhuZPHWK-0mirnDsz1g';
	}
	$map_zoom = get_option('options_map_zoom');
	if ( empty($map_zoom) ) {
		$map_zoom = 16;
	}
	$map_center_lat = get_option('options_center_latitude');
	if ( empty($map_center_lat) ) {
		$map_center_lat = 47.363;
	}
	$map_center_lng = get_option('options_center_longitude');
	if ( empty($map_center_lng) ) {
		$map_center_lng = 8.787;
	}

	$points = array();
	$map_points = get_option('options_map_points');
	if ( !empty($map_points) ) {
		for ( $idx=0; $idx<$map_points; $idx++ ) {
			$point_info = array (
				'icon' => get_option('options_map_points_' . $idx . '_marker_image'),
				'title' => str_replace("'","\'",get_option('options_map_points_' . $idx . '_location_title')),
				'lat' => get_option('options_map_points_' . $idx . '_latitude'),
				'lng' => get_option('options_map_points_' . $idx . '_longitude')
			);
			array_push($points,$point_info);
		}
	}

?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=$map_api_key?>&sensor=false"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/infobox.js" ></script>
<script type="text/javascript">
	function initialize() {
		var mapOptions = {
			center: new google.maps.LatLng(<?=$map_center_lat?>,<?=$map_center_lng?>),
			zoom: <?=$map_zoom?>,
			mapTypeId: google.maps.MapTypeId.SATELLITE
        };
        var map = new google.maps.Map(document.getElementById("map-container"), mapOptions);
		
		/*			
		var myLatlng = new google.maps.LatLng(47.3609,8.7934);
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map
		});
		var myLatlng = new google.maps.LatLng(47.3593,8.7867);
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map
		});
		

		*/
		<?php if ( !empty($points) ) : foreach ( $points as $point ) : if ( !empty($point['lat']) && !empty($point['lng']) ) : ?>
			var myLatlng = new google.maps.LatLng(<?=$point['lat']?>,<?=$point['lng']?>);
			var marker = new google.maps.Marker({
				position: myLatlng,
			<?php if ( !empty($point['icon']) ) : $_img = wp_get_attachment_image_src( $point['icon'], 'full' ); ?>
				icon: "<?=$_img[0]?>",
			<?php endif; ?>			
				map: map
			});
			
			var myOptions = {
				content: "<div class='location-title'><?=$point['title']?></div>"
				,boxStyle: {
					marginTop: "-51px"
					,marginLeft: "45px"
				}
				,disableAutoPan: true
				,pixelOffset: new google.maps.Size(-25, 0)
				,position: myLatlng
				,closeBoxURL: ""
				,isHidden: false
				,pane: "mapPane"
				,enableEventPropagation: true
			};

			var ibLabel = new InfoBox(myOptions);
			ibLabel.open(map);

		<?php endif; endforeach; endif; ?>
		

	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div id="map-container"></div>

<div class="wrapper">
	
	<header id="header">
		<nav id="nav" class="white-bg">
			<?php wp_nav_menu( array('theme_location' => 'primary') ); ?>
		</nav>
		<div class="logo">
			<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" ><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png"/></a>
		</div>
	</header>

	<div id="content" class="location">
		
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>		

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>

	<?php endwhile; endif; ?>

	<?php

	$page_id = get_the_ID();
	$sidebar_contents = get_post_meta( $page_id, 'sidebar_contents', true );
	if ( !empty($sidebar_contents) ) 
		printf('<div class="sidebar">%s</div>', apply_filters('the_content', $sidebar_contents));

	?>

	</div><!-- #content -->
    
<?php get_footer(); ?>