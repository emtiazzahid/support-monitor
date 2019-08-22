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
          <input type="text" class="wqtextfield" name="wqslug" id="wqslug" placeholder="Enter Plugin Slug" value="<?=$data->slug?>" />
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
    $plugins = getPMPlugins();
?>

<h2>Plugin Unresolved Issue Tracker</h2>

<div class="container">
    <div class="col-50">
        <form action="/action_page.php">
            <div class="row">
                <div class="col-25">
                    <label for="plugin">Time before (hour)</label>
                </div>
                <div class="col-75">
                    <input type="number" min="0" class="wqtextfield" name="hour_before" id="hour_before" placeholder="Enter Hour" value="24"/>
                </div>
            </div>
            <div class="row">
                <div class="col-25">
                    <label for="plugin">Select Plugin</label>
                </div>
                <div class="col-75">
                    <select id="plugin" name="plugin">
                        <option value="">All Plugin</option>
                        <?php
                            foreach ($plugins as $plugin) {
	                            ?>
                                <option value="<?php echo $plugin->id ?>"><?php echo $plugin->slug ?></option>
	                            <?php
                            }
                        ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <div class="col-50">
        <form name="entry_form" id="entry_form">
            <div class="row">
                <div class="col-25">
                    <label>New Plugin</label>
                </div>
                <div class="col-75">
                    <input type="text" class="wqtextfield" name="wqslug" id="wqslug" placeholder="Enter Plugin Slug Title" value="" />
                    <div id="wqslug_message" class="wqmessage"></div>
                </div>
            </div>
            <div class="row">
                <input type="submit" id="wqadd" value="Store" />
            </div>
        </form>
    </div>
    <div class="row">
        <p id="plugin_title" class="pmTableTitle"></p>
        <table id="pm_table">
            <thead>
                <th>topic</th>
                <th>post time</th>
                <th>posted by</th>
            </thead>
            <tbody id="plugin_issue_table">
                <tr>
                    <td colspan="4">
                        NO DATA AVAILABLE
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <div class="modal"><!-- Place at bottom of page --></div>
<?php } ?>
