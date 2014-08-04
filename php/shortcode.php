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
		'image'=>FALSE,
	), $atts );
	$attributes['usesummarydisplay'] = OpenACalendar_shortcode_attribute_to_boolean($attributes['usesummarydisplay']);
	
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";

	$html = '<div class="OpenACalendarListEvents">';

	if ($attributes['poolid']) {
	
		foreach(OpenACalendar_db_getNextEventsForPool($attributes['poolid'], $attributes['eventcount']) as $event) {
			$url = $attributes['url'] == 'url' ? $event->getUrl() : $event->getSiteurl();
			$html .= '<div class="OpenACalendarWidgetListEventsEvent">';
			// image
			if (strtolower($attributes['image']) == 'full' && $event->getHasImage() ) {
				$html .= '<div class="OpenACalendarWidgetListEventsEventImage"><a href="'.htmlspecialchars($url).'">'.
					'<img src="'.htmlspecialchars($event->getImageUrlFull()).'" alt="'.htmlspecialchars($event->getImageTitle()." ".$event->getImageSourceText()).'">'.
					'</a></div>';
			} else if (strtolower($attributes['image']) == 'normal' && $event->getHasImage() ) {
				$html .= '<div class="OpenACalendarWidgetListEventsEventImage"><a href="'.htmlspecialchars($url).'">'.
					'<img src="'.htmlspecialchars($event->getImageUrlNormal()).'" alt="'.htmlspecialchars($event->getImageTitle()." ".$event->getImageSourceText()).'">'.
					'</a></div>';
			} else if (intval($attributes['image']) && intval($attributes['image']) <= 500 && $event->getHasImage() ) {
				$html .= '<div class="OpenACalendarWidgetListEventsEventImage"><a href="'.htmlspecialchars($url).'">'.
					'<img src="'.htmlspecialchars($event->getImageUrlNormal()).'" alt="'.htmlspecialchars($event->getImageTitle()." ".$event->getImageSourceText()).'" style="max-width: '.intval($attributes['image']).'px; max-height: '.intval($attributes['image']).'">'.
					'</a></div>';
			} else if (intval($attributes['image']) && $event->getHasImage() ) {
				$html .= '<div class="OpenACalendarWidgetListEventsEventImage"><a href="'.htmlspecialchars($url).'">'.
					'<img src="'.htmlspecialchars($event->getImageUrlFull()).'" alt="'.htmlspecialchars($event->getImageTitle()." ".$event->getImageSourceText()).'" style="max-width: '.intval($attributes['image']).'px; max-height: '.intval($attributes['image']).'">'.
					'</a></div>';
			}
			// start and end
			$html .= '<div class="OpenACalendarWidgetListEventsEventContent">';
			$end = null;
			if ($attributes['endformatsameday'] || $attributes['endformat']) {
				$format = $attributes['endformat'];
				if ($event->isStartAndEndOnSameDay($event->getTimezone()) && $attributes['endformatsameday']) {
					$format = $attributes['endformatsameday'];
				}
				$end = $event->getEndAtAsString($event->getTimezone(), $format);
			}
			if ($end) {
				$html .= '<div class="OpenACalendarWidgetListEventsDate">'.$event->getStartAtAsString($event->getTimezone(), $attributes['startformat']).
					$attributes['startandenddivider'].$end.'</div>';
			} else {
				$html .= '<div class="OpenACalendarWidgetListEventsDate">'.$event->getStartAtAsString($event->getTimezone(), $attributes['startformat']).'</div>';
			}
			// summary
			$html .= '<div class="OpenACalendarWidgetListEventsSummary"><a href="'.htmlspecialchars($url).'">'.
				htmlspecialchars($attributes['usesummarydisplay'] ? $event->getSummaryDisplay() : $event->getSummary()).
				'</a></div>';
			// description
			if ($attributes['descriptionmaxlength'] > 0) {
				$html .= '<div class="OpenACalendarWidgetListEventsDescription">'.htmlspecialchars($event->getDescriptionTruncated($attributes['descriptionmaxlength'])).'</div>';
			}
			// link
			$html .= '<a class="OpenACalendarWidgetListEventsMoreLink" href="' . htmlspecialchars($url). '">More Info</a>';
			$html .= '</div><div class="OpenACalendarWidgetListEventsEventAfterContent"></div></div>';
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

