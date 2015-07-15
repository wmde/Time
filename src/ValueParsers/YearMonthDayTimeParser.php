<?php

namespace ValueParsers;

use DataValues\IllegalValueException;
use DataValues\TimeValue;

/**
 * A straight time parser with a strict rule set that only accepts YMD, DMY and MDY formatted dates
 * if they can not be confused with an other format.
 *
 * @since 0.8.1
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class YearMonthDayTimeParser extends StringValueParser {

	const FORMAT_NAME = 'datetime';

	/**
	 * @var ValueParser
	 */
	private $eraParser;

	/**
	 * @param ValueParser $eraParser String parser that detects signs, "BC" suffixes and such and
	 * returns an array with the detected sign character and the remaining value.
	 */
	public function __construct( ValueParser $eraParser = null ) {
		parent::__construct();

		$this->eraParser = $eraParser ?: new EraParser();
	}

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	protected function stringParse( $value ) {
		try {
			list( $sign, $preparsedValue ) = $this->eraParser->parse( $value );
			list( $signedYear, $month, $day ) = $this->parseYearMonthDay( $preparsedValue );

			if ( substr( $signedYear, 0, 1 ) !== '-' ) {
				$signedYear = $sign . $signedYear;
			} elseif ( $sign === '-' ) {
				throw new ParseException( 'Two eras found' );
			}

			return $this->newTimeValue( $signedYear, $month, $day );
		} catch ( ParseException $ex ) {
			throw new ParseException( $ex->getMessage(), $value, self::FORMAT_NAME );
		}
	}

	/**
	 * @param string $value
	 *
	 * @return string[]
	 */
	private function parseYearMonthDay( $value ) {
		if ( !preg_match( '/^\D*?(-?\d+)\D+(\d+)\D+?(-?\d+)\D*$/', $value, $matches ) ) {
			throw new ParseException( 'Can not find three numbers' );
		}

		if ( abs( $matches[1] ) > 31 ) {
			// A format YDM does not exist.
			if ( $matches[2] > 12 ) {
				throw new ParseException( 'Can not accept YDM' );
			}

			// Since a format YDM does not exist, this must be YMD.
			list( , $signedYear, $month, $day ) = $matches;
		} elseif ( abs( $matches[3] ) > 31 ) {
			if ( $matches[1] > 12 ) {
				list( , $day, $month, $signedYear ) = $matches;
			} elseif ( $matches[2] > 12 ) {
				list( , $month, $day, $signedYear ) = $matches;
			} else {
				throw new ParseException( 'Can not distinguish DMY and MDY' );
			}
		} else {
			// Formats DYM and MYD do not exist.
			throw new ParseException( 'Can not identify year' );
		}

		return array( $signedYear, $month, $day );
	}

	/**
	 * @param string $unsignedYear
	 * @param string $month
	 * @param string $day
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	private function newTimeValue( $unsignedYear, $month, $day ) {
		if ( $month < 1 || $month > 12 ) {
			throw new ParseException( 'Month out of range' );
		} elseif ( $day < 1 || $day > 31 ) {
			throw new ParseException( 'Day out of range' );
		}

		try {
			return new TimeValue(
				$unsignedYear . '-' . $month . '-' . $day . 'T00:00:00Z',
				0,
				0,
				0,
				TimeValue::PRECISION_DAY,
				$this->getCalendarModel( $unsignedYear )
			);
		} catch ( IllegalValueException $ex ) {
			throw new ParseException( $ex->getMessage() );
		}
	}

	/**
	 * @see IsoTimestampParser::getCalendarModel
	 *
	 * @param string $signedYear
	 *
	 * @return string URI
	 */
	private function getCalendarModel( $signedYear ) {
		// The Gregorian calendar was introduced in October 1582.
		return $signedYear <= 1582
			? TimeValue::CALENDAR_JULIAN
			: TimeValue::CALENDAR_GREGORIAN;
	}

}
