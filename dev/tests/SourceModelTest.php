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
			array('cat.com',null,null,null,null,null,'http://cat.com/api1/events.json'),
			array('cat.com',1,null,null,null,null,'http://cat.com/api1/group/1/events.json'),
			array('cat.com',null,2,null,null,null,'http://cat.com/api1/area/2/events.json'),
			array('cat.com',null,null,3,null,null,'http://cat.com/api1/venue/3/events.json'),
			array('cat.com',null,null,null,4,null,'http://cat.com/api1/curatedlist/4/events.json'),
			array('cat.com',null,null,null,null,"NO",'http://cat.com/api1/country/NO/events.json'),
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
	
}

