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
		$wpdb->update($wpdb->prefix."openacalendar_event",array(
			'summary'=>$event->getSummary(),
			'summary_display'=>$event->getSummaryDisplay(),
			'description'=>$event->getDescription(),
			'start_at'=>$event->getStartAtForDatabase(),
			'end_at'=>$event->getEndAtForDatabase(),
			'siteurl'=>$event->getSiteurl(),
			'url'=>$event->getUrl(),
			'timezone'=>$event->getTimezone(),
		),array(
			'id'=>$id
		));
	} else {
		$wpdb->insert($wpdb->prefix."openacalendar_event",array(
			'baseurl'=>$event->getBaseurl(),
			'slug'=>$event->getSlug(),
			'summary'=>$event->getSummary(),
			'summary_display'=>$event->getSummaryDisplay(),
			'description'=>$event->getDescription(),
			'start_at'=>$event->getStartAtForDatabase(),
			'end_at'=>$event->getEndAtForDatabase(),
			'siteurl'=>$event->getSiteurl(),
			'url'=>$event->getUrl(),
			'timezone'=>$event->getTimezone(),
		));
		$id = $wpdb->insert_id;
	}
	
	$wpdb->query(
		$wpdb->prepare('INSERT IGNORE INTO '.$wpdb->prefix.'openacalendar_event_in_pool (eventid,poolid,sourceid) VALUES (%d,%d,%d)',$id,$poolid,$sourceid)
	); 
	
	return $id;
	
}


function OpenACalendar_db_getNextEventsForPool($poolid, $limit=5) {
	global $wpdb;
	
	$out = array();
	
	foreach($wpdb->get_results(
			$wpdb->prepare("SELECT event.* FROM ".$wpdb->prefix."openacalendar_event AS event ".
					"JOIN ".$wpdb->prefix."openacalendar_event_in_pool AS event_in_pool ON event.id = event_in_pool.eventid ".
					"WHERE event_in_pool.poolid=%d AND end_at > NOW() LIMIT ".intval($limit), $poolid)
			,ARRAY_A) as $data) {
		$event = new OpenACalendarModelEvent();
		$event->buildFromDatabase($data);
		$out[] = $event;
	}
	return $out;
	
}

function OpenACalendar_db_newPool($title) {
	global $wpdb;
	$wpdb->insert($wpdb->prefix."openacalendar_pool",array(
			'title'=>trim($title),
		));
	return $wpdb->insert_id;
}


function OpenACalendar_db_newSource(OpenACalendarModelSource $source) {
	global $wpdb;
	$wpdb->insert($wpdb->prefix."openacalendar_source",array(
			'poolid'=>$source->getPoolID(),
			'baseurl'=>$source->getBaseurl(),
		));
	return $wpdb->insert_id;
}

