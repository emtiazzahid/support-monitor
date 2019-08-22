<?php
add_action('wp_ajax_wqplugin_data_fetch', 'wqplugin_data_fetch_callback_function');

function wqplugin_data_fetch_callback_function() {
	global $wpdb;

	if (!isset($_GET['plugin']) || $_GET['plugin'] == ""){

	}else{

	}


	$result = $wpdb->get_row( "SELECT * FROM `wp_plugin_monitor` WHERE `id` = '".$_GET['plugin']."'");
	if($wpdb->num_rows > 0) {
		$url = 'https://wordpress.org/support/plugin/'.$result->slug.'/feed';
		$args_for_get = [
			'timeout' => 20
		];
		$response = wp_remote_get( $url, $args_for_get );
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
			$response = array('message'=>'Data retrieve failed', 'rescode'=>404);
			echo json_encode($response);
			exit();
		}

		$body = wp_remote_retrieve_body($response);

		$xml = simplexml_load_string($body, null, LIBXML_NOCDATA);
		if (!$xml){
			$response = array('message'=>'Data retrieve failed', 'rescode'=>404);
			echo json_encode($response);
			exit();
		}

		$ns = $xml->getNamespaces(true);

		$targetBeforeTime = ( new DateTime('NOW') )->setTimezone(new DateTimeZone('UTC'));
		$targetBeforeTime->sub(new DateInterval("PT".$_GET['hour_before']."H"));

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
			'plugin_title' => $pluginTitle,
		];

		$response = array('data'=> $data, 'rescode'=>200);
	} else {
		$response = array('message'=>'No plugin found', 'rescode'=>404);
	}
	echo json_encode($response);
	exit();
}



add_action('wp_ajax_wqnew_entry', 'wqnew_enrty_callback_function');

function wqnew_enrty_callback_function() {
  global $wpdb;
  $wpdb->get_row( "SELECT * FROM `wp_plugin_monitor` WHERE `slug` = '".$_POST['slug']."' ORDER BY `id` DESC" );

  if($wpdb->num_rows < 1) {
    $wpdb->insert("wp_plugin_monitor", array(
      "slug" => $_POST['slug'],
      "created_at" => time(),
      "updated_at" => time()
    ));

    $response = array('message'=>'Data Has Inserted Successfully', 'rescode'=>200);
  } else {
    $response = array('message'=>'Data Has Already Exist', 'rescode'=>404);
  }
  echo json_encode($response);
  exit();
  wp_die();
}



add_action('wp_ajax_wqedit_entry', 'wqedit_entry_callback_function');

function wqedit_entry_callback_function() {
  global $wpdb;
  $wpdb->get_row( "SELECT * FROM `wp_plugin_monitor` WHERE `slug` = '".$_POST['slug']."' AND `id`!='".$_POST['wqentryid']."' ORDER BY `id` DESC" );
  if($wpdb->num_rows < 1) {
    $wpdb->update( "wp_plugin_monitor", array(
      "slug" => $_POST['slug'],
      "updated_at" => time()
    ), array('id' => $_POST['wqentryid']) );

    $response = array('message'=>'Data Has Updated Successfully', 'rescode'=>200);
  } else {
    $response = array('message'=>'Data Has Already Exist', 'rescode'=>404);
  }
  echo json_encode($response);
  exit();
  wp_die();
}
