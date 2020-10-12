<?php

namespace DataValues;

use InvalidArgumentException;

/**
 * Logical and mathematical helper functions for calculations with and conversions from TimeValue
 * objects.
 *
 * @since 0.6
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class TimeValueCalculator {

	/**
	 * Average length of a year in the Gregorian calendar in seconds, calculated via
	 * 365 + 1 / 4 - 1 / 100 + 1 / 400 = 365.2425 days.
	 */
	private const SECONDS_PER_GREGORIAN_YEAR = 31556952;

	/**
	 * This returns a Unix timestamp from a TimeValue similar to PHP's mk_time() (or strtotime()),
	 * but with no range limitations. Data type is float because PHP's 32 bit integer would
	 * clip in the year 2038.
	 *
	 * @param TimeValue $timeValue
	 *
	 * @return float seconds since 1970-01-01T00:00:00Z
	 */
	public function getTimestamp( TimeValue $timeValue ) {
		return $this->getSecondsSinceUnixEpoch( $timeValue->getTime(), $timeValue->getTimezone() );
	}

	/**
	 * @param string $time an ISO 8601 date and time
	 * @param int $timezone offset from UTC in minutes
	 *
	 * @throws InvalidArgumentException
	 * @return float seconds since 1970-01-01T00:00:00Z
	 */
	private function getSecondsSinceUnixEpoch( $time, $timezone = 0 ) {
		// Validation is done in TimeValue. As long if we found enough numbers we are fine.
		if ( !preg_match( '/([-+]?\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)/', $time, $matches )
		) {
			throw new InvalidArgumentException( "Failed to parse time value $time." );
		}
		list( , $fullYear, $month, $day, $hour, $minute, $second ) = $matches;

		// We use mktime only for the month, day and time calculation. Set the year to the smallest
		// possible in the 1970-2038 range to be safe, even if it's 1901-2038 since PHP 5.1.0.
		$year = $this->isLeapYear( $fullYear ) ? 1972 : 1970;

		$defaultTimezone = date_default_timezone_get();
		date_default_timezone_set( 'UTC' );
		// With day/month set to 0 mktime would calculate the last day of the previous month/year.
		// In the context of this calculation we must assume 0 means "start of the month/year".
		$timestamp = mktime( $hour, $minute, $second, max( 1, $month ), max( 1, $day ), $year );
		date_default_timezone_set( $defaultTimezone );

		if ( $timestamp === false ) {
			throw new InvalidArgumentException( "Failed to get epoche from time value $time." );
		}

		$missingYears = ( $fullYear < 0 ? $fullYear + 1 : $fullYear ) - $year;
		$missingLeapDays = $this->getNumberOfLeapYears( $fullYear )
			- $this->getNumberOfLeapYears( $year );

		return $timestamp + ( $missingYears * 365 + $missingLeapDays ) * 86400 - $timezone * 60;
	}

	/**
	 * @param float $year
	 *
	 * @return bool if the year is a leap year in the Gregorian calendar
	 */
	public function isLeapYear( $year ) {
		$year = $year < 0 ? ceil( $year ) + 1 : floor( $year );
		$isMultipleOf4   = fmod( $year,   4 ) === 0.0;
		$isMultipleOf100 = fmod( $year, 100 ) === 0.0;
		$isMultipleOf400 = fmod( $year, 400 ) === 0.0;
		return $isMultipleOf4 && !$isMultipleOf100 || $isMultipleOf400;
	}

	/**
	 * @param float $year
	 *
	 * @return float The number of leap years since year 1. To be more precise: The number of
	 * leap days in the range between 31 December of year 1 and 31 December of the given year.
	 */
	public function getNumberOfLeapYears( $year ) {
		$year = $year < 0 ? ceil( $year ) + 1 : floor( $year );
		return floor( $year / 4 ) - floor( $year / 100 ) + floor( $year / 400 );
	}

	/**
	 * @param int $precision One of the TimeValue::PRECISION_... constants
	 *
	 * @throws InvalidArgumentException
	 * @return float number of seconds in one unit of the given precision
	 */
	public function getSecondsForPrecision( $precision ) {
		if ( $precision <= TimeValue::PRECISION_YEAR ) {
			return self::SECONDS_PER_GREGORIAN_YEAR * pow(
				10,
				TimeValue::PRECISION_YEAR - $precision
			);
		}

		switch ( $precision ) {
			case TimeValue::PRECISION_SECOND:
				return 1;
			case TimeValue::PRECISION_MINUTE:
				return 60;
			case TimeValue::PRECISION_HOUR:
				return 3600;
			case TimeValue::PRECISION_DAY:
				return 86400;
			case TimeValue::PRECISION_MONTH:
				return self::SECONDS_PER_GREGORIAN_YEAR / 12;
		}

		throw new InvalidArgumentException( "Unable to get seconds for precision $precision." );
	}

}
