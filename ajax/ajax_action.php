<?php
add_action('wp_ajax_wqplugin_data_fetch', 'wqplugin_data_fetch_callback_function');

function wqplugin_data_fetch_callback_function() {
	$pluginsInfo = [];

	if (!isset($_GET['plugin']) || $_GET['plugin'] == ""){
		global $wpdb;

		$plugins = $wpdb->get_results( "SELECT * FROM `wp_plugin_monitor`");

		foreach ($plugins as $plugin){
			$pluginsInfo[] = getPMPluginSupportData($plugin->slug, $_GET['hour_before']);
		}
	}else{
		$pluginsInfo[] = getPMPluginSupportData($_GET['plugin'], $_GET['hour_before']);
	}

	echo json_encode($pluginsInfo);
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
