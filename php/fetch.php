<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."models.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";

function OpenACalendar_getAllEvents() {
	foreach (OpenACalendar_db_getCurrentPools() as $pool) {
		foreach(OpenACalendar_db_getCurrentSourcesForPool($pool['id']) as $source) {
			try {
				OpenACalendar_getAndStoreEventsForSource($source);
			} catch (OpenACalendarGetEventsException $error) {
				// todo - log ?
			}
		}
	}
}

class OpenACalendarGetEventsException extends \Exception {
	protected $returnedData;

	function __construct($message, $returnedData)
	{
		parent::__construct($message);
		$this->returnedData = $returnedData;
	}
}

function OpenACalendar_getAndStoreEventsForSource(OpenACalendarModelSource $sourcedata) {
	
	$ch = curl_init();      
	curl_setopt($ch, CURLOPT_URL, $sourcedata->getJSONAPIURL());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'OpenACalendar WordPress plugin from jmbtechnology.co.uk, site '.get_site_url());
	$dataString = curl_exec($ch);
	$response = curl_getinfo( $ch );
	curl_close($ch);

	$data = json_decode($dataString);
	
	if (is_object($data)) {

		if (isset($data->data)) {

			$count = 0;
			foreach($data->data as $eventData) {
				$eventModel = new OpenACalendarModelEvent();
				$eventModel->buildFromAPI1JSON($sourcedata->getBaseurl(), $eventData);
				$eventid = OpenACalendar_db_storeEvent($eventModel, $sourcedata->getPoolID(), $sourcedata->getId());
				$count++;
			}
			return $count;

		} else {
			throw new OpenACalendarGetEventsException("Could parse JSON, but expected data is missing? ",$dataString);
		}
	} else {
		throw new OpenACalendarGetEventsException("Could not parse JSON!",$dataString);
	}

}

