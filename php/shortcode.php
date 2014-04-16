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
		'eventcount'=>20,
	), $atts );
	
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";

	$html = '<div class="OpenACalendarListEvents">';

	if ($attributes['poolid']) {
	
		foreach(OpenACalendar_db_getNextEventsForPool($attributes['poolid'], $attributes['eventcount']) as $event) {
			$html .= '<div class="OpenACalendarWidgetListEventsEvent">';
			$html .= '<div class="OpenACalendarWidgetListEventsDate">'.$event->getStartAtAsString($event->getTimezone(), $attributes['startformat']).'</div>';
			$html .= '<div class="OpenACalendarWidgetListEventsSummary"><a href="'.htmlspecialchars($event->getSiteurl()).'">'.
				htmlspecialchars($attributes['usesummarydisplay'] ? $event->getSummaryDisplay() : $event->getSummary()).
				'</a></div>';	
			if ($attributes['descriptionmaxlength'] > 0) {
				$html .= '<div class="OpenACalendarWidgetListEventsDescription">'.htmlspecialchars($event->getDescriptionTruncated($attributes['descriptionmaxlength'])).'</div>';
			}
			$html .= '<a class="OpenACalendarWidgetListEventsMoreLink" href="' . $event->getSiteurl() . '">More Info</a>';
			$html .= '</div>';
		}
		
	} else {
		
	}

	$html .= '</div>';
	

	return $html;
}

