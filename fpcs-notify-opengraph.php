<?php
/*
Plugin Name: FPCS Notify Open Graph
Plugin URI: https://github.com/FPCSJames/fpcs-notify-opengraph
Description: Trigger FB Open Graph respidering of posts when published.
Version: 1.0
Author: James M. Joyce, Flashpoint Computer Services, LLC
Author URI: https://www.flashpointcs.net
License: MIT
License URI: https://fpcs.mit-license.org
GitHub Plugin URI: https://github.com/FPCSJames/fpcs-notify-opengraph
*/

/**
 * Hook into WP for settings page creation and FB notification.
 *
 * Author: James M. Joyce <james@flashpointcs.net>
 * Package: fpcs-notify-opengraph
 * Copyright: 2017 Flashpoint Computer Services, LLC
 * License: MIT
 */

final class FPCS_Notify_Open_Graph {

   public function __construct() {
      add_action('plugins_loaded', [$this, 'plugins_loaded']);
      add_action('future_to_published', [$this, 'future_to_published'], 10, 1);
   }

   public function future_to_published($post) {
      require_once('vendor/autoload.php');

      $creds = [
         'app_id' => get_option('fpcs_nog_app_id'),
         'app_secret' => get_option('fpcs_nog_app_secret'),
         'default_access_token' => get_option('fpcs_nog_access_token'),
         'default_graph_version' => 'v2.10'
      ];
      if(empty($creds['app_id']) || empty($creds['app_secret']) || empty($creds['default_access_token'])) {
         return;
      }

      $fb = new \Facebook\Facebook($creds);

      try {
         $response = $fb->post('/?id='.urlencode(get_permalink($post)).'&scrape=true');
      } catch(\Facebook\Exceptions\FacebookResponseException $e) {
         write_log('Graph returned an error: '.$e->getMessage());
         return;
      } catch(\Facebook\Exceptions\FacebookSDKException $e) {
         write_log('Facebook SDK returned an error: '.$e->getMessage());
         return;
      }

      $body = $response->getDecodedBody();
      file_put_contents(dirname(__FILE__).'/body.log', print_r($body, true));

      $date = new DateTime($body->updated_time, new DateTimeZone('UTC'));
      $date->setTimezone(new DateTimeZone('America/Chicago'));
      update_post_meta($post-ID, 'fpcs_nog_update_time', $date->format('Y-m-d g:i:sa T'));
   }

   public function plugins_loaded() {
      require('class-fpcs-nog-admin.php');
      new FPCS_NOG_Admin();
   }

}

new FPCS_Notify_Open_Graph();
