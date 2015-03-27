<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";

class OpenACalendarLocalEventsWidget extends WP_Widget {
	
	function __construct() {
		parent::__construct(
			'openacalendar_local_events_widget', // Base ID
			__('OpenACalendar Events', 'text_domain'), // Name
			array( 'description' => __( 'List OpenAcalendar Events', 'text_domain' ), ) // Args
		);
		wp_enqueue_style( 'openacalendar-events-widget', plugins_url().'/openacalendar/css/listeventswidget.css' );
	}

	protected static $includedAssets = false;
	
	const OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT = 300;
	const OPTION_DEFAULT_EVENT_COUNT = 5;
	const OPTION_DEFAULT_USE_SUMMARY_DISPLAY = 1;
	const OPTION_DEFAULT_EVENT_SHOW_MORE_LINK = 1;
	const OPTION_DEFAULT_EVENT_LINK_OPEN_IN_NEW_WINDOW = 0;
	const OPTION_DEFAULT_START_FORMAT = 'D jS M g:ia';
	const OPTION_DEFAULT_URL = "site";
	const OPTION_DEFAULT_MORE_EVENTS_LINK = 0;
	const OPTION_DEFAULT_MORE_EVENTS_LINK_URL = "";

	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$poolID = apply_filters( 'poolid', $instance['poolid'] );
		$descriptionMaxLength = isset($instance['descriptionmaxlength']) ? 
				intval($instance['descriptionmaxlength']) : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$eventCount= isset($instance['eventcount']) ? 
				intval($instance['eventcount']) : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$eventusesummarydisplay = isset($instance['eventusesummarydisplay']) ? 
				intval($instance['eventusesummarydisplay']) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_USE_SUMMARY_DISPLAY;
		$startformat = isset($instance['startformat']) ? 
				$instance['startformat'] : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_START_FORMAT;
		$whichURL = isset($instance['url']) ? 
				$instance['url']: 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_URL;
		$eventshowmorelink = isset($instance['eventshowmorelink']) ?
			intval($instance['eventshowmorelink']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_SHOW_MORE_LINK;
		$eventlinkopeninnewwindow = isset($instance['eventlinkopeninnewwindow']) ?
			intval($instance['eventlinkopeninnewwindow']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_LINK_OPEN_IN_NEW_WINDOW;
		$moreeventslink = isset($instance['moreeventslink']) ?
			intval($instance['moreeventslink']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_MORE_EVENTS_LINK;
		$moreeventslinkurl = isset($instance['moreeventslinkurl']) ?
			$instance['moreeventslinkurl'] :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_MORE_EVENTS_LINK_URL;

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo '<div class="OpenACalendarListEvents" id="OpenACalendarListEvents'.$args['widget_id'].'">';

		foreach(OpenACalendar_db_getNextEventsForPool($poolID, $eventCount) as $event) {
			$url = $whichURL == 'url' ? $event->getUrl() : $event->getSiteurl();
			echo '<div class="OpenACalendarWidgetListEventsEvent" itemscope itemtype="http://schema.org/Event">';
			echo '<div class="OpenACalendarWidgetListEventsDate"><time datetime="'.$event->getStartAtAsString($event->getTimezone(), 'c').'" itemprop="startDate">'.$event->getStartAtAsString($event->getTimezone(), $startformat).'</time></div>';
			echo '<div class="OpenACalendarWidgetListEventsSummary" itemprop="name"><a href="'.esc_attr($url).'"'.($eventlinkopeninnewwindow?' target="_blank"':'').' itemprop="url">'.
				htmlspecialchars($eventusesummarydisplay ? $event->getSummaryDisplay() : $event->getSummary()).
				'</a></div>';	
			if ($descriptionMaxLength > 0) {
				echo '<div class="OpenACalendarWidgetListEventsDescription" itemprop="description">'.htmlspecialchars($event->getDescriptionTruncated($descriptionMaxLength)).'</div>';
			}
			if ($eventshowmorelink) {
				echo '<a class="OpenACalendarWidgetListEventsMoreLink" href="' . esc_attr($url) . '"'.($eventlinkopeninnewwindow?' target="_blank"':'').' itemprop="url">More Info</a>';
			}
			echo '</div>';
		}

		if ($moreeventslink && !$moreeventslinkurl) {
			$sources = OpenACalendar_db_getCurrentSourcesForPool($poolID);
			if (count($sources) > 0) {
				$moreeventslinkurl = $sources[0]->getWebURL();
			}
		}

		if ($moreeventslink && $moreeventslinkurl) {
			echo '<div class="OpenACalendarWidgetListEventsMoreEvents">';
			echo '<a href="'.esc_attr($moreeventslinkurl).'">More events</a>';
			echo '</div>';
		}

		echo '</div>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] :  __( 'Events', 'text_domain' );
		$poolID = apply_filters( 'poolid', isset($instance['poolid']) ? $instance['poolid'] : null );
		$descriptionMaxLength = isset($instance['descriptionmaxlength']) ? 
				intval($instance['descriptionmaxlength']) : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$eventCount= isset($instance['eventcount']) ? 
				intval($instance['eventcount']) : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$eventusesummarydisplay = isset( $instance[ 'eventusesummarydisplay' ] ) ? 
				intval($instance[ 'eventusesummarydisplay' ]):
				OpenACalendarEventsWidget::OPTION_DEFAULT_USE_SUMMARY_DISPLAY;
		$startformat = isset($instance['startformat']) ? 
				$instance['startformat'] : 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_START_FORMAT;
		$whichURL = isset($instance['url']) ? 
				$instance['url']: 
				OpenACalendarLocalEventsWidget::OPTION_DEFAULT_URL;
		$eventshowmorelink = isset($instance['eventshowmorelink']) ?
			intval($instance['eventshowmorelink']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_SHOW_MORE_LINK;
		$eventlinkopeninnewwindow = isset($instance['eventlinkopeninnewwindow']) ?
			intval($instance['eventlinkopeninnewwindow']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_EVENT_LINK_OPEN_IN_NEW_WINDOW;
		$moreeventslink = isset($instance['moreeventslink']) ?
			intval($instance['moreeventslink']) :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_MORE_EVENTS_LINK;
		$moreeventslinkurl = isset($instance['moreeventslinkurl']) ?
			$instance['moreeventslinkurl'] :
			OpenACalendarLocalEventsWidget::OPTION_DEFAULT_MORE_EVENTS_LINK_URL;

		$pools = OpenACalendar_db_getCurrentPools();

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'poolid' ); ?>"><?php _e( 'Event Pool:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'poolid' ); ?>" name="<?php echo $this->get_field_name( 'poolid' ); ?>">
		<?php foreach($pools as $pool)  { ?><option value="<?php echo $pool['id']; ?>" <?php if ($pool['id'] == $poolID) { ?>selected="selected" <?php } ?>><?php echo htmlspecialchars($pool['title']); ?></option><?php } ?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'descriptionmaxlength' ); ?>"><?php _e( 'Description Max Length:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'descriptionmaxlength' ); ?>" name="<?php echo $this->get_field_name( 'descriptionmaxlength' ); ?>" type="text" value="<?php echo esc_attr( $descriptionMaxLength ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventcount' ); ?>"><?php _e( 'Max Events Shown:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'eventcount' ); ?>" name="<?php echo $this->get_field_name( 'eventcount' ); ?>" type="text" value="<?php echo esc_attr( $eventCount ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventusesummarydisplay' ); ?>"><?php _e( 'Use Fuller Event Titles:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'eventusesummarydisplay' ); ?>" name="<?php echo $this->get_field_name( 'eventusesummarydisplay' ); ?>" type="checkbox" value="1" <?php if ($eventusesummarydisplay) { echo "checked"; }; ?>>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventshowmorelink' ); ?>"><?php _e( 'Show more link for each event:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'eventshowmorelink' ); ?>" name="<?php echo $this->get_field_name( 'eventshowmorelink' ); ?>" type="checkbox" value="1" <?php if ($eventshowmorelink) { echo "checked"; }; ?>>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventlinkopeninnewwindow' ); ?>"><?php _e( 'Open links to an event in a new window:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'eventlinkopeninnewwindow' ); ?>" name="<?php echo $this->get_field_name( 'eventlinkopeninnewwindow' ); ?>" type="checkbox" value="1" <?php if ($eventlinkopeninnewwindow) { echo "checked"; }; ?>>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'startformat' ); ?>"><?php _e( 'Format start time/date:' ); ?> (<a href="http://php.net/date" target="_blank"><?php _e( 'PHP Format' ) ?></a>)</label>
		<input id="<?php echo $this->get_field_id( 'startformat' ); ?>" name="<?php echo $this->get_field_name( 'startformat' ); ?>" type="text" value="<?php echo esc_attr($startformat); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'moreeventslink' ); ?>"><?php _e( 'Show More Events Link:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'moreeventslink' ); ?>" name="<?php echo $this->get_field_name( 'moreeventslink' ); ?>" type="checkbox" value="1" <?php if ($moreeventslink) { echo "checked"; }; ?>>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'moreeventslinkurl' ); ?>"><?php _e( 'More Events Link - URL:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'moreeventslinkurl' ); ?>" name="<?php echo $this->get_field_name( 'moreeventslinkurl' ); ?>" type="text" value="<?php echo esc_attr($moreeventslinkurl); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Which URL:' ); ?></label> 
		<select  id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>">
			<option value="site" <?php if ($whichURL == 'site') { ?>selected<?php } ?>><?php _e( 'Event on Calendar' ); ?></option>
			<option value="url" <?php if ($whichURL == 'url') { ?>selected<?php } ?>><?php _e( 'Event URL' ); ?></option>
		</select>
		</p>
		<?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['poolid'] = ( ! empty( $new_instance['poolid'] ) ) ? intval( $new_instance['poolid'] ) : '';
		$instance['descriptionmaxlength'] = ( isset( $new_instance['descriptionmaxlength'] ) ) ? 
				intval( $new_instance['descriptionmaxlength'] ) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$instance['eventcount'] = ( isset( $new_instance['eventcount'] ) ) ? 
				intval( $new_instance['eventcount'] ) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$instance['eventlinkopeninnewwindow'] = ( isset( $new_instance['eventlinkopeninnewwindow'] ) && $new_instance['eventlinkopeninnewwindow'] == '1' ) ? 1 : 0;
		$instance['eventshowmorelink'] = ( isset( $new_instance['eventshowmorelink'] )  && $new_instance['eventshowmorelink'] == '1') ? 1 : 0;
		$instance['eventusesummarydisplay'] = ( isset( $new_instance['eventusesummarydisplay'] )  && $new_instance['eventusesummarydisplay'] == '1') ? 1 : 0;
		$instance['startformat'] = ( ! empty( $new_instance['startformat'] ) ) ?  $new_instance['startformat']  : OpenACalendarLocalEventsWidget::OPTION_DEFAULT_START_FORMAT;
		$instance['url'] =  ( ! empty( $new_instance['url'] ) ) ?  $new_instance['url']  : OpenACalendarLocalEventsWidget::OPTION_DEFAULT_URL;
		$instance['moreeventslink'] =   ( isset( $new_instance['moreeventslink'] ) && $new_instance['moreeventslink'] == '1' ) ? 1 : 0;
		$instance['moreeventslinkurl'] =  ( ! empty( $new_instance['moreeventslinkurl'] ) ) ?  $new_instance['moreeventslinkurl']  : OpenACalendarLocalEventsWidget::OPTION_DEFAULT_MORE_EVENTS_LINK_URL;
		return $instance;
	}
}

