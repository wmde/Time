<?php

namespace ValueParsers;

use ValueFormatters\TimeFormatter;

/**
 * ValueParser that parses the string representation of a calendar model.
 *
 * @since 0.2
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class CalendarModelParser extends StringValueParser {

	const FORMAT_NAME = 'calendar-model';

	/**
	 * Regex pattern constant matching the parable calendar models
	 * should be used as an insensitive to match all cases
	 *
	 * TODO: How important is it that this regex is in sync with the list below?
	 */
	const MODEL_PATTERN = '(Gregorian|Julian|)';

	protected function stringParse( $value ) {
		$key = trim( $value );

		// TODO: What about abbreviation, e.g. "greg" and "jul"?
		// TODO: What about localizations?
		if ( $key === ''
			|| $key === TimeFormatter::CALENDAR_GREGORIAN
			|| strtolower( $key ) === 'gregorian'
		) {
			return TimeFormatter::CALENDAR_GREGORIAN;
		} elseif ( $key === TimeFormatter::CALENDAR_JULIAN
			|| strtolower( $key ) === 'julian'
		) {
			return TimeFormatter::CALENDAR_JULIAN;
		}

		throw new ParseException( 'Cannot parse calendar model', $value, self::FORMAT_NAME );
	}

}
