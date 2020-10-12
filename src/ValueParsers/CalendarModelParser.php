<?php

namespace ValueParsers;

use DataValues\TimeValue;

/**
 * ValueParser that parses the string representation of a calendar model.
 *
 * @since 0.2
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class CalendarModelParser extends StringValueParser {

	private const FORMAT_NAME = 'calendar-model';

	/**
	 * Option to provide localized calendar model names for unlocalization. Must be an array mapping
	 * localized calendar model names to URIs.
	 *
	 * @see TimeFormatter::OPT_CALENDARNAMES
	 */
	public const OPT_CALENDAR_MODEL_URIS = 'calendar-model-uris';

	/**
	 * @deprecated Do not use.
	 *
	 * Regex pattern constant matching the parable calendar models
	 * should be used as an insensitive to match all cases
	 *
	 * TODO: How crucial is it that this regex is in sync with the list below?
	 */
	public const MODEL_PATTERN = '(Gregorian|Julian|)';

	/**
	 * @param ParserOptions|null $options
	 */
	public function __construct( ParserOptions $options = null ) {
		parent::__construct( $options );

		$this->defaultOption( self::OPT_CALENDAR_MODEL_URIS, array() );
	}

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return string
	 */
	protected function stringParse( $value ) {
		$uris = $this->getOption( self::OPT_CALENDAR_MODEL_URIS );
		if ( array_key_exists( $value, $uris ) ) {
			return $uris[$value];
		}

		switch ( $value ) {
			case TimeValue::CALENDAR_GREGORIAN:
				return TimeValue::CALENDAR_GREGORIAN;
			case TimeValue::CALENDAR_JULIAN:
				return TimeValue::CALENDAR_JULIAN;
		}

		return $this->getCalendarModelUriFromKey( $value );
	}

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return string|null
	 */
	private function getCalendarModelUriFromKey( $value ) {
		$key = strtolower( trim( $value ) );

		// TODO: What about abbreviations, e.g. "greg"?
		switch ( $key ) {
			case '':
			case 'gregorian':
			case 'western':
			case 'christian':
				return TimeValue::CALENDAR_GREGORIAN;
			case 'julian':
				return TimeValue::CALENDAR_JULIAN;
		}

		throw new ParseException( 'Cannot parse calendar model', $value, self::FORMAT_NAME );
	}

}
