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
	 * Maximum length for a timestamp.
	 */
	private $MAX_LENGTH_TIMESTAMP = 33;

	/**
	 * Lowest positive timestamp.
	 */
	private $TIMESTAMP_ZERO = '+0000000000000000-01-01T00:00:00Z';

	/**
	 * Highest positive timestamp strictly earlier than the lowest positive timestamp with
	 * a length of $MAX_LENGTH_TIMESTAMP + 1.
	 */
	private $HIGHEST_TIMESTAMP = '+9999999999999999-12-31T23:59:59Z';

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
		if ( $timestamp[0] === '+' || $precision >= TimeValue::PRECISION_YEAR ) {
			$timestamp = $this->timestampAbsFloor( $timestamp, $precision );
		} else {
			// digits corresponding to [0, PRECISION_YEAR] must be maximized
			// digits corresponding to [PRECISION_MONTH, PRECISION_SECOND] must be minimized
			$subTimestampLeft = $this->timestampAbsCeiling( $timestamp, $precision );
			$subTimestampLeft = substr(
				$subTimestampLeft,
				0,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR )
			);
			$subTimestampRight = $this->timestampAbsFloor( $timestamp, $precision );
			$subTimestampRight = substr(
				$subTimestampRight,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR )
			);
			$timestamp = $subTimestampLeft . $subTimestampRight;
		}
		$unixTimestamp = $this->getSecondsSinceUnixEpoch( $timestamp, $timeValue->getTimezone() );
		$unixTimestamp -= $timeValue->getBefore() * $this->getSecondsForPrecision( $precision );
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
		if ( $timestamp[0] === '+' || $precision >= TimeValue::PRECISION_YEAR ) {
			$timestamp = $this->timestampAbsCeiling( $timestamp, $precision );
		} else {
			// digits corresponding to [0, PRECISION_YEAR] must be minimized
			// digits corresponding to [PRECISION_MONTH, PRECISION_SECOND] must be maximized
			$subTimestampLeft = $this->timestampAbsFloor( $timestamp, $precision );
			$subTimestampLeft = substr(
				$subTimestampLeft,
				0,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR )
			);
			$subTimestampRight = $this->timestampAbsCeiling( $timestamp, $precision );
			$subTimestampRight = substr(
				$subTimestampRight,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR )
			);
			$timestamp = $subTimestampLeft . $subTimestampRight;
		}
		$unixTimestamp = $this->getSecondsSinceUnixEpoch( $timestamp, $timeValue->getTimezone() );
		$unixTimestamp += $timeValue->getAfter() * $this->getSecondsForPrecision( $precision );
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
	 * @param string $calendar only TimeValue::CALENDAR_GREGORIAN is supported
	 * @param float $month
	 * @param float $year
	 *
	 * @throws InvalidArgumentException if $calendar is not supported
	 * @return int
	 */
	public function getDaysInMonth( $calendar, $month, $year ) {
		if ( $calendar !== TimeValue::CALENDAR_GREGORIAN ) {
			throw new InvalidArgumentException( "Only Gregorian calendar is supported." );
		}
		return $month == 2 ? ( $this->isLeapYear( $year ) ? 29 : 28 ) : ( ( $month - 1 ) % 7 % 2 ? 30 : 31 );
	}

	/**
	 * @param float $year
	 *
	 * @return bool if the year is a leap year in the Gregorian calendar
	 */
	public function isLeapYear( $year ) {
		$year = $year < 0 ? ceil( $year ) + 1 : floor( $year );
		$isMultipleOf4 = fmod( $year, 4 ) === 0.0;
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

	/**
	 * @param $timestamp
	 * @param $precision
	 *
	 * @return string
	 */
	private function timestampAbsFloor( $timestamp, $precision ) {
		// The year is padded with zeros to have 16 digits
		$timestamp = substr_replace(
			$timestamp,
			str_repeat( '0', $this->MAX_LENGTH_TIMESTAMP - strlen( $timestamp ) ),
			1,
			0
		);
		$numCharsToModify = $this->charsAffectedByPrecision( $precision );
		$timestamp = substr( $timestamp, 0, -$numCharsToModify ) .
			substr( $this->TIMESTAMP_ZERO, -$numCharsToModify );
		return $timestamp;
	}

	/**
	 * @param $timestamp
	 * @param $precision
	 *
	 * @return string
	 */
	private function timestampAbsCeiling( $timestamp, $precision ) {
		// The year is padded with zeros to have 16 digits
		$timestamp = substr_replace(
			$timestamp,
			str_repeat( '0', $this->MAX_LENGTH_TIMESTAMP - strlen( $timestamp ) ),
			1,
			0
		);
		$numCharsToModify = $this->charsAffectedByPrecision( $precision );
		$timestampCeiling = substr( $timestamp, 0, -$numCharsToModify ) .
			substr( $this->HIGHEST_TIMESTAMP, -$numCharsToModify );
		if ( $precision === TimeValue::PRECISION_MONTH ) {
			// The highest day (28-31) depends on the month and the year
			$month = (float)substr(
				$timestamp,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR ),
				2
			);
			$year = (float)substr(
				$timestamp,
				1,
				-$this->charsAffectedByPrecision( TimeValue::PRECISION_YEAR ) - 1
			);
			$daysInMonth = $this->getDaysInMonth( TimeValue::CALENDAR_GREGORIAN, $month, $year );
			$timestampCeiling = substr( $timestamp, 0, -$numCharsToModify ) .
				$daysInMonth .
				substr( $this->HIGHEST_TIMESTAMP, -$numCharsToModify + 2 );
		}
		return $timestampCeiling;
	}

	/**
	 * @param int $precision
	 *
	 * @return int lowest number of characters in an ISO 8601 timestamp string
	 * that are irrelevant given $precision
	 */
	private function charsAffectedByPrecision( $precision ) {
		$numCharsAffected = 1;
		switch ( $precision ) {
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
