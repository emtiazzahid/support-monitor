<?php

function getPMPlugins(){
	global $wpdb;
	$data = $wpdb->get_results( "SELECT * FROM `wp_plugin_monitor`" );

	return $data;
}

function issueCompareByTimeStamp($issue1, $issue2)
{
	if (strtotime($issue1['pubDate']) < strtotime($issue2['pubDate']))
		return 1;
	else if (strtotime($issue1['pubDate']) > strtotime($issue2['pubDate']))
		return -1;
	else
		return 0;
}