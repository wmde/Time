<?php

namespace DataValues\Tests;

use DataValues\TimeValue;
use DataValues\TimeValueCalculator;

/**
 * @covers DataValues\TimeValueCalculator
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @license GPL-2.0+
 * @author Thiemo Kreuz
 */
class TimeValueCalculatorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var TimeValueCalculator
	 */
	private $calculator;

	protected function setUp() {
		$this->calculator = new TimeValueCalculator();
	}

	/**
	 * @param string $time an ISO 8601 date and time
	 * @param int $timezone offset from UTC in minutes
	 *
	 * @return TimeValue
	 */
	private function getTimeValueMock( $time, $timezone = 0 ) {
		$timeValue = $this->getMockBuilder( TimeValue::class )
			->disableOriginalConstructor()
			->getMock();

		$timeValue->expects( $this->any() )
			->method( 'getTime' )
			->will( $this->returnValue( $time ) );
		$timeValue->expects( $this->any() )
			->method( 'getTimezone' )
			->will( $this->returnValue( $timezone ) );
		$timeValue->expects( $this->any() )
			->method( 'getPrecision' )
			->will( $this->returnValue( TimeValue::PRECISION_DAY ) );
		$timeValue->expects( $this->any() )
			->method( 'getCalendarModel' )
			->will( $this->returnValue( 'Stardate' ) );

		return $timeValue;
	}

	public function timestampProvider() {
		return array(
			// Make sure it's identical to the PHP/Unix time stamps in current years
			array( '+2004-02-29T00:00:00Z', strtotime( '2004-02-29T00:00:00+00:00' ) ),
			array( '+2038-00-00T00:00:00Z', strtotime( '2038-01-01T00:00:00+00:00' ) ),

			// Time zones
			array( '+2000-01-01T12:59:59', strtotime( '2000-01-01T12:59:59-02:00' ), -120 ),
			array( '+2000-01-01T12:59:59', strtotime( '2000-01-01T12:59:59+04:45' ), 285 ),

			array( '+0401-00-00T00:00:00Z', -49512816000 ),
			array( '+1902-00-00T00:00:00Z', -2145916800 ),
			array( '+1939-00-00T00:00:00Z', -978307200 ),
			array( '+1969-12-31T23:59:00Z', -60 ),
			array( '+1969-12-31T23:59:59Z', -1 ),
			array( '+1970-01-01T00:00:00Z', 0 ),
			array( '+1970-01-01T00:00:01Z', 1 ),
			array( '+1970-01-01T00:01:00Z', 60 ),
			array( '+1972-02-29T00:00:00Z', 68169600 ),
			array( '+1996-02-29T00:00:00Z', 825552000 ),
			array( '+1999-12-31T23:59:59Z', 946684799 ),
			array( '+2000-01-01T00:00:00Z', 946684800 ),
			array( '+2000-02-01T00:00:00Z', 949363200 ),
			array( '+2000-02-29T00:00:00Z', 951782400 ),
			array( '+2001-00-00T00:00:00Z', 978307200 ),
			array( '+2001-01-01T00:00:00Z', 978307200 ),
			array( '+2014-04-30T12:35:55Z', 1398861355 ),
			array( '+2401-00-00T00:00:00Z', 13601088000 ),

			// Make sure there is only 1 second between these two
			array( '-0001-12-31T23:59:59Z', -62135596801 ),
			array( '+0001-00-00T00:00:00Z', -62135596800 ),

			// No special calculation for leap seconds, just make sure they pass
			array( '+1970-10-11T12:13:61Z', 24495241 ),
			array( '+1970-10-11T12:14:01Z', 24495241 ),

			// Year 0 does not exist, but we do not complain, assume -1
			array( '-0000-12-31T23:59:59Z', -62135596801 ),
			array( '+0000-00-00T00:00:00Z', floor( ( -1 - 1969 ) * 365.2425 ) * 86400 ),

			// Since there is no year 0, negative leap years are -1, -5 and so on
			array( '-8001-00-00T00:00:00Z', floor( ( -8001 - 1969 ) * 365.2425 ) * 86400 ),
			array( '-0005-00-00T00:00:00Z', floor( ( -5 - 1969 ) * 365.2425 ) * 86400 ),
			array( '+0004-00-00T00:00:00Z', floor( ( 4 - 1970 ) * 365.2425 ) * 86400 ),
			array( '+8000-00-00T00:00:00Z', floor( ( 8000 - 1970 ) * 365.2425 ) * 86400 ),

			// PHP_INT_MIN is -2147483648
			array( '-2147484001-00-00T00:00:00Z', floor( ( -2147484001 - 1969 ) * 365.2425 ) * 86400 ),
			// PHP_INT_MAX is +2147483647
			array( '+2147484000-00-00T00:00:00Z', floor( ( 2147484000 - 1970 ) * 365.2425 ) * 86400 ),
		);
	}

	/**
	 * @dataProvider timestampProvider
	 */
	public function testGetTimestamp( $time, $expectedTimestamp = 0.0, $timezone = 0 ) {
		$timeValue = $this->getTimeValueMock( $time, $timezone );
		$timestamp = $this->calculator->getTimestamp( $timeValue );

		$this->assertEquals( $expectedTimestamp, $timestamp );
	}

	public function yearProvider() {
		return array(
			// Every 4 years
			array( 1895, 459 ),
			array( 1896, 460, true ),
			array( 1897, 460 ),

			// Not every 100 years but every 400 years
			array( 1900, 460 ),
			array( 2000, 485, true ),
			array( 2100, 509 ),

			// Extremes
			array( 1, 0 ),
			array( 9999, 2424 ),
			array( 2147483647, 520764784 ),

			// There is no year zero, assume -1
			array( -1, 0, true ),
			array( 0, 0, true ),

			// Off by 1 for negative years because zero is skipped
			array( -6, -2 ),
			array( -5, -1, true ),
			array( -4, -1 ),
			array( -3, -1 ),
			array( -2, -1 ),
			array( -1, 0, true ),
			array( 1, 0 ),
			array( 2, 0 ),
			array( 3, 0 ),
			array( 4, 1, true ),
			array( 5, 1 ),

			// Because we can
			array( -6.9, -2 ),
			array( -6.1, -2 ),
			array( -5.501, -1, true ),
			array( -5.499, -1, true ),
			array( -4.6, -1 ),
			array( -4.4, -1 ),
			array( 1995.01, 483 ),
			array( 1995.09, 483 ),
			array( 1996.001, 484, true ),
			array( 1996.009, 484, true ),
			array( 1997.1, 484 ),
			array( 1997.9, 484 ),
		);
	}

	/**
	 * @dataProvider yearProvider
	 */
	public function testIsLeapYear( $year, $numberOfLeapYears, $expected = false ) {
		$isLeapYear = $this->calculator->isLeapYear( $year );

		$this->assertEquals( $expected, $isLeapYear );
	}

	/**
	 * @dataProvider yearProvider
	 */
	public function testGetNumberOfLeapYears( $year, $expected, $isLeapYear = false ) {
		$numberOfLeapYears = $this->calculator->getNumberOfLeapYears( $year );

		$this->assertEquals( $expected, $numberOfLeapYears );
	}

	public function precisionProvider() {
		$secondsPerDay = 60 * 60 * 24;
		$daysPerGregorianYear = 365 + 1 / 4 - 1 / 100 + 1 / 400;

		return array(
			array( TimeValue::PRECISION_SECOND, 1 ),
			array( TimeValue::PRECISION_MINUTE, 60 ),
			array( TimeValue::PRECISION_HOUR, 60 * 60 ),
			array( TimeValue::PRECISION_DAY, $secondsPerDay ),
			array( TimeValue::PRECISION_MONTH, $secondsPerDay * $daysPerGregorianYear / 12 ),
			array( TimeValue::PRECISION_YEAR, $secondsPerDay * $daysPerGregorianYear ),
			array( TimeValue::PRECISION_YEAR10, $secondsPerDay * $daysPerGregorianYear * 1e1 ),
			array( TimeValue::PRECISION_YEAR1G, $secondsPerDay * $daysPerGregorianYear * 1e9 ),
		);
	}

	/**
	 * @dataProvider precisionProvider
	 */
	public function testGetSecondsForPrecision( $precision, $expected ) {
		$seconds = $this->calculator->getSecondsForPrecision( $precision );

		$this->assertEquals( $expected, $seconds );
	}

	/**
	 * @return array
	 */
	private function timestampWithoutSignProvider() {
		return [
			'16-11-11T06:08:04Z',
			'245-04-30T00:00:00Z',
			'1054-02-11T14:00:02Z',
			'2012-02-29T23:59:59Z',
			#'2012-03-01T00:00:00Z',
			'2013-12-31T23:59:59Z',
			'2014-01-01T00:00:00Z',
			'54844518-04-25T02:00:00Z',
			'9990994999299999-07-09T14:20:00Z',
		];
	}

	/**
	 * @return array of precisions from the most to the least precise
	 */
	private function simplePrecisionProvider() {
		return [
			TimeValue::PRECISION_SECOND,
			TimeValue::PRECISION_MINUTE,
			TimeValue::PRECISION_HOUR,
			TimeValue::PRECISION_DAY,
			TimeValue::PRECISION_MONTH,
			TimeValue::PRECISION_YEAR,
			TimeValue::PRECISION_YEAR10,
			TimeValue::PRECISION_YEAR100,
			TimeValue::PRECISION_YEAR1K,
			TimeValue::PRECISION_YEAR10K,
			TimeValue::PRECISION_YEAR100K,
			TimeValue::PRECISION_YEAR1M,
			TimeValue::PRECISION_YEAR10M,
			TimeValue::PRECISION_YEAR100M,
			TimeValue::PRECISION_YEAR1G
		];
	}

	/**
	 * Check that:
	 *   - timestampWithoutSignProvider() values are ordered from earliest to latest according
	 *       to getLowerTimestamp(),
	 *   - getTimestamp() values are always greater or equal than getLowerTimestamp() ones,
	 *   - higher precisions of the same timestamp always correspond to greater or equal
	 *       getLowerTimestamp() values than lower precisions,
	 *   - higher before values for the same timestamp always correspond to lower or equal
	 *       getLowerTimestamp() values than lower before values,
	 *   - a few TimeValue values are equal to their expected getLowerTimestamp() ones.
	 */
	public function testGetLowerTimestamp() {
		$timestamps = $this->timestampWithoutSignProvider();
		$precisions = $this->simplePrecisionProvider();
		$timeValueCalculator = new TimeValueCalculator();
		$timeValue = new TimeValue(
			'+2013-03-14T12:51:02Z',
			0, 0, 0,
			TimeValue::PRECISION_MONTH,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixLowerTimestamp = $timeValueCalculator->getLowerTimestamp( $timeValue );
		$this->assertEquals( 1362096000, $unixLowerTimestamp );
		$timeValue = new TimeValue(
			'+0002-07-17T02:41:22Z',
			0, 0, 0,
			TimeValue::PRECISION_DAY,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixLowerTimestamp = $timeValueCalculator->getLowerTimestamp( $timeValue );
		$this->assertEquals( -62087040000, $unixLowerTimestamp );
		foreach ( [ '+', '-' ] as $sign ) {
			$oldUnixLowerTimestampMonth = null;
			foreach ( $timestamps as $timestamp ) {
				$timestamp = $sign . $timestamp;
				$timeValue = new TimeValue(
					$timestamp,
					0, 0, 0,
					TimeValue::PRECISION_MONTH,
					TimeValue::CALENDAR_GREGORIAN
				);
				$unixLowerTimestamp = $timeValueCalculator->getLowerTimestamp( $timeValue );
				if ( !empty( $oldUnixLowerTimestampMonth ) ) {
					if ( $sign === '+' ) {
						$this->assertGreaterThan( $oldUnixLowerTimestampMonth, $unixLowerTimestamp );
					} else {
						$this->assertGreaterThan( $unixLowerTimestamp, $oldUnixLowerTimestampMonth );
					}
				}
				$oldUnixLowerTimestampMonth = $unixLowerTimestamp;
				$oldUnixLowerTimestamp = null;
				foreach ( $precisions as $precision ) {
					$timeValue = new TimeValue(
						$timestamp,
						0, 0, 0,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixTimestampAsIs = $timeValueCalculator->getTimestamp( $timeValue );
					$unixLowerTimestamp = $timeValueCalculator->getLowerTimestamp( $timeValue );
					$this->assertGreaterThanOrEqual( $unixLowerTimestamp, $unixTimestampAsIs );
					$timeValueBefore1 = new TimeValue(
						$timestamp,
						0, 1, 1,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixLowerTimestampBefore1 = $timeValueCalculator->getLowerTimestamp( $timeValueBefore1 );
					if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
						$this->assertGreaterThan( $unixLowerTimestampBefore1, $unixLowerTimestamp );
					} else {
						$this->assertGreaterThanOrEqual( $unixLowerTimestampBefore1, $unixLowerTimestamp );
					}
					$timeValueBefore2 = new TimeValue(
						$timestamp,
						0, 2, 2,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixLowerTimestampBefore2 = $timeValueCalculator->getLowerTimestamp( $timeValueBefore2 );
					if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
						$this->assertGreaterThan( $unixLowerTimestampBefore2, $unixLowerTimestampBefore1 );
					} else {
						$this->assertGreaterThanOrEqual( $unixLowerTimestampBefore2, $unixLowerTimestampBefore1 );
					}
					// As $precisions are ordered from most to least precise, old lower timestamps must be
					// greater than or equal to current lower timestamps
					if ( !empty( $oldUnixLowerTimestamp ) ) {
						$this->assertGreaterThanOrEqual( $unixLowerTimestamp, $oldUnixLowerTimestamp );
					}
					$oldUnixLowerTimestamp = $unixLowerTimestamp;
				}
			}
		}
	}

	/**
	 * Check that:
	 *   - timestampWithoutSignProvider() values are ordered from earliest to latest according
	 *       to getHigherTimestamp(),
	 *   - getHigherTimestamp() values are always greater or equal than getTimestamp() ones,
	 *   - lower precisions of the same timestamp always correspond to greater or equal
	 *       getHigherTimestamp() values than higher precisions,
	 *   - higher after values for the same timestamp always correspond to greater or equal
	 *       getHigherTimestamp() values than lower after values,
	 *   - a few TimeValue values are equal to their expected getHigherTimestamp() ones.
	 */
	public function testGetHigherTimestamp() {
		$timestamps = $this->timestampWithoutSignProvider();
		$precisions = $this->simplePrecisionProvider();
		$timeValueCalculator = new TimeValueCalculator();
		$timeValue = new TimeValue(
			'+2013-03-14T12:51:02Z',
			0, 0, 0,
			TimeValue::PRECISION_MONTH,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixHigherTimestamp = $timeValueCalculator->getHigherTimestamp( $timeValue );
		$this->assertEquals( 1364774399, $unixHigherTimestamp );
		$timeValue = new TimeValue(
			'+1002-07-17T02:41:22Z',
			0, 0, 0,
			TimeValue::PRECISION_DAY,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixHigherTimestamp = $timeValueCalculator->getHigherTimestamp( $timeValue );
		$this->assertEquals( -30530044801, $unixHigherTimestamp );
		foreach ( [ '+', '-' ] as $sign ) {
			$oldUnixHigherTimestampMonth = null;
			foreach ( $timestamps as $timestamp ) {
				$timestamp = $sign . $timestamp;
				$timeValue = new TimeValue(
					$timestamp,
					0, 0, 0,
					TimeValue::PRECISION_MONTH,
					TimeValue::CALENDAR_GREGORIAN
				);
				$unixHigherTimestamp = $timeValueCalculator->getHigherTimestamp( $timeValue );
				if ( !empty( $oldUnixHigherTimestampMonth ) ) {
					if ( $sign === '+' ) {
						$this->assertGreaterThan( $oldUnixHigherTimestampMonth, $unixHigherTimestamp );
					} else {
						$this->assertGreaterThan( $unixHigherTimestamp, $oldUnixHigherTimestampMonth );
					}
				}
				$oldUnixHigherTimestampMonth = $unixHigherTimestamp;
				$oldUnixHigherTimestamp = null;
				foreach ( $precisions as $precision ) {
					$timeValue = new TimeValue(
						$timestamp,
						0, 0, 0,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixTimestampAsIs = $timeValueCalculator->getTimestamp( $timeValue );
					$unixHigherTimestamp = $timeValueCalculator->getHigherTimestamp( $timeValue );
					$this->assertLessThanOrEqual( $unixHigherTimestamp, $unixTimestampAsIs );
					$timeValueAfter1 = new TimeValue(
						$timestamp,
						0, 1, 1,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixHigherTimestampAfter1 = $timeValueCalculator->getHigherTimestamp( $timeValueAfter1 );
					if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
						$this->assertLessThan( $unixHigherTimestampAfter1, $unixHigherTimestamp );
					} else {
						$this->assertLessThanOrEqual( $unixHigherTimestampAfter1, $unixHigherTimestamp );
					}
					$timeValueAfter2 = new TimeValue(
						$timestamp,
						0, 2, 2,
						$precision,
						TimeValue::CALENDAR_GREGORIAN
					);
					$unixHigherTimestampAfter2 = $timeValueCalculator->getHigherTimestamp( $timeValueAfter2 );
					if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
						$this->assertLessThan( $unixHigherTimestampAfter2, $unixHigherTimestampAfter1 );
					} else {
						$this->assertLessThanOrEqual( $unixHigherTimestampAfter2, $unixHigherTimestampAfter1 );
					}
					// As $precisions are ordered from most to least precise, old higher timestamps must be
					// less than or equal to current higher timestamps
					if ( !empty( $oldUnixHigherTimestamp ) ) {
						echo $oldUnixHigherTimestamp . ' < ' . $unixHigherTimestamp . "\n";
						$this->assertLessThanOrEqual( $unixHigherTimestamp, $oldUnixHigherTimestamp );
					}
					$oldUnixHigherTimestamp = $unixHigherTimestamp;
				}
			}
		}
	}

	/**
	 * @param string $timestamp
	 * @param int $precision
	 */
	private function auxTestGetHigherTimestamp( $timestamp, $precision ) {
		$timeValueCalculator = new TimeValueCalculator();
		$timeValue = new TimeValue(
			$timestamp,
			0, 0, 0,
			$precision,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixTimestampAsIs = $timeValueCalculator->getTimestamp( $timeValue );
		$unixHigherTimestamp = $timeValueCalculator->getHigherTimestamp( $timeValue );
		$this->assertLessThanOrEqual( $unixHigherTimestamp, $unixTimestampAsIs );

		$timeValueAfter1 = new TimeValue(
			$timestamp,
			0, 1, 1,
			$precision,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixHigherTimestampAfter1 = $timeValueCalculator->getHigherTimestamp( $timeValueAfter1 );
		if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
			$this->assertLessThan( $unixHigherTimestampAfter1, $unixHigherTimestamp );
		} else {
			$this->assertLessThanOrEqual( $unixHigherTimestampAfter1, $unixHigherTimestamp );
		}
		$timeValueAfter2 = new TimeValue(
			$timestamp,
			0, 2, 2,
			$precision,
			TimeValue::CALENDAR_GREGORIAN
		);
		$unixHigherTimestampAfter2 = $timeValueCalculator->getHigherTimestamp( $timeValueAfter2 );
		if ( $unixTimestampAsIs > -10000000000000 && $unixTimestampAsIs < 10000000000000 ) {
			$this->assertLessThan( $unixHigherTimestampAfter2, $unixHigherTimestampAfter1 );
		} else {
			$this->assertLessThanOrEqual( $unixHigherTimestampAfter2, $unixHigherTimestampAfter1 );
		}
	}

}
