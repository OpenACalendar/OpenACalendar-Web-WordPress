<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class OpenACalendarModelSource {

	//protected $id;
	protected $poolid;
	protected $baseurl;
	
	// TODO group_slug MEDIUMINT UNSIGNED NULL,
	// TODO area_slug MEDIUMINT UNSIGNED NULL,
	// TODO venue_slug MEDIUMINT UNSIGNED NULL,
	// TODO curated_list_slug MEDIUMINT UNSIGNED NULL,
	// TODO country_code VARCHAR(10) NULL,
	
	//public function getId() { return $this->id; }
	
	public function getPoolID() { return $this->poolid; }
	public function setPoolID($poolid) { $this->poolid = $poolid; }
	public function getBaseurl() { return $this->baseurl; }
	public function setBaseurl($baseurl) { 
		// TODO verify as much as possible, strip http://
		$this->baseurl = $baseurl; 
	}
	
}

class OpenACalendarModelEvent {

	protected $id;
	protected $baseurl;
	protected $slug;


	protected $summary;
	protected $summary_display;
	protected $description;
	protected $start_at;
	protected $end_at;
	protected $siteurl;
	protected $url;
	protected $timezone;
	
	public function buildFromDatabase($data) {
		$this->baseurl = $data['baseurl'];
		$this->slug = $data['slug'];
		$this->summary = $data['summary'];
		$this->summary_display = $data['summary_display'];
		$this->description = $data['description'];
		$utc = new DateTimeZone("UTC");
		$this->start_at = new DateTime($data['start_at'], $utc);
		$this->end_at = new DateTime($data['end_at'], $utc);
		$this->siteurl = $data['siteurl'];
		$this->url = $data['url'];
		$this->timezone = $data['timezone'];
	}
	
	public function buildFromAPI1JSON($baseurl, $data) {
		$this->baseurl = $baseurl;
		$this->slug = $data->slug;
		$this->summary = $data->summary;
		$this->summary_display = $data->summaryDisplay;
		$this->description = $data->description;
		$utc = new DateTimeZone("UTC");
		$this->start_at = new DateTime("", $utc);
		$this->start_at->setTimestamp($data->start->timestamp);
		$this->end_at = new DateTime("", $utc);
		$this->end_at->setTimestamp($data->end->timestamp);
		$this->siteurl = $data->siteurl;
		$this->url = $data->url;
		$this->timezone = $data->timezone;	
	}
	
	public function getId() {
		return $this->id;
	}

	public function getBaseurl() {
		return $this->baseurl;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function getSummary() {
		return $this->summary;
	}

	public function getSummaryDisplay() {
		return $this->summary_display;
	}
	
	public function getDescription() {
		return $this->description;
	}

	public function getStartAt() {
		return $this->start_at;
	}

	public function getStartAtForDatabase() {
		if ($this->start_at->getTimezone() == 'UTC') {
			return $this->start_at->format("Y-m-d H:i:s");
		} else {
			$sa = clone $this->start_at;
			$sa->setTimezone(new \DateTimeZone("UTC"));
			return $sa->format("Y-m-d H:i:s");
		}
	}

	public function getStartAtAsString($timezone='UTC', $format="Y-m-d H:i:s") {
		if ($this->start_at->getTimezone() == $timezone) {
			return $this->start_at->format($format);
		} else {
			$sa = clone $this->start_at;
			$sa->setTimezone(new \DateTimeZone($timezone));
			return $sa->format($format);
		}
	}

	
	public function getEndAt() {
		return $this->end_at;
	}

	public function getEndAtForDatabase() {
		if ($this->end_at->getTimezone() == 'UTC') {
			return $this->end_at->format("Y-m-d H:i:s");
		} else {
			$sa = clone $this->end_at;
			$sa->setTimezone(new \DateTimeZone("UTC"));
			return $sa->format("Y-m-d H:i:s");
		}
	}
	
	public function getSiteurl() {
		return $this->siteurl;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getTimezone() {
		return $this->timezone;
	}


	
}

