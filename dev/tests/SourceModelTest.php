<?php




/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


class SourceModelTest extends \PHPUnit_Framework_TestCase {
	
	
	function dataForTestSetSlug() {
		return array(
			array('',null),
			array('1',1),
			array('1-test',1),
			array('237-test',237),
		);
	}
	
	function dataForTestSetUserAttendingEventsSlug() {
		return array(
			array('',null),
			array('jarofgreen','jarofgreen'),
			array(' jarofgreen','jarofgreen'),
			array(' jar of green','jarofgreen'),
		);
	}

	/**
	* @dataProvider dataForTestSetSlug
	*/
	function testSetGroupSlug($set, $result) {
		$source = new OpenACalendarModelSource();
		$source->setGroupSlug($set);
		$this->assertEquals($result, $source->getGroupSlug());
	}

	/**
	* @dataProvider dataForTestSetSlug
	*/
	function testSetAreaSlug($set, $result) {
		$source = new OpenACalendarModelSource();
		$source->setAreaSlug($set);
		$this->assertEquals($result, $source->getAreaSlug());
	}

	/**
	* @dataProvider dataForTestSetSlug
	*/
	function testSetVenueSlug($set, $result) {
		$source = new OpenACalendarModelSource();
		$source->setVenueSlug($set);
		$this->assertEquals($result, $source->getVenueSlug());
	}

	/**
	* @dataProvider dataForTestSetSlug
	*/
	function testSetCuratedListSlug($set, $result) {
		$source = new OpenACalendarModelSource();
		$source->setCuratedListSlug($set);
		$this->assertEquals($result, $source->getCuratedListSlug());
	}
	
	
	/**
	* @dataProvider dataForTestSetUserAttendingEventsSlug
	*/
	function testSetUserAttendingEventsSlug($set, $result) {
		$source = new OpenACalendarModelSource();
		$source->setUserAttendingEvents($set);
		$this->assertEquals($result, $source->getUserAttendingEvents());
	}
	
	
	
	function dataForTestJSONAPIURL() {
		return array(
			array('cat.com',null,null,null,null,null,'http://cat.com/api1/events.json?includeMedias=true'),
			array('cat.com',1,null,null,null,null,'http://cat.com/api1/group/1/events.json?includeMedias=true'),
			array('cat.com',null,2,null,null,null,'http://cat.com/api1/area/2/events.json?includeMedias=true'),
			array('cat.com',null,null,3,null,null,'http://cat.com/api1/venue/3/events.json?includeMedias=true'),
			array('cat.com',null,null,null,4,null,'http://cat.com/api1/curatedlist/4/events.json?includeMedias=true'),
			array('cat.com',null,null,null,null,"NO",'http://cat.com/api1/country/NO/events.json?includeMedias=true'),
		);
	}
	
	/**
	* @dataProvider dataForTestJSONAPIURL
	*/
	function testJSONAPIURL($baseurl, $group_slug, $area_slug, $venue_slug, $curated_list_slug, $country_code, $result) {
		$source = new OpenACalendarModelSource();
		$source->setBaseurl($baseurl);
		$source->setGroupSlug($group_slug);
		$source->setAreaSlug($area_slug);
		$source->setVenueSlug($venue_slug);
		$source->setCuratedListSlug($curated_list_slug);
		$source->setCountryCode($country_code);
		$this->assertEquals($result, $source->getJSONAPIURL());
	}


	function dataForTestWebURL() {
		return array(
			array('cat.com',null,null,null,null,null,'http://cat.com'),
			array('cat.com',1,null,null,null,null,'http://cat.com/group/1'),
			array('cat.com',null,2,null,null,null,'http://cat.com/area/2'),
			array('cat.com',null,null,3,null,null,'http://cat.com/venue/3'),
			array('cat.com',null,null,null,4,null,'http://cat.com/curatedlist/4'),
			array('cat.com',null,null,null,null,"NO",'http://cat.com/country/NO'),
		);
	}

	/**
	* @dataProvider dataForTestWebURL
	*/
	function testWebURL($baseurl, $group_slug, $area_slug, $venue_slug, $curated_list_slug, $country_code, $result) {
		$source = new OpenACalendarModelSource();
		$source->setBaseurl($baseurl);
		$source->setGroupSlug($group_slug);
		$source->setAreaSlug($area_slug);
		$source->setVenueSlug($venue_slug);
		$source->setCuratedListSlug($curated_list_slug);
		$source->setCountryCode($country_code);
		$this->assertEquals($result, $source->getWebURL());
	}

	function dataForSetBaseURL() {
		return array(
			array('http://cat.com','cat.com'),
			array('https://cat.com','cat.com'),
			array('cat.com','cat.com'),
			array('http://cat.com/api1','cat.com'),
			array('https://cat.com/api1','cat.com'),
			array('cat.com/api1','cat.com'),
		);
	}
	
	/**
	* @dataProvider dataForSetBaseURL
	*/
	function testSetBaseURL($in, $out) {
		$source = new OpenACalendarModelSource();
		$source->setBaseurl($in);
		$this->assertEquals($out, $source->getBaseurl());
	}

	function dataForSetCountryPass() {
		return array(
			array('GB','GB'),
			array('gb','gb'),
			array('gb ','gb'),
			array('DE','DE'),
		);
	}

	/**
	* @dataProvider dataForSetCountryPass
	*/
	function testSetCountryPass($in, $out) {
		$source = new OpenACalendarModelSource();
		$source->setCountryCode($in);
		$this->assertEquals($out, $source->getCountryCode());
	}

	function dataForSetCountryFail() {
		return array(
			array('The nation where cats rule supreme!'),
		);
	}

	/**
	* @dataProvider dataForSetCountryFail
	*/
	function testSetCountryFail($country) {
		$source = new OpenACalendarModelSource();
		$this->setExpectedException('OpenACalendarCountryNotRecognisedError');
		$source->setCountryCode($country);
	}

}

