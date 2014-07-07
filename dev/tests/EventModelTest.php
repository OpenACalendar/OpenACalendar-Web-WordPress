<?php




/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


class EventModelTest extends \PHPUnit_Framework_TestCase {
	
	
	function dataForGetDescriptionTruncated() {
		return array(
			array('Cat',0,''),
			array('Cat',5,'Cat'),
			array('The Cat Sat On The Mat',5,'The ...'),
			array('The Cat Sat On The Mat',7,'The Cat ...'),
			array('The Cat Sat On The Mat',13,'The Cat Sat ...'),
			array('The Cat. Sat On The Mat',8,'The Cat. ...'),
			array('The Cat. Sat On The Mat',7,'The Cat ...'),
			array('The Cat, Sat On The Mat',8,'The Cat, ...'),
			array('The Cat, Sat On The Mat',7,'The Cat ...'),
			array('The Cat - Sat On The Mat',9,'The Cat - ...'),
			array('The Cat - Sat On The Mat',8,'The Cat ...'),
		);
	}

	/**
	* @dataProvider dataForGetDescriptionTruncated
	*/
	function testGetDescriptionTruncated($set, $length, $result) {
		$event = new OpenACalendarModelEvent();
		$event->buildFromDatabase(array(
				'baseurl'=>null,
				'slug'=>null,
				'summary'=>null,
				'summary_display'=>null,
				'description'=>null,
				'start_at'=>null,
				'end_at'=>null,
				'siteurl'=>null,
				'url'=>null,
				'timezone'=>null,
				'deleted'=>null,
				'description'=>$set,
			));
		$this->assertEquals($result, $event->getDescriptionTruncated($length));
	}


	function dataIsStartAndEndOnSameDay() {
		return array(
			array('2014-01-01 17:00:00','2014-01-01 21:00:00','Europe/London',true),
			array('2014-01-01 17:00:00','2014-01-03 21:00:00','Europe/London',false),
		);
	}

	/**
	 * @dataProvider dataIsStartAndEndOnSameDay
	 */
	function testIsStartAndEndOnSameDay($start, $end, $timezone, $result) {
		$event = new OpenACalendarModelEvent();
		$event->buildFromDatabase(array(
			'baseurl'=>null,
			'slug'=>null,
			'summary'=>null,
			'summary_display'=>null,
			'description'=>null,
			'start_at'=>$start,
			'end_at'=>$end,
			'siteurl'=>null,
			'url'=>null,
			'timezone'=>null,
			'deleted'=>null,
			'description'=>null,
		));
		$this->assertEquals($result, $event->isStartAndEndOnSameDay($timezone));
	}
	
}

