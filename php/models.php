<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class OpenACalendarModelEvent {

	protected $id;
	protected $baseurl;
	protected $slug;


	protected $summary;
	protected $description;
	protected $start_at;
	protected $end_at;
	protected $siteurl;
	protected $url;
	protected $timezone;
	
	public function buildFromAPI1JSON($baseurl, $data) {
		$this->baseurl = $baseurl;
		$this->slug = $data->slug;
		$this->summary = $data->summaryDisplay;
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

