<?php

namespace ValueParsers;

use DataValues\TimeValue;

/**
 * A straight time parser with a strict rule set that only accepts YMD, DMY, MDY and YDM formatted
 * dates if they cannot be confused with an other format.
 *
 * @since 0.8.1
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class YearMonthDayTimeParser extends StringValueParser {

	private const FORMAT_NAME = 'year-month-day';

	/**
	 * @var ValueParser
	 */
	private $eraParser;

	/**
	 * @var ValueParser
	 */
	private $isoTimestampParser;

	/**
	 * @param ValueParser|null $eraParser String parser that detects signs, "BC" suffixes and such and
	 * returns an array with the detected sign character and the remaining value.
	 * @param ParserOptions|null $options
	 */
	public function __construct( ValueParser $eraParser = null, ParserOptions $options = null ) {
		parent::__construct( $options );

		$this->eraParser = $eraParser ?: new EraParser();
		$this->isoTimestampParser = new IsoTimestampParser( null, $this->options );
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
	 * @throws ParseException
	 * @return string[]
	 */
	private function parseYearMonthDay( $value ) {
		if ( !preg_match( '/^\D*?(-?\d+)\D+(\d+)\D+?(-?\d+)\D*$/', $value, $matches ) ) {
			throw new ParseException( 'Cannot find three numbers' );
		}

		// A 32 in the first spot cannot be confused with anything.
		if ( $matches[1] < 1 || $matches[1] > 31 ) {
			if ( $matches[3] > 12 || $matches[2] == $matches[3] ) {
				list( , $signedYear, $month, $day ) = $matches;
			} elseif ( $matches[2] > 12 ) {
				list( , $signedYear, $day, $month ) = $matches;
			} else {
				throw new ParseException( 'Cannot distinguish YDM and YMD' );
			}
		} elseif ( $matches[3] < 1 || $matches[3] > 59
			// A 59 in the third spot may be a second, but cannot if the first number is > 24.
			// A 31 in the last spot may be the day, but cannot if it's negative.
			|| ( abs( $matches[1] ) > 24 && $matches[3] > 31 )
		) {
			if ( $matches[1] > 12 || $matches[1] == $matches[2] ) {
				list( , $day, $month, $signedYear ) = $matches;
			} elseif ( $matches[2] > 12 ) {
				list( , $month, $day, $signedYear ) = $matches;
			} else {
				throw new ParseException( 'Cannot distinguish DMY and MDY' );
			}
		} else {
			// Formats DYM and MYD do not exist.
			throw new ParseException( 'Cannot identify year' );
		}

		return array( $signedYear, $month, $day );
	}

	/**
	 * @param string $signedYear
	 * @param string $month
	 * @param string $day
	 *
	 * @throws ParseException
	 * @return TimeValue
	 */
	private function newTimeValue( $signedYear, $month, $day ) {
		if ( $month < 1 || $month > 12 ) {
			throw new ParseException( 'Month out of range' );
		} elseif ( $day < 1 || $day > 31 ) {
			throw new ParseException( 'Day out of range' );
		}

		return $this->isoTimestampParser->parse(
			sprintf( '%s-%02s-%02sT00:00:00Z', $signedYear, $month, $day )
		);
	}

}
