<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."models.php";

function OpenACalendar_db_getCurrentPools() {
	global $wpdb;
	
	return $wpdb->get_results(
			"SELECT * FROM ".$wpdb->prefix."openacalendar_pool WHERE deleted=0"
			,ARRAY_A);
	
}

function OpenACalendar_db_getCurrentSourcesForPool($poolid) {
	global $wpdb;
	$out = array();
	
	foreach($wpdb->get_results(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_source WHERE deleted=0 AND poolid=%d", $poolid)
			,ARRAY_A) as $sourceData) {
		$source = new OpenACalendarModelSource();
		$source->buildFromDatabase($sourceData);
		$out[] = $source;
	}
	return $out;
}


function OpenACalendar_db_getCurrentPool($poolid) {
	global $wpdb;
	
	return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_pool WHERE deleted=0 AND id=%d", $poolid)
			,ARRAY_A);
}

function OpenACalendar_db_getCurrentSource($sourceid) {
	global $wpdb;
	
	$source = new OpenACalendarModelSource();
	$source->buildFromDatabase($wpdb->get_row(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_source WHERE deleted=0 AND id=%d", $sourceid)
			,ARRAY_A));
	return $source;
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
			'deleted'=>($event->getDeleted() ? 1 : 0),
			'timezone'=>$event->getTimezone(),
			'image_url_normal'=>$event->getImageUrlNormal(),
			'image_url_full'=>$event->getImageUrlFull(),
			'image_title'=>$event->getImageTitle(),
			'image_source_text'=>$event->getImageSourceText(),
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
			'deleted'=>($event->getDeleted() ? 1 : 0),
			'timezone'=>$event->getTimezone(),
			'image_url_normal'=>$event->getImageUrlNormal(),
			'image_url_full'=>$event->getImageUrlFull(),
			'image_title'=>$event->getImageTitle(),
			'image_source_text'=>$event->getImageSourceText(),
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
					"WHERE event_in_pool.poolid=%d AND event.end_at > NOW() AND event.deleted = 0 ".
					"GROUP BY event.id ORDER BY event.start_at ASC LIMIT ".intval($limit), $poolid)
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
	$existing = $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_source WHERE ".
				"poolid=%d AND ".
				"group_slug=%d AND ".
				"venue_slug=%d AND ".
				"area_slug=%d AND ".
				"curated_list_slug=%d AND ".
				"country_code=%s AND ".
				"user_attending_events=%d AND ".
				"baseurl=%s  ",
				$source->getPoolID(),
				$source->getGroupSlug(),
				$source->getVenueSlug(),
				$source->getAreaSlug(),
				$source->getCuratedListSlug(),
				$source->getCountryCode(),
				$source->getUserAttendingEvents(),
				$source->getBaseurl()
				)
			,ARRAY_A);
	if ($existing) {
		$source->setId($existing['id']);
		$wpdb->update($wpdb->prefix."openacalendar_source",array(
			'deleted'=>0,
		),array(
			'id'=>$source->getId(),
		));
	} else {
		$wpdb->insert($wpdb->prefix."openacalendar_source",array(
				'poolid'=>$source->getPoolID(),
				'group_slug'=>$source->getGroupSlug(),
				'venue_slug'=>$source->getVenueSlug(),
				'area_slug'=>$source->getAreaSlug(),
				'curated_list_slug'=>$source->getCuratedListSlug(),
				'country_code'=>$source->getCountryCode(),
				'user_attending_events'=>$source->getUserAttendingEvents(),
				'baseurl'=>$source->getBaseurl(),
			));
		$source->setId($wpdb->insert_id);
	}
	
	return $source->getId();
}

function OpenACalendar_db_deleteSource(OpenACalendarModelSource $source) {
	global $wpdb;
	$wpdb->update($wpdb->prefix."openacalendar_source",array(
		'deleted'=>1,
	),array(
		'id'=>$source->getId(),
	));
}

