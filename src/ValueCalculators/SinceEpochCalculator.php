<?php

namespace ValueCalculators;

use DataValues\TimeValue;
use RuntimeException;

/**
 * Class to calculate the number of seconds since the PHP Epoch ( 1-1-1970 )
 * for a TimeValue.
 *
 * NOTE: This class does not take into account the extra seconds in Leap Years
 *       as it always presumes the are self::$secondsPerYear seconds in a year
 *
 * @author Adam Shorland
 * @since 0.6
 */
class SinceEpochCalculator {

	/**
	 * @var int seconds in a year ( 365 * 24 * 60 * 60 )
	 */
	private static $secondsPerYear = 31536000;

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

		$timeWithoutYear = '1970-' . $matches[2];
		$yearsToBeAdded = ( (double)$matches[1] - 1970 );

		$sinceEpochForYears = $yearsToBeAdded * self::$secondsPerYear;
		$sinceEpochWithoutYears = strtotime( $timeWithoutYear );
		if( $sinceEpochWithoutYears === false ){
			throw new RuntimeException( 'Failed to get sinceEpochWithoutYears from time value: ' . $timeString );
		}

		return $sinceEpochWithoutYears + $sinceEpochForYears;
	}

} 