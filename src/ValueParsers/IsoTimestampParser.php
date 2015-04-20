<?php

namespace ValueParsers;

use DataValues\IllegalValueException;
use DataValues\TimeValue;
use InvalidArgumentException;

/**
 * ValueParser that parses various string representations of time values, in YMD ordered formats
 * resembling ISO 8601, e.g. +2013-01-01T00:00:00Z. While the parser tries to be relaxed, certain
 * aspects of the ISO norm are obligatory: The order must be YMD. All elements but the year must
 * have 2 digits. The seperation characters must be dashes (in the date part), "T" and colons (in
 * the time part).
 *
 * The parser refuses to parse strings that can be parsed differently by other, locale-aware
 * parsers, e.g. 01-02-03 can be in YMD, DMY or MDY order depending on the language.
 *
 * @since 0.7 renamed from TimeParser to IsoTimestampParser.
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class IsoTimestampParser extends StringValueParser {

	const FORMAT_NAME = 'time';

	const OPT_PRECISION = 'precision';
	const OPT_CALENDAR = 'calendar';

	const CALENDAR_GREGORIAN = 'http://www.wikidata.org/entity/Q1985727';
	const CALENDAR_JULIAN = 'http://www.wikidata.org/entity/Q1985786';
	const PRECISION_NONE = 'noprecision';

	/**
	 * @var CalendarModelParser
	 */
	private $calendarModelParser;

	/**
	 * @param CalendarModelParser|null $calendarModelParser
	 * @param ParserOptions|null $options
	 */
	public function __construct(
		CalendarModelParser $calendarModelParser = null,
		ParserOptions $options = null
	) {
		parent::__construct( $options );

		$this->defaultOption( self::OPT_CALENDAR, self::CALENDAR_GREGORIAN );
		$this->defaultOption( self::OPT_PRECISION, self::PRECISION_NONE );

		$this->calendarModelParser = $calendarModelParser ?: new CalendarModelParser( $this->options );
	}

	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 * @throws ParseException
	 * @return TimeValue
	 */
	protected function stringParse( $value ) {
		if ( !is_string( $value ) ) {
			throw new InvalidArgumentException( '$value must be a string' );
		}

		$timeParts = $this->splitTimeString( $value );
		// Pad sign with 1 plus, year with 16 zeros and hour, minute and second with 2 zeros
		$time = vsprintf( '%\'+1s%016s-%s-%sT%02s:%02s:%02sZ', $timeParts );
		$precision = $this->getPrecision( $timeParts );
		$calendarModel = $this->getCalendarModel( $timeParts[7] );

		try {
			return new TimeValue( $time, 0, 0, 0, $precision, $calendarModel );
		} catch ( IllegalValueException $ex ) {
			throw new ParseException( $ex->getMessage(), $value, self::FORMAT_NAME );
		}
	}

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return string[] Array with index 0 => sign, 1 => year, 2 => month, 3 => day, 4 => hour,
	 * 5 => minute, 6 => second and 7 => calendar model.
	 */
	private function splitTimeString( $value ) {
		$pattern = '@^\s*'                                                //leading spaces
			. "([-+\xE2\x88\x92]?)\\s*"                                   //sign
			. '(\d{1,16})-(\d{2})-(\d{2})'                                //year, month and day
			. '(?:T(\d{2}):(\d{2})(?::(\d{2}))?)?'                        //hour, minute and second
			. 'Z?'                                                        //time zone
			. '\s*\(?\s*' . CalendarModelParser::MODEL_PATTERN . '\s*\)?' //calendar model
			. '\s*$@iu';                                                  //trailing spaces

		if ( !preg_match( $pattern, $value, $matches ) ) {
			throw new ParseException( 'Malformed time', $value, self::FORMAT_NAME );
		} elseif ( strlen( $matches[2] ) < 3 && $matches[2] < 60 && $matches[5] === '' ) {
			throw new ParseException( 'Not enough information to decide if the format is YMD',
				$value, self::FORMAT_NAME );
		} elseif ( $matches[3] > 12 ) {
			throw new ParseException( 'Month out of range', $value, self::FORMAT_NAME );
		} elseif ( $matches[4] > 31 ) {
			throw new ParseException( 'Day out of range', $value, self::FORMAT_NAME );
		} elseif ( $matches[5] > 23 ) {
			throw new ParseException( 'Hour out of range', $value, self::FORMAT_NAME );
		} elseif ( $matches[6] > 59 ) {
			throw new ParseException( 'Minute out of range', $value, self::FORMAT_NAME );
		} elseif ( $matches[7] > 62 ) {
			throw new ParseException( 'Second out of range', $value, self::FORMAT_NAME );
		}


		$matches = array_slice( $matches, 1 );
		$matches[0] = str_replace( "\xE2\x88\x92", '-', $matches[0] );

		return $matches;
	}

	/**
	 * @param string[] $timeParts Array with index 0 => sign, 1 => year, 2 => month, etc.
	 *
	 * @return int One of the TimeValue::PRECISION_... constants.
	 */
	private function getPrecision( array $timeParts ) {
		if ( intval( $timeParts[6] ) > 0 ) {
			$precision = TimeValue::PRECISION_SECOND;
		} elseif ( intval( $timeParts[5] ) > 0 ) {
			$precision = TimeValue::PRECISION_MINUTE;
		} elseif ( intval( $timeParts[4] ) > 0 ) {
			$precision = TimeValue::PRECISION_HOUR;
		} elseif ( intval( $timeParts[3] ) > 0 ) {
			$precision = TimeValue::PRECISION_DAY;
		} elseif ( intval( $timeParts[2] ) > 0 ) {
			$precision = TimeValue::PRECISION_MONTH;
		} else {
			$precision = $this->getPrecisionFromYear( $timeParts[1] );
		}

		$option = $this->getOption( self::OPT_PRECISION );

		// It's impossible to increase precision via option, e.g. to month if no month is given
		if ( is_int( $option ) && $option <= $precision ) {
			return $option;
		}

		return $precision;
	}

	/**
	 * @param string $year
	 *
	 * @return int One of the TimeValue::PRECISION_... constants.
	 */
	private function getPrecisionFromYear( $year ) {
		// default to year precision for range 4000 BC to 4000
		if ( $year >= -4000 && $year <= 4000 ) {
			return TimeValue::PRECISION_YEAR;
		}

		$rightZeros = strlen( $year ) - strlen( rtrim( $year, '0' ) );
		$precision = TimeValue::PRECISION_YEAR - $rightZeros;
		if ( $precision < TimeValue::PRECISION_Ga ) {
			$precision = TimeValue::PRECISION_Ga;
		}

		return $precision;
	}

	/**
	 * @param string $calendarModelName
	 *
	 * @return string
	 */
	private function getCalendarModel( $calendarModelName ) {
		if ( !empty( $calendarModelName ) ) {
			return $this->calendarModelParser->parse( $calendarModelName );
		}

		// The calendar model is an URI and URIs can't be case-insensitive
		switch ( $this->getOption( self::OPT_CALENDAR ) ) {
			case self::CALENDAR_JULIAN:
				return self::CALENDAR_JULIAN;
			default:
				return self::CALENDAR_GREGORIAN;
		}
	}

}
