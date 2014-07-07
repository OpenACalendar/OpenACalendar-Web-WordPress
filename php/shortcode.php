<?php


/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

	
function OpenACalendar_shortcode_events( $atts, $content="" ) {
	$attributes = shortcode_atts( array(
		'poolid' => null,
		'descriptionmaxlength'=>300,
		'usesummarydisplay'=>true,
		'startformat'=>'D jS M g:ia',
		'endformatsameday'=>'',
		'endformat'=>'',
		'startandenddivider' => ' to ',
		'eventcount'=>20,
		'url'=>'site',
	), $atts );
	$attributes['usesummarydisplay'] = OpenACalendar_shortcode_attribute_to_boolean($attributes['usesummarydisplay']);
	
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";

	$html = '<div class="OpenACalendarListEvents">';

	if ($attributes['poolid']) {
	
		foreach(OpenACalendar_db_getNextEventsForPool($attributes['poolid'], $attributes['eventcount']) as $event) {
			$url = $attributes['url'] == 'url' ? $event->getUrl() : $event->getSiteurl();
			$html .= '<div class="OpenACalendarWidgetListEventsEvent">';
			$end = null;
			if ($attributes['endformatsameday'] || $attributes['endformat']) {
				$format = $attributes['endformat'];
				if ($event->isStartAndEndOnSameDay($event->getTimezone()) && $attributes['endformatsameday']) {
					$format = $attributes['endformatsameday'];
				}
				$end = $event->getStartAtAsString($event->getTimezone(), $format);
			}
			if ($end) {
				$html .= '<div class="OpenACalendarWidgetListEventsDate">'.$event->getStartAtAsString($event->getTimezone(), $attributes['startformat']).
					$attributes['startandenddivider'].$end.'</div>';
			} else {
				$html .= '<div class="OpenACalendarWidgetListEventsDate">'.$event->getStartAtAsString($event->getTimezone(), $attributes['startformat']).'</div>';
			}
			$html .= '<div class="OpenACalendarWidgetListEventsSummary"><a href="'.htmlspecialchars($url).'">'.
				htmlspecialchars($attributes['usesummarydisplay'] ? $event->getSummaryDisplay() : $event->getSummary()).
				'</a></div>';	
			if ($attributes['descriptionmaxlength'] > 0) {
				$html .= '<div class="OpenACalendarWidgetListEventsDescription">'.htmlspecialchars($event->getDescriptionTruncated($attributes['descriptionmaxlength'])).'</div>';
			}
			$html .= '<a class="OpenACalendarWidgetListEventsMoreLink" href="' . htmlspecialchars($url). '">More Info</a>';
			$html .= '</div>';
		}
		
	} else {
		
	}

	$html .= '</div>';
	

	return $html;
}

function OpenACalendar_shortcode_attribute_to_boolean($in) {
	if ($in === true) {
		return true;
	} else if ($in === false) {
		return false;
	} else {
		$in = strtolower(trim($in));
		return substr($in,0,2) == 'on' || in_array( substr($in,0,1), array('1','t','y'));
	}
}

