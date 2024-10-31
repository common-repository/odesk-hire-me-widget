<?php
/*
Plugin Name: Odesk Hire Me Widget
Description: oDesk currently discontinue thier oDesk Hire Me Widget. this plugin is just an alternative, not an official.
Author: Dariel P Adipura
Author URI: http://mangdariel.web.id/
Version: 1.0
*/

function oDeskHireMe($atts, $content){
	ob_start();
	$options = shortcode_atts(array(
		'profile_id'=>'#',
		'width'=>'-1'
	), $atts);
	if($options['profile_id'] == '#'){
		return 'Invalid oDesk Profie ID';
	}else{
		$url = 'https://www.odesk.com/users/'.$options['profile_id'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$doc = new DOMDocument();
		@$doc->loadHTML($response);

		$xpath = new DOMXpath($doc);
		$star = getNodeValue($xpath, '//i[@class="oIcon oStarIcon oIconStats"]/following-sibling::*[1]');
		$name = getNodeValue($xpath, '//hgroup[@class="oSummaryHeading"]/h1[@class="oH1Huge"]');
		$title = getNodeValue($xpath, '//hgroup[@class="oSummaryHeading"]/h1[@class="oH2High"]');
		$rating = getNodeValue($xpath, '//i[@class="oIcon oCoinsIcon oIconStats"]/following-sibling::*[1]');
		$jobs = getNodeValue($xpath, '//i[@class="oIcon oBriefcaseIcon oIconStats"]/following-sibling::*[1]');
		$hours = getNodeValue($xpath, '//i[@class="oIcon oClockIcon oIconStats"]/following-sibling::*[1]');
		$avatar = getImgSrc($xpath, '//div[@class="oImg oAvatarImg"]/img[@class="oAvatarDecorator"]');
		?>
		<div id="odesk-widget" style="<?php echo (int)$options['width'] > -1 ? 'width: '.$options['width'].'px':''; ?>">
			<div id="odesk-avatar">
				<img src="<?php echo $avatar; ?>" />
			</div>
			<div id="odesk-summary">
				<h3><?php echo $name; ?></h3>
				<h3 id="odesk-title"><?php echo $title; ?></h3>
				<div id="odesk-star"><?php echo $star; ?></div>
				<div id="odesk-info">
					<table>
						<tr>
							<td width="30%">Rate</td>
							<td width="5%">:</td>
							<td><?php echo $rating; ?>/hr</td>
						</tr>
						<tr>
							<td width="30%">Jobs</td>
							<td>:</td>
							<td><?php echo $jobs; ?></td>
						</tr>
						<tr>
							<td width="30%">Hours</td>
							<td>:</td>
							<td><?php echo $hours; ?></td>
						</tr>
					</table>
				</div>
				<dic class="ocl"></dic>
			</div>
			<div class="ocl"></div>
			<a href="<?php echo $url; ?>" target="_blank" id="odesk-hire-me">
				<img src="<?php echo plugins_url('assets/images/odesk-logo-new.svg', __FILE__); ?>" alt="odesk-logo" /> Hire me
			</a>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('odesk_hire_me', 'oDeskHireMe');

function oDeskHead(){
	echo '<link rel="stylesheet" type="text/css" href="'.plugins_url('assets/css/odesk.css', __FILE__).'">';
	echo '<link href="http://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">';
}
add_action('wp_head', 'oDeskHead');

function getNodeValue($xpath, $query){
	$node = $xpath->query($query);
	if($node->length > 0){
		return $node->item(0)->nodeValue;
	}
	return '';
}

function getImgSrc($xpath, $query){
	$node = $xpath->query($query);
	if($node->length > 0){
		return $node->item(0)->getAttribute('src');
	}
	return '';
}