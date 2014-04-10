<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

require_once __DIR__.DIRECTORY_SEPARATOR."models.php";

function OpenACalendar_db_getCurrentPools() {
	global $wpdb;
	
	return $wpdb->get_results(
			"SELECT * FROM ".$wpdb->prefix."openacalendar_pool WHERE deleted=0"
			,ARRAY_A);
	
}

function OpenACalendar_db_getCurrentSourcesForPool($poolid) {
	global $wpdb;
	
	return $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_source WHERE deleted=0 AND poolid=%d", $poolid)
			,ARRAY_A);
}


function OpenACalendar_db_getCurrentPool($poolid) {
	global $wpdb;
	
	return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_pool WHERE deleted=0 AND id=%d", $poolid)
			,ARRAY_A);
}


function OpenACalendar_db_storeEvent(OpenACalendarModelEvent $event, $poolid, $sourceid) {
	global $wpdb;
	
	$id = $wpdb->get_var(
			$wpdb->prepare("SELECT id FROM ".$wpdb->prefix."openacalendar_event WHERE baseurl=%s AND slug=%d",$event->getBaseurl(),$event->getSlug())
			);
	if ($id) {
		// TODO;
	} else {
		$wpdb->insert($wpdb->prefix."openacalendar_event",array(
			'baseurl'=>$event->getBaseurl(),
			'slug'=>$event->getSlug(),
			'summary'=>$event->getSummary(),
			'description'=>$event->getDescription(),
			'start_at'=>$event->getStartAtForDatabase(),
			'end_at'=>$event->getEndAtForDatabase(),
			'siteurl'=>$event->getSiteurl(),
			'url'=>$event->getUrl(),
			'timezone'=>$event->getTimezone(),
		));
		$id = $wpdb->insert_id;
	}
	
	$wpdb->insert($wpdb->prefix."openacalendar_event_in_pool",array(
			'eventid'=>$id,
			'poolid'=>$poolid,
			'sourceid'=>$sourceid,
		));
	
	return $id;
	
}

