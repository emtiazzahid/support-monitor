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

function getPMPluginSupportData($plugin, $hourBefore = 24){
	$url = 'https://wordpress.org/support/plugin/'.$plugin.'/feed';
	$args_for_get = [
		'timeout' => 20
	];

	$response = wp_remote_get( $url, $args_for_get );
	if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
		return [
			'issues' => [],
			'status' => 'Data Fetch Error',
		];
	}

	$body = wp_remote_retrieve_body($response);

	$xml = simplexml_load_string($body, null, LIBXML_NOCDATA);
	if (!$xml){
		return  [
			'issues' => [],
			'status' => 'Data Fetch Error',
		];
	}

	$ns = $xml->getNamespaces(true);

	$targetBeforeTime = ( new DateTime('NOW') )->setTimezone(new DateTimeZone('UTC'));
	$targetBeforeTime->sub(new DateInterval("PT".$hourBefore."H"));

	$items = $xml->channel->item;
	$pluginTitle = strval ($xml->channel->title);
	$issues = [];

	foreach ($items as $item){

		$doc = new DOMDocument();
		$doc->loadHTML($item->description);
		$repliesText = $doc->getElementsByTagName('p')->item(0)->textContent;
		$repliesTextArray = explode(' ', $repliesText);

		$pubDate = (new DateTime(strval ( $item->pubDate )))->setTimezone(new DateTimeZone('UTC'));

		if ($repliesTextArray[1] > 0 || $pubDate > $targetBeforeTime) {
			continue;
		}

		$issues[] = [
			'slug' => $plugin,
			'link' => strval ( $item->link ),
			'title' => strval ( $item->title ),
			'pubDate' => strval ( $item->pubDate ),
			'creator' => strval ( $item->children($ns['dc'])->creator ),
			'replies' => $repliesTextArray[1],
		];
	}

	usort($issues, 'issueCompareByTimeStamp');
	$issues = array_reverse($issues);

	$data = [
		'issues' => $issues,
		'slug' => $plugin,
		'status' => count($issues) > 0 ? 'Success' : 'No Data Available',
	];

	return $data;
}