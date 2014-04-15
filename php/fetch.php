<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
require_once __DIR__.DIRECTORY_SEPARATOR."models.php";
require_once __DIR__.DIRECTORY_SEPARATOR."database.php";

function OpenACalendar_getAllEvents() {
	foreach (OpenACalendar_db_getCurrentPools() as $pool) {
		foreach(OpenACalendar_db_getCurrentSourcesForPool($pool['id']) as $source) {
			OpenACalendar_getAndStoreEventsForSource($source);
		}
	}
}

function OpenACalendar_getAndStoreEventsForSource($sourcedata) {
	
	$url = "http://".$sourcedata['baseurl']."/api1";
	
	// TODO filters
	
	$url .= '/events.json';
	
	$ch = curl_init();      
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'OpenACalendar WordPress plugin from jmbtechnology.co.uk');
	$dataString = curl_exec($ch);
	$response = curl_getinfo( $ch );
	curl_close($ch);

	
	$data = json_decode($dataString);

	$count = 0;
	
	foreach($data->data as $eventData) {
		$eventModel = new OpenACalendarModelEvent();
		$eventModel->buildFromAPI1JSON($sourcedata['baseurl'], $eventData);
		$eventid = OpenACalendar_db_storeEvent($eventModel, $sourcedata['poolid'], $sourcedata['id']);
		$count++;
	}
	
	return $count;
	
	
	
	
}