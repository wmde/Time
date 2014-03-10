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
 */
class CalendarModelParser extends StringValueParser {

	/**
	 * Regex pattern constant matching the parable calendar models
	 * should be used as an insensitive to match all cases
	 */
	const MODEL_PATTERN = '(Gregorian|Julian|)';

	protected function stringParse( $value ) {
		$value = strtolower( $value );

		switch ( $value ) {
			case '':
			case 'gregorian':
				return TimeFormatter::CALENDAR_GREGORIAN;
			case 'julian':
				return TimeFormatter::CALENDAR_JULIAN;
		}

		throw new ParseException( 'Cannot parse calendar model: ' . $value );
	}
}
