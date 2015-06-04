<?php
/*
Plugin Name: Social Counter Dashboard Widget
Description: This widget shows the count of Facebook, Twitter, Google Plus activities for your domain.
Version: 1.0
Author: David Geresdi
Author URI: http://www.davidgeresdi.com/
License: GPL2
*/

if( !class_exists( 'SocialCount_DashboardWidget') ) {
	class SocialCount_DashboardWidget {
		function socialcount_dashboard_widget() {				
				$siteurl = get_bloginfo('url');
				
				 function get_fb_likes($url)
				 {
				   $query = "select total_count,like_count,comment_count,share_count,click_count from link_stat where url='{$url}'";
				   $call = "https://api.facebook.com/method/fql.query?query=" . rawurlencode($query) . "&format=json";

				   $ch = curl_init();
				    curl_setopt($ch, CURLOPT_URL, $call);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    $output = curl_exec($ch);
				    curl_close($ch);
				    return json_decode($output);
				 }

				 function get_tweets($url) {
				 
					 $json_string = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
					 $json = json_decode($json_string, true);
					 return intval( $json['count'] );
				 }

				 function get_plusones($url) {
				 
					 $curl = curl_init();
					 curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
					 curl_setopt($curl, CURLOPT_POST, 1);
					 curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
					 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					 curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
					 $curl_results = curl_exec ($curl);
					 curl_close ($curl);
				 
					 $json = json_decode($curl_results, true);
				 
					 return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
				 }

				 $fb_likes = reset( get_fb_likes($siteurl) );
				 
				 print('
				 <table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="33%" align="center" valign="top">							
							 <img src="'.plugin_dir_url( __FILE__ ).'/images/facebook.png" width="68" /><br />
							 <strong>Likes:</strong> '.$fb_likes->like_count.'<br />							 
							 <strong>Shares:</strong> '.$fb_likes->share_count.'	 
						</td>
						<td width="33%" align="center" valign="top">
							<img src="'.plugin_dir_url( __FILE__ ).'/images/twitter.png" width="68" /><br />
							<strong>Tweets:</strong><br />'.get_tweets($siteurl).'
						</td>
						<td width="33%" align="center" valign="top">
							 <img src="'.plugin_dir_url( __FILE__ ).'/images/gplus.png"  width="68" /><br />
						     <strong>+1\'s:</strong><br />'.get_plusones($siteurl).'
						</td>
					</tr>
				 </table>
				 ');
				 
		}

		function socialcount_add_dashboard_widget() {
			wp_add_dashboard_widget( 'socialcount-admin-widget', 'Activity on Social Media Platforms for '.$_SERVER['HTTP_HOST'], array( 'SocialCount_DashboardWidget', 'socialcount_dashboard_widget' ) );
		}		
	}
	add_action( 'wp_dashboard_setup', array( 'SocialCount_DashboardWidget', 'socialcount_add_dashboard_widget' ) );
}
?>
