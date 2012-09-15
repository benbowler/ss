<?php
/**
 * Import starter
 */

function BuddystreamTwitterImportStart(){

    if( ! get_site_option('tweetstream_user_settings_syncbp')){

        //add record to the log
        $buddyStreamLog = new BuddyStreamLog();
        $buddyStreamLog->log("Twitter import disabled.");

        return false;
    }

    $importer = new BuddyStreamTwitterImport();
    return $importer->doImport();
}

/**
 * Twitter Import Class
 */

class BuddyStreamTwitterImport{

    //do the import
    public function doImport() {

        global $bp, $wpdb;

        $buddyStreamLog = new BuddyStreamLog();
        $buddyStreamFilters = new BuddyStreamFilters();

        //item counter for in the logs
        $itemCounter = 0;

        if (get_site_option("tweetstream_consumer_key")) {
            if (get_site_option('tweetstream_user_settings_syncbp') == 0) {

                $user_metas = $wpdb->get_results(
                    $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta where meta_key='tweetstream_token'")
                );

                if ($user_metas) {
                    foreach ($user_metas as $user_meta) {

                        //check for
                        $limitReached = $buddyStreamFilters->limitReached('twitter', $user_meta->user_id);

                        if (!$limitReached && get_user_meta($user_meta->user_id, 'tweetstream_synctoac', 1)) {

                            //Handle the OAuth requests
                            $buddyStreamOAuth = new BuddyStreamOAuth();
                            $buddyStreamOAuth->setCallbackUrl($bp->root_domain);
                            $buddyStreamOAuth->setConsumerKey(get_site_option("tweetstream_consumer_key"));
                            $buddyStreamOAuth->setConsumerSecret(get_site_option("tweetstream_consumer_secret"));
                            $buddyStreamOAuth->setAccessToken(get_user_meta($user_meta->user_id,'tweetstream_token', 1));
                            $buddyStreamOAuth->setAccessTokenSecret(get_user_meta($user_meta->user_id,'tweetstream_tokensecret', 1));

                            $items = $buddyStreamOAuth->oAuthRequest('http://api.twitter.com/1/statuses/user_timeline.xml');
                            $items = @simplexml_load_string($items);

                            if ($items && !$items->error) {

                                //update the user screen_name
                                $screenName = ''.$items->status->user->screen_name[0];
                                update_user_meta($user_meta->user_id,'tweetstream_screenname', $screenName);

                                //go through tweets
                                foreach ($items as $tweet) {

                                    //check daylimit
                                    $limitReached = $buddyStreamFilters->limitReached('twitter', $user_meta->user_id);

                                    //check if good filter passes
                                    $goodFilters = get_site_option('tweetstream_filter') . get_user_meta($user_meta->user_id, 'tweetstream_filtergood', 1);
                                    $goodFilter = $buddyStreamFilters->searchFilter($tweet->text, $goodFilters, false, true, true);

                                    //check if bad filter passes
                                    $badFilters = get_site_option('tweetstream_filterexplicit') . get_user_meta($user_meta->user_id, 'tweetstream_filterbad', 1);
                                    $badFilter = $buddyStreamFilters->searchFilter($tweet->text, $badFilters, true);

                                    //no filters set so just import everything
                                    if(! get_site_option('tweetstream_filter') && ! get_user_meta($user_meta->user_id, 'tweetstream_filtergood', 1)){
                                        $goodFilter = true;
                                    }

                                    if(! get_site_option('tweetstream_filterexplicit') && ! get_user_meta($user_meta->user_id, 'tweetstream_filterbad', 1)){
                                        $badFilter = false;
                                    }

                                    //check if source filter passes
                                    $sourceFilter = $buddyStreamFilters->searchFilter($bp->root_domain, $tweet->source, true);
                                    if (!$limitReached && $goodFilter && !$badFilter && !$sourceFilter) {

                                        $returnCreate = buddystreamCreateActivity(array(
                                                'user_id'       => $user_meta->user_id,
                                                'extension'     => 'twitter',
                                                'type'          => 'tweet',
                                                'content'       => $tweet->text,
                                                'item_id'       => $tweet->id,
                                                'raw_date'      => gmdate('Y-m-d H:i:s', strtotime($tweet->created_at)),
                                                'actionlink'    => 'http://www.twitter.com/' . $screenName . '/status/'.$tweet->id
                                            )
                                        );

                                        if($returnCreate){
                                            $itemCounter++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //add record to the log
        $buddyStreamLog->log("Twitter imported ".$itemCounter." tweets for ".count($user_metas)." users.");

        //return number of items imported
        return $itemCounter;

    }
}