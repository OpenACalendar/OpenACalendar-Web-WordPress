<?php




/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


class AttributeToBooleanTest extends \PHPUnit_Framework_TestCase {
	
	
	function dataForTest1() {
		return array(
			array(true, true),
			array(false, false),
			array('true', true),
			array('false', false),
			array(' TRUE ', true),
			array(' FALSE ', false),
			array('on', true),
			array('off', false),
			array('yes', true),
			array('no', false),
			array('1', true),
			array('0', false),
		);
	}

	/**
	* @dataProvider dataForTest1
	*/
	function test1($in, $out) {
		$this->assertEquals($out, OpenACalendar_shortcode_attribute_to_boolean($in));
	}

	
}

