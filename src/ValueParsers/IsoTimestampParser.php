<?php

namespace ValueParsers;

use DataValues\IllegalValueException;
use DataValues\TimeValue;

/**
 * ValueParser that parses various string representations of time values, in YMD ordered formats
 * resembling ISO 8601, e.g. +2013-01-01T00:00:00Z. While the parser tries to be relaxed, certain
 * aspects of the ISO norm are obligatory: The order must be YMD. All elements but the year must
 * have 2 digits. The separation characters must be dashes (in the date part), "T" and colons (in
 * the time part).
 *
 * The parser refuses to parse strings that can be parsed differently by other, locale-aware
 * parsers, e.g. 01-02-03 can be in YMD, DMY or MDY order depending on the language.
 *
 * @since 0.7 renamed from TimeParser to IsoTimestampParser.
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 * @author Daniel Kinzler
 */
class IsoTimestampParser extends StringValueParser {

	private const FORMAT_NAME = 'iso-timestamp';

	/**
	 * Option to override the precision auto-detection and set a specific precision. Should be an
	 * integer or string containing one of the TimeValue::PRECISION_... constants.
	 */
	public const OPT_PRECISION = 'precision';

	/**
	 * Option to override the calendar model auto-detection and set a specific calendar model URI.
	 * Should be one of the TimeValue::CALENDAR_... constants.
	 */
	public const OPT_CALENDAR = 'calendar';

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_GREGORIAN instead
	 */
	public const CALENDAR_GREGORIAN = TimeValue::CALENDAR_GREGORIAN;

	/**
	 * @deprecated since 0.7.1, use TimeValue::CALENDAR_JULIAN instead
	 */
	public const CALENDAR_JULIAN = TimeValue::CALENDAR_JULIAN;

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

		$this->defaultOption( self::OPT_CALENDAR, null );
		$this->defaultOption( self::OPT_PRECISION, null );

		$this->calendarModelParser = $calendarModelParser ?: new CalendarModelParser( $this->options );
	}

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	protected function stringParse( $value ) {
		try {
			$timeParts = $this->splitTimeString( $value );
		} catch ( ParseException $ex ) {
			throw new ParseException( $ex->getMessage(), $value, self::FORMAT_NAME );
		}

		// Pad sign with 1 plus, year with 4 zeros and hour, minute and second with 2 zeros
		$timestamp = vsprintf( '%\'+1s%04s-%s-%sT%02s:%02s:%02sZ', $timeParts );
		$precision = $this->getPrecision( $timeParts );
		$calendarModel = $this->getCalendarModel( $timeParts );

		try {
			return new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel );
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
		$pattern = '@^\s*'                                                // leading spaces
			. "([-+\xE2\x88\x92]?)\\s*"                                   // sign
			. '(\d{1,16})-(\d{2})-(\d{2})'                                // year-month-day
			. '(?:T(\d{2}):?(\d{2})(?::?(\d{2}))?)?'                      // hour:minute:second
			. 'Z?'                                                        // time zone
			. '\s*\(?\s*' . CalendarModelParser::MODEL_PATTERN . '\s*\)?' // calendar model
			. '\s*$@iu';                                                  // trailing spaces

		if ( !preg_match( $pattern, $value, $matches ) ) {
			throw new ParseException( 'Malformed time' );
		}

		list( , $sign, $year, $month, $day, $hour, $minute, $second, $calendarModel ) = $matches;

		if ( $sign === ''
			&& strlen( $year ) < 3
			&& $year < 32
			&& $hour === ''
		) {
			throw new ParseException( 'Not enough information to decide if the format is YMD' );
		}

		if ( $month > 12 ) {
			throw new ParseException( 'Month out of range' );
		} elseif ( $day > 31 ) {
			throw new ParseException( 'Day out of range' );
		} elseif ( $hour > 23 ) {
			throw new ParseException( 'Hour out of range' );
		} elseif ( $minute > 59 ) {
			throw new ParseException( 'Minute out of range' );
		} elseif ( $second > 61 ) {
			throw new ParseException( 'Second out of range' );
		}

		if ( $month < 1 && $day > 0 ) {
			throw new ParseException( 'Cannot have a day with no month' );
		}

		if ( $day < 1 && ( $hour > 0 || $minute > 0 || $second > 0 ) ) {
			throw new ParseException( 'Cannot have hour, minute or second with no day' );
		}

		$sign = str_replace( "\xE2\x88\x92", '-', $sign );

		return array( $sign, $year, $month, $day, $hour, $minute, $second, $calendarModel );
	}

	/**
	 * @param string[] $timeParts Array with index 0 => sign, 1 => year, 2 => month, etc.
	 *
	 * @throws ParseException
	 * @return int One of the TimeValue::PRECISION_... constants.
	 */
	private function getPrecision( array $timeParts ) {
		if ( $timeParts[6] > 0 ) {
			$precision = TimeValue::PRECISION_SECOND;
		} elseif ( $timeParts[5] > 0 ) {
			$precision = TimeValue::PRECISION_MINUTE;
		} elseif ( $timeParts[4] > 0 ) {
			$precision = TimeValue::PRECISION_HOUR;
		} elseif ( $timeParts[3] > 0
			// Cannot have a day with no month, fall back to year precision.
			&& $timeParts[2] > 0
		) {
			$precision = TimeValue::PRECISION_DAY;
		} elseif ( $timeParts[2] > 0 ) {
			$precision = TimeValue::PRECISION_MONTH;
		} else {
			$precision = $this->getPrecisionFromYear( $timeParts[1] );
		}

		$option = $this->getOption( self::OPT_PRECISION );

		if ( $option !== null ) {
			if ( !is_int( $option ) && ( !is_string( $option ) || !ctype_digit( $option ) ) ) {
				throw new ParseException( 'Precision must be an integer' );
			}

			$option = (int)$option;

			// It's impossible to increase the detected precision via option, e.g. from year to month if
			// no month is given. If a day is given it can be increased, relevant for midnight.
			if ( $option <= $precision || $precision >= TimeValue::PRECISION_DAY ) {
				return $option;
			}
		}

		return $precision;
	}

	/**
	 * @param string $unsignedYear
	 *
	 * @return int One of the TimeValue::PRECISION_... constants.
	 */
	private function getPrecisionFromYear( $unsignedYear ) {
		// default to year precision for range 4000 BC to 4000
		if ( $unsignedYear <= 4000 ) {
			return TimeValue::PRECISION_YEAR;
		}

		$rightZeros = strlen( $unsignedYear ) - strlen( rtrim( $unsignedYear, '0' ) );
		$precision = TimeValue::PRECISION_YEAR - $rightZeros;
		if ( $precision < TimeValue::PRECISION_YEAR1G ) {
			$precision = TimeValue::PRECISION_YEAR1G;
		}

		return $precision;
	}

	/**
	 * Determines the calendar model. The calendar model is determined as follows:
	 *
	 * - if $timeParts[7] is set, use $this->calendarModelParser to parse it into a URI.
	 * - otherwise, if $this->getOption( self::OPT_CALENDAR ) is not null, use
	 *   $this->calendarModelParser to parse it into a URI.
	 * - otherwise, use self::CALENDAR_JULIAN for dates before 1583, and self::CALENDAR_GREGORIAN
	 *   for later dates.
	 *
	 * @note Keep this in sync with HtmlTimeFormatter::getDefaultCalendar().
	 *
	 * @param string[] $timeParts as returned by splitTimeString()
	 *
	 * @return string URI
	 */
	private function getCalendarModel( array $timeParts ) {
		list( $sign, $unsignedYear, , , , , , $calendarModel ) = $timeParts;

		if ( !empty( $calendarModel ) ) {
			return $this->calendarModelParser->parse( $calendarModel );
		}

		$option = $this->getOption( self::OPT_CALENDAR );

		if ( $option !== null ) {
			return $this->calendarModelParser->parse( $option );
		}

		// The Gregorian calendar was introduced in October 1582,
		// so we'll default to Julian for all years before that.
		return $sign === '-' || $unsignedYear <= 1582
			? TimeValue::CALENDAR_JULIAN
			: TimeValue::CALENDAR_GREGORIAN;
	}

}
