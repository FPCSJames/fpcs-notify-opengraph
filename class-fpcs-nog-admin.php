<?php if(!defined('ABSPATH')) { exit; }

/**
 * Manage a settings page for the plugin.
 *
 * Author: James M. Joyce <james@flashpointcs.net>
 * Package: fpcs-notify-opengraph
 * Copyright: 2017 Flashpoint Computer Services, LLC
 * License: MIT
 */

final class FPCS_NOG_Admin {

   public function __construct() {
      add_action('admin_menu', [$this, 'add_page']);
		add_action('admin_init', [$this, 'register_fields']);
   }

   public function add_page() {
      add_options_page(
         'FPCS Notify Open Graph Settings', // page title
         'FPCS Notify Open Graph', // menu title
         'manage_options', // capability required for access
         'fpcs_notify_open_graph', // menu slug
         [$this, 'display_page'] // callback
      );
   }

   public function display_page() {
      ?>
      <div class="wrap">
			<h2>FPCS Notify Open Graph Settings</h2>

			<form method="post" action="options.php">
				<?php
					settings_fields('fpcs_nog_group');
					do_settings_sections('fpcs_nog');
					submit_button();
				?>
			</form>
		</div>
      <?php
   }

   public function field_app_id() {
      $value = get_option('fpcs_nog_app_id', '');
      printf(
			'<input class="regular-text" type="text" name="%s" id="%s" value="%s">',
         'fpcs_nog_app_id',
         'fpcs_nog_setting-appid',
         esc_attr($value)
		);
	}

	public function field_app_secret() {
      $value = get_option('fpcs_nog_app_secret', '');
      printf(
         '<input class="regular-text" type="text" name="%s" id="%s" value="%s">',
         'fpcs_nog_app_secret',
         'fpcs_nog_setting-appsecret',
         esc_attr($value)
      );
	}

   public function field_access_token() {
      $value = get_option('fpcs_nog_access_token', '');
      printf(
         '<input class="regular-text" type="text" name="%s" id="%s" value="%s">',
         'fpcs_nog_access_token',
         'fpcs_nog_setting-accesstoken',
         esc_attr($value)
      );
	}

   public function register_fields() {
      $sections = [
         [
            'id' => 'fpcs_nog_section-all',
            'title' => 'API Credentials',
         ]
      ];

      $fields = [
         [
            'id' => 'fpcs_nog_setting-appid',
            'title' => 'App ID',
            'callback' => 'field_app_id',
            'section' => 'fpcs_nog_section-all',
            'name' => 'lcarets_rets_url',
            'filter' => 'sanitize_text_field'
         ],
         [
            'id' => 'fpcs_nog_setting-appsecret',
            'title' => 'App Secret',
            'callback' => 'field_app_secret',
            'section' => 'fpcs_nog_section-all',
            'name' => 'fpcs_nog_app_secret',
            'filter' => 'sanitize_text_field'
         ],
         [
            'id' => 'fpcs_nog_setting-accesstoken',
            'title' => 'Access Token',
            'callback' => 'field_access_token',
            'section' => 'fpcs_nog_section-all',
            'name' => 'fpcs_nog_access_token',
            'filter' => 'sanitize_text_field'
         ]
      ];

      foreach($sections as $section) {
         add_settings_section(
            $section['id'],
            __($section['title'], 'fpcs_nog'),
            '__return_empty_string',
            'fpcs_nog'
         );
      }
      foreach($fields as $field) {
         add_settings_field(
            $field['id'],
            __($field['title'], 'fpcs_nog'),
            [$this, $field['callback']],
            'fpcs_nog',
            $field['section']
         );
         register_setting('fpcs_nog_group', $field['name'], $field['filter']);
      }
   }

}
