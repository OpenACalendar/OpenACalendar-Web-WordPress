<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

require_once __DIR__.DIRECTORY_SEPARATOR."database.php";

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
				
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo '<div id="OpenACalendarListEvents'.$args['widget_id'].'">';

		foreach(OpenACalendar_db_getNextEventsForPool($poolID, $eventCount) as $event) {
			echo '<div class="OpenACalendarWidgetListEventsEvent">';
			// TODO date
			echo '<div class="OpenACalendarWidgetListEventsSummary"><a href="'.htmlspecialchars($event->getSiteurl()).'">'.
				htmlspecialchars($eventusesummarydisplay ? $event->getSummaryDisplay() : $event->getSummary()).
				'</a></div>';	
			// TODO description
			// TODO moreinfo
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
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'poolid' ); ?>"><?php _e( 'Event Pool ID:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'poolid' ); ?>" name="<?php echo $this->get_field_name( 'poolid' ); ?>" type="text" value="<?php echo esc_attr( $poolID ); ?>">
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
		$instance['eventusesummarydisplay'] = ( isset( $new_instance['eventusesummarydisplay'] ) ) ? 1 : 0;
				
		return $instance;
	}
}

