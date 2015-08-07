<div class="wrap">
<?php screen_icon('options-general');?>

<h2>Widget Rotator Pro Help</h2>
<?php
if(isset($_GET['success'])){?>
    <div id="message" class="updated">
        <p><strong>Done!</strong></p>
    </div>
<?php  }?>
<form method="post" action="<?php echo self_admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" />
<input type="hidden" name="action" value="widget_rotator_pro_settings_page" />
<?php wp_nonce_field( 'widget_rotator_pro_settings_page', 'widget_rotator_pro_settings_page' ); ?>

<div id="poststuff" class="metabox-holder has-right-sidebar">
  <div class="inner-sidebar">
    <div class="meta-box-sortables">
      <div class="meta-box-sortables">
        <div class="postbox">
          <div class="handlediv" title="Click to toggle">
            <br>
          </div>
          <h3 class="hndle">
            <span>
              Developed By
            </span>
          </h3>
          <div class="inside">
            <a href="http://nanoplugins.com">Nano Plugins</a><br> You can check our other plugins on our website <a href="http://nanoplugins.com">http://nanoplugins.com</a>: 
          </div>
        </div>
      </div>
      
      
          
      </div>

  </div>
  <div id="post-body">
    <div id="post-body-content">
      <div class="meta-box-sortables">
        <div class="postbox ">
          <div class="handlediv" title="Click to toggle">
            <br>
          </div>
          <h3 class="hndle">
            <span>
              How To Use
            </span>
          </h3>
          <div class="inside">
        1. After you have installed and activated this plugin, go to Appearance -> Widgets -> And drag Widget Rotator Pro in any of your sidebar. <br><br>
	2. Give it a title and choose display order. Click save and then refresh the page (otherwise it won't load in other widgets) <br><br>
	3. Now drag and drop any widget in the same sidebar. At the bottom of each widget you will find an option called "Rotate this widget in". Choose any rotator from same side bar. <br><br>
	4. Now enter a random priority number. Higher number means more priority. Do remember that the sum of priority in each widget assigned to a rotator should be 100.  <br><br>
	5. If you have 3 widgets then enter 30,30,40 or 25,25,50 or anything else but total should be 100.  <br><br>
	6. You can add multiple rotators in each sidebar and choose anyone for your widgets. Just remember to keep the total of priority to 100.  <br><br>
	You can download more awesome plugins from  <a href="http://nanoplugins.com">http://nanoplugins.com</a> <br><br>
          </div>
        </div>
      </div>
      
      
    </div>
    
  </div>
  <!-- #post-body-content -->
</div>
</form>

</div>