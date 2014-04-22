<?php
/**
Plugin Name: OpenACalendar
Plugin URI: http://ican.openacalendar.org/
Description: Incorporate data from an OpenACalendar site into your Wordpress.
Version: 2.0.1
Author: JMB Technology Ltd
Author URI: http://jmbtechnology.co.uk/
License: BSD http://ican.openacalendar.org/license.html
 */


// ################################################## Database
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'setup.php');
add_action( 'plugins_loaded', 'OpenACalendar_database_setup' );
register_activation_hook(dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'setup.php', 'OpenACalendar_database_setup' );

// ################################################## Widgets
function openacalendar_plugin_register_widgets() {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'widgetv1.php';
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'widget.php';
	register_widget( 'OpenACalendarEventsWidget' );
	register_widget( 'OpenACalendarLocalEventsWidget' );
}
add_action( 'widgets_init', 'openacalendar_plugin_register_widgets' );



// ################################################## SHORTCODES
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'shortcode.php');
add_shortcode( 'openacalendar_events', 'OpenACalendar_shortcode_events' );

// ################################################## Get events in Cron
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'fetch.php');
add_action( 'openacalendar_getallevents', 'OpenACalendar_getAllEvents' );
if ( ! wp_next_scheduled( 'openacalendar_getallevents' ) ) {
  wp_schedule_event( time(), 'hourly', 'openacalendar_getallevents' );
}
 
 
// ################################################## Admin menu

function openacalendar_admin_menu_init() {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'admin.php');
	add_options_page( 'OpenACalendar options', 'OpenACalendar', 'manage_options', 'openacalendar-admin-menu', 'OpenACalendar_admin_menu' );
}
add_action( 'admin_menu', 'openacalendar_admin_menu_init' );
