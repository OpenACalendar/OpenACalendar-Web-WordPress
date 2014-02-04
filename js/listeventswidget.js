/**
 * ican.openacalendar.org Wordpress Plugin
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


var OpenACalendarWidgetListEvents = {
	callBackFunctionCount: 0,
	place: function(datadivid, site, options) {
		var usingOptions = {
			eventCount: 5,
			descriptionMaxLength: 300,
			groupID: undefined,
			eventUseSummaryDisplay: true
		}
		for (var prop in options) {
			if (options.hasOwnProperty(prop)) {
				usingOptions[prop] = options[prop];
			}
		}
		
		OpenACalendarWidgetListEvents.callBackFunctionCount++;
		window["OpenACalendarWidgetListEventsCallBackFunction"+OpenACalendarWidgetListEvents.callBackFunctionCount] = function(data) {			
			var html = '';
			var limit = Math.min(data.data.length, usingOptions.eventCount);
			if (limit <= 0) {
				html = '<div class="OpenACalendarWidgetListEventsEventNone">No events</div>';
			} else {
				for (var i=0;i<limit;i++) {
					html += OpenACalendarWidgetListEvents.htmlFromEvent(data.data[i], usingOptions.descriptionMaxLength, usingOptions.eventUseSummaryDisplay);
				}
			}

			document.getElementById(datadivid).innerHTML=html;
		}
		var url;
		if (usingOptions.groupID) {
			url = site+"/api1/group/"+usingOptions.groupID+"/events.jsonp";
		} else if (usingOptions.venueID) {
			url = site+"/api1/venue/"+usingOptions.venueID+"/events.jsonp";
		} else if (usingOptions.countryCode) {
			url = site+"/api1/country/"+usingOptions.countryCode.toUpperCase()+"/events.jsonp";
		} else {			
			url = site+"/api1/events.jsonp";
		}
		
		var script = document.createElement("script");
		script.type = "text/javascript"; 
		script.src = url+"?callback=OpenACalendarWidgetListEventsCallBackFunction"+OpenACalendarWidgetListEvents.callBackFunctionCount;
		var headTag = document.getElementsByTagName('head').item(0);
		headTag.appendChild(script);
	},
	htmlFromEvent: function(event, descriptionMaxLength, eventUseSummaryDisplay) {
		var html = '<div class="OpenACalendarWidgetListEventsEvent">'
		html += '<div class="OpenACalendarWidgetListEventsDate">'+event.start.displaylocal+'</div>';
		html += '<div class="OpenACalendarWidgetListEventsSummary"><a href="'+event.siteurl+'">'+
			OpenACalendarWidgetListEvents.escapeHTML(eventUseSummaryDisplay ? event.summaryDisplay : event.summary)+
			'</a></div>';
		if (descriptionMaxLength > 0) {
			html += '<div class="OpenACalendarWidgetListEventsDescription">'+OpenACalendarWidgetListEvents.escapeHTMLNewLine(event.description, descriptionMaxLength)+'</div>';
			html += '<a class="OpenACalendarWidgetListEventsMoreLink" href="'+event.siteurl+'">More Info</a>';
		}
		html += '<div class="OpenACalendarWidgetListEventsClear"></div>';	
		return html+'</div>';
	},			
	escapeHTML: function(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	},
	escapeHTMLNewLine: function(str, maxLength) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		var out =  div.innerHTML;
		if (out.length > maxLength) {
			out = out.substr(0,maxLength)+" ...";
		}
		return out.replace(/\n/g,'<br>');
	}
};

