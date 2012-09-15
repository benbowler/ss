<?php 
//v2.03.10
if (!function_exists('zing_support_us')) {
	function zing_support_us($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false,$tweet='Zingiri') {
		if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
?>
		<div style="width:20%;float:right;position:relative">
				<div style="margin:5px 15px;">
					<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
					<div style="float:left;">
						<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.zingiri.com" data-text="<?php echo $tweet?>">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
					</div>
					<div style="float:left;">
						<fb:share-button href="http://www.zingiri.com/plugins-and-addons/<?php echo $shareName;?>/" type="button" >
					</div>
				</div>
				<div style="clear:both"></div>
			<div class="cc-support-us">
				<p>Rate our plugin on Wordpress</p>
				<a href="http://wordpress.org/extend/plugins/<?php echo $wpPluginName;?>" alt="Rate our plugin">
				<img src="<?php echo $pluginUrl?>images/5-stars-125pxw.png" />
				</a>
				<?php 
				$option=$wpPluginName.'-support-us';
				if (get_option($option) == '') {
					update_option($option,time());
				} elseif (isset($_REQUEST['support-us']) && ($_REQUEST['support-us'] == 'hide')) {
					update_option($option,time()+7776000);
				} else {
					if ((time() - get_option($option)) > 1209600) { //14 days 
						if ($donations) echo "<div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://www.zingiri.com/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$adminLink."&support-us=hide'>hide</a> this message.</div>";
					}
				}
				?>
			</div>
			<div style="text-align:center;margin-top:15px">
				<a href="http://www.zingiri.com" target="_blank"><img width="150px" src="<?php echo $pluginUrl?>images/logo.png" /></a>
			</div>
		</div>
<?php 
	}
}

if (!function_exists('zing_support_us_top')) {
	function zing_support_us_top($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false,$tweet='Zingiri') {
		if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
?>
		<div style="float:right;position:relative">
				<div style="margin:5px 15px">
					<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
					<div style="float:left;">
						<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.zingiri.com" data-text="<?php echo $tweet?>">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
					</div>
					<div style="float:right;">
						<fb:share-button href="http://www.zingiri.com/plugins-and-addons/<?php echo $shareName;?>/" type="button" >
					</div>
				</div>
				<div style="clear:both"></div>
		</div>
<?php 
	}
}

if (!function_exists('zing_support_us_bottom')) {
	function zing_support_us_bottom($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false) {
		if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
?>
		<div style="width:100%;float:right;position:relative;text-align:center">
			<div style="text-align:center;margin-top:15px">
				<a href="http://www.zingiri.com" target="_blank"><img width="150px" src="<?php echo $pluginUrl?>images/logo.png" /></a>
			</div>
		</div>
<?php 
	}
}

if (!function_exists('zing_support_us_reminder')) {
	function zing_support_us_reminder($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false) {
		if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
?>
		<div style="width:100%;float:left;position:relative">
				<?php 
				$option=$wpPluginName.'-support-us';
				if (get_option($option) == '') {
					update_option($option,time());
				} elseif (isset($_REQUEST['support-us']) && ($_REQUEST['support-us'] == 'hide')) {
					update_option($option,time()+7776000);
				} else {
					if ((time() - get_option($option)) > 1209600) { //14 days 
						if ($donations) echo "<div class='cc-support-us'><div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://www.zingiri.com/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$adminLink."&support-us=hide'>hide</a> this message.</div></div>";
					}
				}
				?>
			
		</div>
<?php 
	}
}

