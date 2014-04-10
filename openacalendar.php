<?php
/**
 * Plugin Name: OpenACalendar
 * Plugin URI: http://ican.openacalendar.org/
 * Description: Incorporate data from an OpenACalendar site into your Wordpress.
 * Version: 1.0.0
 * Author: JMB Technology Ltd
 * Author URI: http://jmbtechnology.co.uk/
 * License: BSD http://ican.openacalendar.org/license.html
 */


// ################################################## Database
require_once(__DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'setup.php');
add_action( 'plugins_loaded', 'OpenACalendar_database_setup' );
register_activation_hook( __DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'setup.php', 'OpenACalendar_database_setup' );

// ################################################## Widgets
function openacalendar_plugin_register_widgets() {
	require __DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'widgetv1.php';
	require __DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'widget.php';
	register_widget( 'OpenACalendarEventsWidget' );
	register_widget( 'OpenACalendarLocalEventsWidget' );
}
add_action( 'widgets_init', 'openacalendar_plugin_register_widgets' );

// ################################################## Admin menu

function openacalendar_admin_menu_init() {
	require_once(__DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'admin.php');
	add_options_page( 'OpenACalendar options', 'OpenACalendar', 'manage_options', 'openacalendar-admin-menu', 'OpenACalendar_admin_menu' );
}
add_action( 'admin_menu', 'openacalendar_admin_menu_init' );
