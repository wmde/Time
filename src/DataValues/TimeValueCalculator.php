<?php

namespace DataValues;

use InvalidArgumentException;

/**
 * Logical and mathematical helper functions for calculations with and conversions from TimeValue
 * objects.
 *
 * @since 0.6
 *
 * @license GPL-2.0+
 * @author Thiemo Kreuz
 */
class TimeValueCalculator {

	/**
	 * Average length of a year in the Gregorian calendar in seconds, calculated via
	 * 365 + 1 / 4 - 1 / 100 + 1 / 400 = 365.2425 days.
	 */
	const SECONDS_PER_GREGORIAN_YEAR = 31556952;

	/**
	 * Lowest positive timestamp.
	 */
	const TIMESTAMP_ZERO = '+0000000000000000-01-01T00:00:00Z';

	/**
	 * Highest positive timestamp.
	 */
	const HIGHEST_TIMESTAMP = '+9999999999999999-12-31T23:59:59Z';

	/**
	 * Maximum length for a timestamp.
	 */
	const MAX_LENGTH_TIMESTAMP = 33;

	/**
	 * This returns the Unix timestamp from a TimeValue.
	 * This is similar to PHP's mk_time() (or strtotime()), but with no range limitations.
	 * Data type is float because PHP's 32 bit integer would clip in the year 2038.
	 *
	 * @param TimeValue $timeValue
	 *
	 * @return float seconds since 1970-01-01T00:00:00Z
	 */
	public function getTimestamp( TimeValue $timeValue ) {
		return $this->getSecondsSinceUnixEpoch( $timeValue->getTime(), $timeValue->getTimezone() );
	}

	/**
	 * Returns the lowest possible Unix timestamp from a TimeValue considering its precision
	 * and its before value. Data type is float because PHP's 32 bit integer would clip in the
	 * year 2038.
	 *
	 * @param TimeValue $timeValue
	 *
	 * @return float seconds since 1970-01-01T00:00:00Z
	 */
	public function getLowerTimestamp( TimeValue $timeValue ) {
		$precision = $timeValue->getPrecision();
		$timestamp = $timeValue->getTime();
		if (strcmp(substr($timestamp, 0, 1), '-') === 0 && $precision < TimeValue::PRECISION_YEAR) {
			$timestamp = $this->timestampAbsCeiling($timestamp, $precision);
		}
		else {
			$timestamp = $this->timestampAbsFloor($timestamp, $precision);
		}
		$unixTimestamp = $this->getSecondsSinceUnixEpoch($timestamp, $timeValue->getTimezone());
		$unixTimestamp -= $timeValue->getBefore() * $this->getSecondsForPrecision($precision);
		return $unixTimestamp;
	}

	/**
	 * Returns the highest possible Unix timestamp from a TimeValue considering its precision
	 * and its after value. Data type is float because PHP's 32 bit integer would clip in the
	 * year 2038.
	 *
	 * @param TimeValue $timeValue
	 *
	 * @return float seconds since 1970-01-01T00:00:00Z
	 */
	public function getHigherTimestamp( TimeValue $timeValue ) {
		$precision = $timeValue->getPrecision();
		$timestamp = $timeValue->getTime();
		if (strcmp(substr($timestamp, 0, 1), '-') === 0 && $precision < TimeValue::PRECISION_YEAR) {
			$timestamp = $this->timestampAbsFloor($timestamp, $precision);
		}
		else {
			$timestamp = $this->timestampAbsCeiling($timestamp, $precision);
		}
		$unixTimestamp = $this->getSecondsSinceUnixEpoch($timestamp, $timeValue->getTimezone());
		$unixTimestamp += $timeValue->getAfter() * $this->getSecondsForPrecision($precision);
		return $unixTimestamp;
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
				return 1.0;
			case TimeValue::PRECISION_MINUTE:
				return 60.0;
			case TimeValue::PRECISION_HOUR:
				return 3600.0;
			case TimeValue::PRECISION_DAY:
				return 86400.0;
			case TimeValue::PRECISION_MONTH:
				return self::SECONDS_PER_GREGORIAN_YEAR / 12;
		}

		throw new InvalidArgumentException( "Unable to get seconds for precision $precision." );
	}

	/**
	 * @param $timestamp
	 * @param $precision
	 * @return string
	 */
	private function timestampAbsFloor($timestamp, $precision) {
		// The year is padded with zeros to have 16 digits
		$timestamp = substr_replace($timestamp,
			str_repeat('0', self::MAX_LENGTH_TIMESTAMP - strlen($timestamp)), 1, 0);
		$numCharsToModify = $this->charsAffectedByPrecision($precision);
		$timestamp = substr($timestamp, 0, -$numCharsToModify) .
			substr(self::TIMESTAMP_ZERO, -$numCharsToModify);
		return $timestamp;
	}

	/**
	 * @param $timestamp
	 * @param $precision
	 * @return string
	 */
	private function timestampAbsCeiling($timestamp, $precision) {
		// The year is padded with zeros to have 16 digits
		$timestamp = substr_replace($timestamp,
			str_repeat('0', self::MAX_LENGTH_TIMESTAMP - strlen($timestamp)), 1, 0);
		$numCharsToModify = $this->charsAffectedByPrecision($precision);
		// WARNING: Day 31 will be applied to all months
		$timestamp = substr($timestamp, 0, -$numCharsToModify) .
			substr(self::HIGHEST_TIMESTAMP, -$numCharsToModify);
		return $timestamp;
	}

	/**
	 * @param $precision
	 * @return int
	 */
	private function charsAffectedByPrecision($precision) {
		$numCharsAffected = 1;
		switch ($precision) {
			case TimeValue::PRECISION_MINUTE:
				$numCharsAffected = 3;
				break;
			case TimeValue::PRECISION_HOUR:
				$numCharsAffected = 6;
				break;
			case TimeValue::PRECISION_DAY:
				$numCharsAffected = 9;
				break;
			case TimeValue::PRECISION_MONTH:
				$numCharsAffected = 12;
				break;
			case TimeValue::PRECISION_YEAR:
				$numCharsAffected = 15;
				break;
			case TimeValue::PRECISION_YEAR10:
				$numCharsAffected = 17;
				break;
			case TimeValue::PRECISION_YEAR100:
				$numCharsAffected = 18;
				break;
			case TimeValue::PRECISION_YEAR1K:
				$numCharsAffected = 19;
				break;
			case TimeValue::PRECISION_YEAR10K:
				$numCharsAffected = 20;
				break;
			case TimeValue::PRECISION_YEAR100K:
				$numCharsAffected = 21;
				break;
			case TimeValue::PRECISION_YEAR1M:
				$numCharsAffected = 22;
				break;
			case TimeValue::PRECISION_YEAR10M:
				$numCharsAffected = 23;
				break;
			case TimeValue::PRECISION_YEAR100M:
				$numCharsAffected = 24;
				break;
			case TimeValue::PRECISION_YEAR1G:
				$numCharsAffected = 25;
				break;
		}
		return $numCharsAffected;
	}

}
