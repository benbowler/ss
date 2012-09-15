<link rel="stylesheet" href="<?php echo BP_BUDDYSTREAM_URL.'/extensions/default/admin.css';?>" type="text/css" />

<?php echo BuddyStreamExtensions::tabLoader('twitter'); ?>

<div class="buddystream_info_box"><?php _e('twitter statitics description','buddystream');?></div>

<?php 

global $bp,$wpdb; $component = "twitter";

//include some generic stats
$incPath = str_replace("wp-admin","",getcwd());
ini_set('include_path', $incPath);
include(PLUGINDIR.'/buddystream/extensions/default/templates/Stats.php');

?>

<table class="buddystream_table" cellspacing="0">
      <tr class="header">
          <td><?php echo __('Statistics', 'buddystream'); ?></td>
          <td></td>
      </tr>
 
      
    <?php
       $count_users             = count($wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->users")));
       $count_tweetstreamusers  = count($wpdb->get_results($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='tweetstream_token';")));
       $perc_tweetstreamusers   = round(($count_tweetstreamusers / $count_users) * 100);
       $count_tweets            = count($wpdb->get_results($wpdb->prepare("SELECT type FROM " . $bp->activity->table_name . " WHERE type='twitter';")));
       $count_activity          = count($wpdb->get_results($wpdb->prepare("SELECT id FROM " . $bp->activity->table_name)));
       $perc_tweetupdates       = round(($count_tweets / $count_activity * 100));
       $average_tweets_day      = round($count_tweets / 24);
       $average_tweets_week     = $average_tweets_day * 7;
       $average_tweets_month    = $average_tweets_day * 30;
       $average_tweets_year     = $average_tweets_day * 365;

       echo "
        <tr>
            <td>".__('Amount of users:','buddystream')."</td>
            <td>" . $count_users . "</td>
        </tr>
        <tr class='odd'>
            <td>".__('Amount of user Twitter intergration:','buddystream')."</td>
            <td>" . $count_tweetstreamusers . "</td>
        </tr>
        <tr>
            <td>".__('Percentage of users using intergration:','buddystream')."</td>
            <td>" . $perc_tweetstreamusers . "%</td>
        </tr>
        <tr class='odd'>
            <td>".__('Amount of activity updates:','buddystream')."</td>
            <td>" . $count_activity . "</td>
        </tr>
        <tr>
            <td>".__('Amount of tweets updates:','buddystream')."</td>
            <td>" . $count_tweets . "</td>
        </tr>
        <tr class='odd'>
            <td>".__('Percentage of tweets in activity updates:','buddystream')."</td>
            <td>" . $perc_tweetupdates . "%</td>
        </tr>
        <tr>
            <td>".__('Average tweets import per day:','buddystream')."</td>
            <td>" . $average_tweets_day . "</td>
        </tr>
        <tr class='odd'>
            <td>".__('Average tweets import per week:','buddystream')."</td>
            <td>" . $average_tweets_week . "</td>
        </tr>
        <tr>
            <td>".__('Average tweets import per month:','buddystream')."</td>
            <td>" . $average_tweets_month . "</td>
        </tr>
        <tr class='odd'>
            <td>".__('Average tweets import per year:','buddystream')."</td>
            <td>" . $average_tweets_year . "</td>
        </tr>
        ";
    ?>
   </tbody>

  </table>
</div>

