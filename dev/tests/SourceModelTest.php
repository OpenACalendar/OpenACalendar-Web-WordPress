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
	
}

