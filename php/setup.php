<?php


/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


function OpenACalendar_database_setup() {
	global $wpdb;
	
	$currentVersion = 4;
	$installedVersion = get_option( "openacalendar_db_version" );	
	
	if ($installedVersion != $currentVersion) {
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		dbDelta(  "CREATE TABLE ".$wpdb->prefix."openacalendar_pool  (
			id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) DEFAULT '' NOT NULL,
			deleted TINYINT(1) NOT NULL DEFAULT 0,
			UNIQUE KEY id (id)
		 ) CHARSET=".DB_CHARSET.";" );

		dbDelta(  "CREATE TABLE ".$wpdb->prefix."openacalendar_source  (
			id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			poolid MEDIUMINT UNSIGNED NOT NULL,
			baseurl VARCHAR(255) DEFAULT '' NOT NULL,
			group_slug MEDIUMINT UNSIGNED NULL,
			area_slug MEDIUMINT UNSIGNED NULL,
			venue_slug MEDIUMINT UNSIGNED NULL,
			curated_list_slug MEDIUMINT UNSIGNED NULL,
			country_code VARCHAR(10) NULL,
			user_attending_events VARCHAR(255) NULL,
			deleted MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
			UNIQUE KEY id (id)
		 ) CHARSET=".DB_CHARSET.";" );

		dbDelta(  "CREATE TABLE ".$wpdb->prefix."openacalendar_event  (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			baseurl VARCHAR(255) NOT NULL,
			slug MEDIUMINT UNSIGNED NOT NULL,
			summary VARCHAR(255) NULL,
			summary_display VARCHAR(255) NULL,
			description TEXT NULL,
			start_at DATETIME NULL,
			end_at DATETIME NULL,
			siteurl VARCHAR(255) NULL,
			url VARCHAR(255) NULL,
			timezone VARCHAR(255) NULL,
			image_url_normal VARCHAR(255) NULL,
			image_url_full VARCHAR(255) NULL,
			image_title VARCHAR(255) NULL,
			image_source_text VARCHAR(255) NULL,
			deleted MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,			
			UNIQUE KEY id (id)
		 ) CHARSET=".DB_CHARSET.";" );
	
		dbDelta(  "CREATE TABLE ".$wpdb->prefix."openacalendar_event_in_pool (
			eventid INT UNSIGNED NOT NULL,
			poolid MEDIUMINT UNSIGNED NOT NULL,
			sourceid MEDIUMINT UNSIGNED NOT NULL,
			UNIQUE KEY id (eventid,poolid,sourceid)
		 ) CHARSET=".DB_CHARSET.";" );
		
		update_option( "openacalendar_db_version", $currentVersion );
	}
		
}

