<?php
/*
Plugin Name: Social Counter Dashboard Widget
Description: This widget shows the count of Facebook, Twitter, Google Plus activities for your domain.
Version: 0.4
Author: David Geresdi
Author URI: http://www.davidgeresdi.com/
License: GPL2
*/

if( !class_exists( 'SocialCount_DashboardWidget') ) {
	class SocialCount_DashboardWidget {
		function socialcount_dashboard_widget() {
				$siteurl = 'http://www.'.$_SERVER['HTTP_HOST'].'/';
				
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

				 //FOR FUTURE FUNCTIONS ONLY
				 //echo 'CLICKS: '.$fb_likes->click_count.'<br />';
				 
				 print('
				 <table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="33%" align="center" valign="top">							
							 <strong><font color="blue">Activities on Facebook:</font><br /></strong>
							 Total: '.$fb_likes->total_count.'<br />
							 Likes: '.$fb_likes->like_count.'<br />
							 Comments: '.$fb_likes->comment_count.'<br />
							 Shares: '.$fb_likes->share_count.'	 
						</td>
						<td width="33%" align="center" valign="middle">
							<strong><font color="red">Total number of tweets:</font><br /></strong>
							'.get_tweets($siteurl).'
						</td>
						<td width="33%" align="center" valign="middle">
							 <strong><font color="green">Google +1 adds:</font></strong><br />
						     '.get_plusones($siteurl).'
						</td>
					</tr>
				 </table>
				 ');
				 
		}

		function socialcount_add_dashboard_widget() {
			wp_add_dashboard_widget( 'socialcount-admin-widget', 'Domain Social Activity Counter ('.$_SERVER['HTTP_HOST'].')', array( 'SocialCount_DashboardWidget', 'socialcount_dashboard_widget' ) );
		}		
	}
	add_action( 'wp_dashboard_setup', array( 'SocialCount_DashboardWidget', 'socialcount_add_dashboard_widget' ) );
}
?>
