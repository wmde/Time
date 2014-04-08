<?php

namespace ValueCalculators;

use DataValues\TimeValue;
use RuntimeException;

/**
 * Class to calculate the number of seconds since the PHP Epoch ( 1-1-1970 )
 * for a TimeValue.
 *
 * @author Adam Shorland
 * @since 0.6
 */
class SinceEpochCalculator {

	/**
	 * @var double seconds in a year ( 365 * 24 * 60 * 60 )
	 */
	private static $secondsPerYear = 31536000;

	/**
	 * @var double seconds in a day ( 24 * 60 * 60 )
	 */
	private static $secondsPerDay = 86400;

	/**
	 * @param TimeValue $time
	 *
	 * @return int
	 */
	public function calculate( TimeValue $time ) {
		return $this->getSinceEpoch( $time->getTime() );
	}

	/**
	 * @param string $timeString +0000000000002014-01-01T00:00:00Z
	 *
	 * @throws RuntimeException
	 * @return double
	 */
	private function getSinceEpoch( $timeString ) {
		preg_match( '/^([-+]\d{1,16})-(.+Z)$/', $timeString, $matches );

		$year = (double)$matches[1];
		$yearIsLeapYear = $this->isLeapYear( $year );

		if( $yearIsLeapYear ) {
			$mockYear = 1972;
		} else {
			$mockYear = 1970;
		}

		$sinceEpochForYears = ( $year - $mockYear ) * self::$secondsPerYear;
		$sinceEpochWithoutYears = strtotime( $mockYear . '-' . $matches[2] );
		if( $sinceEpochWithoutYears === false ){
			throw new RuntimeException( 'Failed to get sinceEpochWithoutYears from time value: ' . $timeString );
		}

		$secondsDueToLeapYears = $this->getNumberOfLeapYearsSince1970( $year ) * self::$secondsPerDay;

		return $sinceEpochWithoutYears + $sinceEpochForYears + $secondsDueToLeapYears;
	}

	/**
	 * @param double $year
	 *
	 * @return double
	 */
	private function getNumberOfLeapYearsSince1970( $year ) {
		$years = ( $year - 1970 + 2 ) / 4;

		$currentIsLeap = false;
		if ( abs( $years - round( $years ) ) < 0.1 ) {
			$currentIsLeap = true;
		}

		return floor ( $years ) - (int)$currentIsLeap;
	}

	/**
	 * @param double $year
	 *
	 * @return bool
	 */
	private function isLeapYear( $year ) {
		if( $year %4 != 0 ) {
			return false;
		} elseif ( $year %100 != 0 ) {
			return true;
		} elseif ( $year %400 == 0 ) {
			return true;
		} else {
			return false;
		}
	}

} 