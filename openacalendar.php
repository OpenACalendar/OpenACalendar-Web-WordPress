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



function openacalendar_plugin_register_widgets() {
	require __DIR__.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'widgetv1.php';
	register_widget( 'OpenACalendarEventsWidget' );
}

add_action( 'widgets_init', 'openacalendar_plugin_register_widgets' );

