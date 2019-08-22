<?php
if(isset($_REQUEST['entryid']) && $_REQUEST['entryid']!='') {
  global $wpdb;
  $data = $wpdb->get_row( "SELECT * FROM `wp_plugin_monitor` WHERE id = '".$_REQUEST['entryid']."'" );
?>
  <div class="wrap wqmain_body">
    <h3 class="wqpage_heading">Edit Entry</h3>
    <div class="wqform_body">
      <form name="update_form" id="update_form">
        <input type="hidden" name="wqentryid" id="wqentryid" value="<?=$_REQUEST['entryid']?>" />
        <div class="wqlabel">SLUG</div>
        <div class="wqfield">
          <input type="text" class="wqtextfield" name="slug" id="wqslug" placeholder="Enter Plugin Slug" value="<?=$data->slug?>" />
        </div>
        <div id="wqslug_message" class="wqmessage"></div>

        <div>&nbsp;</div>

        <div><input type="submit" class="wqsubmit_button" id="wqedit" value="Update" /></div>
        <div>&nbsp;</div>
        <div class="wqsubmit_message"></div>

      </form>
    </div>
  </div>
<?php
} else {
?>
    <div class="wrap wqmain_body">
        <h3 class="wqpage_heading">New Entry</h3>
        <div class="wqform_body">
            <form name="entry_form" id="entry_form">
                <div class="wqlabel">SLUG</div>
                <div class="wqfield">
                    <input type="text" class="wqtextfield" name="slug" id="wqslug" placeholder="Enter Plugin Slug" />
                </div>
                <div id="wqslug_message" class="wqmessage"></div>

                <div>&nbsp;</div>

                <div><input type="submit" class="wqsubmit_button" id="wqedit" value="Save" /></div>
                <div>&nbsp;</div>
                <div class="wqsubmit_message"></div>

            </form>
        </div>
    </div>
    <div class="modal"><!-- Place at bottom of page --></div>
<?php } ?>
