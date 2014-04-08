<?php

namespace ValueCalculators\Test;

use DataValues\TimeValue;
use ValueCalculators\SinceEpochCalculator;
use ValueFormatters\TimeFormatter;

/**
 * @covers ValueCalculators\SinceEpochCalculator
 *
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class EpochCalculatorTest extends \PHPUnit_Framework_TestCase {

	public function validProvider() {
		$tests = array(

			//Future dates
			'0' => array(
				'The Epoch itself',
				'+00000001970-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'86400' => array(
				'1 day after the Epoch',
				'+00000001970-01-02T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'2678400' => array(
				'1 month (31 days) after the Epoch',
				'+00000001970-02-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'2764800' => array(
				'1 month (31 days) and 1 extra day after the Epoch',
				'+00000001970-02-02T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'5097600' => array(
				'2 months (31+28 days) after the Epoch',
				'+00000001970-03-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'31536000' => array(
				'1 year after the Epoch',
				'+00000001971-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'63072000' => array(
				'2 years after the Epoch',
				'+00000001972-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'94694400' => array(
				'3 years after the Epoch (1 leap year)',
				'+00000001973-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'157766400' => array(
				'5 years after the Epoch (1 leap year)',
				'+00000001975-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'1577836800' => array(
				'50 years after the Epoch (12 leap years)',
				'+00000002020-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
//			'15778713600' => array(
//				'500 years after the Epoch (124 leap years)',
//				'+00000002470-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'157783680000' => array(
//				'5,000 years after the Epoch (1250 leap years)',
//				'+00000006970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'1577836800000' => array(
//				'50,000 years after the Epoch (12500 leap years)',
//				'+00000051970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'15778368000000' => array(
//				'500,000 years after the Epoch (125,000 leap years)',
//				'+00000501970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'157783680000000' => array(
//				'5,000,000 years after the Epoch (1,250,000 leap years)',
//				'+00005001970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'1577836800000000' => array(
//				'50,000,000 years after the Epoch (12,500,000 leap years)',
//				'+00050001970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'15778368000000000' => array(
//				'500,000,000 years after the Epoch (125,000,000 leap years)',
//				'+00500001970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'157783680000000000' => array(
//				'5,000,000,000 years after the Epoch (1,250,000,000 leap years)',
//				'+05000001970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'1577836800000000000' => array(
//				'50,000,000,000 years after the Epoch (12,500,000,000 leap years)',
//				'+50000001970-01-01T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//			'15778368000000000000' => array(
//				'50,000,000,000 years and 2 days after the Epoch (125,000,000,000 leap years)',
//				'+50000001970-01-03T00:00:00Z',
//				TimeValue::PRECISION_DAY
//			),
//
			//Past dates
			'-86400' => array(
				'1 day before the Epoch',
				'+00000001969-12-31T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			//There is not a 31st of November, but in PHP there is!
			'-2678400' => array(
				'1 month before the Epoch (31 Nov)',
				'+00000001969-11-31T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'-2764800' => array(
				'1 month before the Epoch (30 Nov)',
				'+00000001969-11-30T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			'-62136892800' => array(
				'1969 years before the Epoch',
				'+00000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY
			),
			//TODO tests for TimeValues that are BCE
		);

		$argLists = array();
		foreach ( $tests as $expected => $args ) {
			$argLists[ $args[0] ] = array(
				(double)$expected,
				new TimeValue( $args[1], 0, 0, 0, $args[2], TimeFormatter::CALENDAR_GREGORIAN )
			);
		}

		return $argLists;
	}

	/**
	 * @dataProvider validProvider
	 */
	public function testMe( $expected, TimeValue $timeValue ) {
		$calculator = new SinceEpochCalculator();
		$this->assertEquals( $expected, $calculator->calculate( $timeValue ) );
	}

}
