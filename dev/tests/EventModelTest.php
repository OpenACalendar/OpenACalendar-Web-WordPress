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
				'description'=>$set,
			));
		$this->assertEquals($result, $event->getDescriptionTruncated($length));
	}

	
}

