<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class OpenACalendarCountryNotRecognisedError extends Exception {
}

class OpenACalendarModelSource {

	protected $id;
	protected $poolid;
	protected $baseurl;
	
	protected $group_slug;
	protected $area_slug;
	protected $venue_slug;
	protected $curated_list_slug;
	protected $country_code;
	protected $user_attending_events;


	public function buildFromDatabase($data) {
		$this->id = $data['id'];
		$this->poolid = $data['poolid'];
		$this->baseurl = $data['baseurl'];
		$this->group_slug = $data['group_slug'] ? $data['group_slug'] : null;
		$this->area_slug = $data['area_slug'] ? $data['area_slug'] : null;
		$this->venue_slug = $data['venue_slug'] ? $data['venue_slug'] : null;
		$this->curated_list_slug = $data['curated_list_slug'] ? $data['curated_list_slug'] : null;
		$this->country_code = $data['country_code'] ? $data['country_code'] : null;
		$this->user_attending_events = $data['user_attending_events'] ? $data['user_attending_events'] : null;
	}
	
	public function getId() { return $this->id; }
	public function setId($id) {
		$this->id = $id;
	}

 	public function getPoolID() { return $this->poolid; }
	public function setPoolID($poolid) { $this->poolid = $poolid; }
	public function getBaseurl() { return $this->baseurl; }
	public function setBaseurl($baseurl) { 
		if (substr(strtolower($baseurl),0,7) == 'http://') {
			$baseurl = substr(strtolower($baseurl),7);
		} else if (substr(strtolower($baseurl),0,8) == 'https://') {
			$baseurl = substr(strtolower($baseurl),8);
		}
		$bits = explode('/', $baseurl, 2);
		$this->baseurl = $bits[0]; 
	}
	
	public function getGroupSlug() {
		return $this->group_slug;
	}

	public function setGroupSlug($group_slug) {
		$bits = explode("-", $group_slug, 2);
		$this->group_slug = isset($bits[0]) ? $bits[0] : null;
	}

	public function getAreaSlug() {
		return $this->area_slug;
	}

	public function setAreaSlug($area_slug) {
		$bits = explode("-", $area_slug, 2);
		$this->area_slug = isset($bits[0]) ? $bits[0] : null;	}

	public function getVenueSlug() {
		return $this->venue_slug;
	}

	public function setVenueSlug($venue_slug) {
		$bits = explode("-", $venue_slug, 2);
		$this->venue_slug = isset($bits[0]) ? $bits[0] : null;
	}

	public function getCuratedListSlug() {
		return $this->curated_list_slug;
	}

	public function setCuratedListSlug($curated_list_slug) {
		$bits = explode("-", $curated_list_slug, 2);
		$this->curated_list_slug = isset($bits[0]) ? $bits[0] : null;
	}

	public function getCountryCode() {
		return $this->country_code;
	}

	public function setCountryCode($country_code) {
		if ($country_code) {
			if ($this->isValidCountryCode($country_code)) {
				$this->country_code = trim($country_code);
			} else {
				throw new OpenACalendarCountryNotRecognisedError();
			}
		}
	}

	public function isValidCountryCode($country_code) {
		foreach(explode("\n", file_get_contents(__DIR__.'/../iso3166.tab')) as $line) {
			if ($line && substr($line, 0,1) != '#') {
				$bits = explode("\t", $line) ;
				if (strtoupper($bits[0]) == strtoupper(trim($country_code))) return true;
			}
		}
		return false;
	}

	public function getJSONAPIURL() {
		$url = "http://".$this->baseurl."/api1";
		
		if ($this->group_slug) {
			$url .= '/group/'.$this->group_slug;
		} else if ($this->venue_slug) {
			$url .= '/venue/'.$this->venue_slug;
		} else if ($this->area_slug) {
			$url .= '/area/'.$this->area_slug;
		} else if ($this->curated_list_slug) {
			$url .= '/curatedlist/'.$this->curated_list_slug;
		} else if ($this->country_code) {
			$url .= '/country/'.$this->country_code;
		} else if ($this->user_attending_events) {
			$url .= '/person/'.$this->user_attending_events;
		}
		
		$url .= '/events.json?includeMedias=true';
		
		return $url;
	}

	public function getWebURL() {
		$url = "http://".$this->baseurl;

		if ($this->group_slug) {
			$url .= '/group/'.$this->group_slug;
		} else if ($this->venue_slug) {
			$url .= '/venue/'.$this->venue_slug;
		} else if ($this->area_slug) {
			$url .= '/area/'.$this->area_slug;
		} else if ($this->curated_list_slug) {
			$url .= '/curatedlist/'.$this->curated_list_slug;
		} else if ($this->country_code) {
			$url .= '/country/'.$this->country_code;
		} else if ($this->user_attending_events) {
			$url .= '/person/'.$this->user_attending_events;
		}

		return $url;
	}
	
	public function getUserAttendingEvents() {
		return $this->user_attending_events;
	}

	public function setUserAttendingEvents($user_attending_events) {
		$this->user_attending_events = trim(str_replace(array(' '), array(''), $user_attending_events));
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
	protected $deleted;

	protected $image_url_normal;
	protected $image_url_full;
	protected $image_title;
	protected $image_source_text;
	
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
		$this->deleted = $data['deleted'];
		$this->image_url_normal  = $data['image_url_normal'];
		$this->image_url_full  = $data['image_url_full'];
		$this->image_title = $data['image_title'];
		$this->image_source_text = $data['image_source_text'];
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
		$this->deleted = $data->deleted;
		if (isset($data->medias) && count($data->medias) > 0 && isset($data->medias[0]->picture)) {
			$this->image_title = $data->medias[0]->title;
			$this->image_source_text = $data->medias[0]->sourcetext;
			$this->image_url_normal = $data->medias[0]->picture->normalURL;
			$this->image_url_full = $data->medias[0]->picture->fullURL;
		}
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
	
	public function getDescriptionTruncated($length) {
		if ($length  == 0) {
			return '';
		} else if ($length > strlen($this->description)) {
			return $this->description;
		} else {
			$cut_at = $length;
			$cut_chars = array(' ',',','.','\n','\t','\r');
			while($cut_at > 0 && !in_array(substr($this->description, $cut_at, 1), $cut_chars)) {
				$cut_at--;
			}
			return substr($this->description,0,$cut_at).' ...';
		}
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

	public function getEndAtAsString($timezone='UTC', $format="Y-m-d H:i:s") {
		if ($this->end_at->getTimezone() == $timezone) {
			return $this->end_at->format($format);
		} else {
			$sa = clone $this->end_at;
			$sa->setTimezone(new \DateTimeZone($timezone));
			return $sa->format($format);
		}
	}

	public function isStartAndEndOnSameDay($timezone = 'UTC') {
		$timezone = new \DateTimeZone($timezone);
		$start = clone $this->start_at;
		$start->setTimezone($timezone);
		$end = clone $this->end_at;
		$end->setTimezone($timezone);
		return $start->format("Y-m-d") == $end->format("Y-m-d");
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


	public function getDeleted() {
		return $this->deleted;
	}

	public function getImageSourceText()
	{
		return $this->image_source_text;
	}

	public function getImageTitle()
	{
		return $this->image_title;
	}

	public function getImageUrlFull()
	{
		return $this->image_url_full;
	}

	public function getImageUrlNormal()
	{
		return $this->image_url_normal;
	}

	public function getHasImage()
	{
		return $this->image_url_normal && $this->image_url_full;
	}

}

