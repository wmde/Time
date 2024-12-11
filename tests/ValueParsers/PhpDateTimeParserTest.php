<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\IsoTimestampParser;
use ValueParsers\MonthNameUnlocalizer;
use ValueParsers\ParserOptions;
use ValueParsers\PhpDateTimeParser;
use ValueParsers\ValueParser;

/**
 * @covers ValueParsers\PhpDateTimeParser
 *
 * @group DataValue
 * @group DataValueExtensions
 * @group TimeParsers
 * @group ValueParsers
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class PhpDateTimeParserTest extends ValueParserTestCase {

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return PhpDateTimeParser
	 */
	protected function getInstance() {
		$options = new ParserOptions();

		return new PhpDateTimeParser(
			new MonthNameUnlocalizer( array() ),
			$this->getEraParser(),
			new IsoTimestampParser( null, $options )
		);
	}

	/**
	 * @return ValueParser
	 */
	private function getEraParser() {
		$mock = $this->createMock( ValueParser::class );

		$mock->expects( $this->any() )
			->method( 'parse' )
			->with( $this->isType( 'string' ) )
			->willReturnCallback(
				static function ( $value ) {
					$sign = '+';
					// Tiny parser that supports a single negative sign only
					if ( $value[0] === '-' ) {
						$sign = '-';
						$value = substr( $value, 1 );
					}
					return array( $sign, $value );
				}
			);

		return $mock;
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';
		$argList = array();

		$valid = array(
			// Whitespace
			"10/10/2010\n" =>
				array( '+0000000000002010-10-10T00:00:00Z' ),
			' 10.10.2010 ' =>
				array( '+0000000000002010-10-10T00:00:00Z' ),

			// Normal/easy dates
			'  10.  10.  2010  ' =>
				array( '+0000000000002010-10-10T00:00:00Z' ),
			'10,10,2010' =>
				array( '+0000000000002010-10-10T00:00:00Z' ),
			'10 10 2010' =>
				array( '+0000000000002010-10-10T00:00:00Z' ),
			'10/10/0010' =>
				array( '+0000000000000010-10-10T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'1 July 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'1. July 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'1 July, 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'1 Jul. 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'1 Jul 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'January 9 1920' =>
				array( '+0000000000001920-01-09T00:00:00Z' ),
			'Feb 11 1930' =>
				array( '+0000000000001930-02-11T00:00:00Z' ),
			'1st July 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'2nd July 2013' =>
				array( '+0000000000002013-07-02T00:00:00Z' ),
			'3rd July 2013' =>
				array( '+0000000000002013-07-03T00:00:00Z' ),
			'1th July 2013' =>
				array( '+0000000000002013-07-01T00:00:00Z' ),
			'2th July 2013' =>
				array( '+0000000000002013-07-02T00:00:00Z' ),
			'3th July 2013' =>
				array( '+0000000000002013-07-03T00:00:00Z' ),
			'4th July 2013' =>
				array( '+0000000000002013-07-04T00:00:00Z' ),

			// Year first dates
			'2009-01-09' =>
				array( '+0000000000002009-01-09T00:00:00Z' ),
			'55-01-09' =>
				array( '+0000000000000055-01-09T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'555-01-09' =>
				array( '+0000000000000555-01-09T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'33300-1-1' =>
				array( '+0000000000033300-01-01T00:00:00Z' ),
			'3330002-1-1' =>
				array( '+0000000003330002-01-01T00:00:00Z' ),

			// Less than 4 digit years
			'10/10/10' =>
				array( '+0000000000000010-10-10T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'9 Jan 09' =>
				array( '+0000000000000009-01-09T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'1/1/1' =>
				array( '+0000000000000001-01-01T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'1-1-1' =>
				array( '+0000000000000001-01-01T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'31-1-55' =>
				array( '+0000000000000055-01-31T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'10-10-100' =>
				array( '+0000000000000100-10-10T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'4th July 11' =>
				array( '+0000000000000011-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'4th July 111' =>
				array( '+0000000000000111-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'4th July 1' =>
				array( '+0000000000000001-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'12.Jun.10x' =>
				array( '+0000000000000010-06-12T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),

			// More than 4 digit years
			'4th July 10000' =>
				array( '+0000000000010000-07-04T00:00:00Z' ),
			'10/10/22000' =>
				array( '+0000000000022000-10-10T00:00:00Z' ),
			'1-1-33300' =>
				array( '+0000000000033300-01-01T00:00:00Z' ),
			'4th July 7214614279199781' =>
				array( '+7214614279199781-07-04T00:00:00Z' ),
			'-10100-02-29' =>
				array( '-0000000000010100-03-01T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),

			// Years with leading zeros
			'009-08-07' =>
				array( '+0000000000000009-08-07T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'000001-07-04' =>
				array( '+0000000000000001-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'0000001-07-04' =>
				array( '+0000000000000001-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'00000001-07-04' =>
				array( '+0000000000000001-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'000000001-07-04' =>
				array( '+0000000000000001-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'00000000000-07-04' =>
				array( '+0000000000000000-07-04T00:00:00Z', TimeValue::PRECISION_DAY, $julian ),
			'4th July 00000002015' =>
				array( '+0000000000002015-07-04T00:00:00Z' ),
			'00000002015-07-04' =>
				array( '+0000000000002015-07-04T00:00:00Z' ),
			'4th July 00000092015' =>
				array( '+0000000000092015-07-04T00:00:00Z' ),
			'00000092015-07-04' =>
				array( '+0000000000092015-07-04T00:00:00Z' ),

			// Hour, minute and second precision
			'4 July 2015 23:59' =>
				array( '+0000000000002015-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE ),
			'4 July 100 23:59' =>
				array( '+0000000000000100-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4 July 015 23:59' =>
				array( '+0000000000000015-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4 July 15 23:59' =>
				array( '+0000000000000015-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4.7.015 23:59' =>
				array( '+0000000000000015-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4.7.15 23:59' =>
				array( '+0000000000000015-07-04T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4/7/015 23:59' =>
				array( '+0000000000000015-04-07T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4/7/15 23:59' =>
				array( '+0000000000000015-04-07T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4th July 2015 12:00' =>
				array( '+0000000000002015-07-04T12:00:00Z', TimeValue::PRECISION_HOUR ),
			'2015-07-04 12:00' =>
				array( '+0000000000002015-07-04T12:00:00Z', TimeValue::PRECISION_HOUR ),
			'2015-07-04 12:30' =>
				array( '+0000000000002015-07-04T12:30:00Z', TimeValue::PRECISION_MINUTE ),
			'2015-07-04 12:30:29' =>
				array( '+0000000000002015-07-04T12:30:29Z', TimeValue::PRECISION_SECOND ),
			'15.07.04 23:59' =>
				array( '+0000000000000004-07-15T23:59:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'15.07.04 00:01' =>
				array( '+0000000000000004-07-15T00:01:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'15-07-01 12:37:00' =>
				array( '+0000000000000001-07-15T12:37:00Z', TimeValue::PRECISION_MINUTE, $julian ),
			'4th July 15 12:00' =>
				array( '+0000000000000015-07-04T12:00:00Z', TimeValue::PRECISION_HOUR, $julian ),
			'July 4th 15 12:00' =>
				array( '+0000000000000015-07-04T12:00:00Z', TimeValue::PRECISION_HOUR, $julian ),

			// Testing leap year stuff
			'10000-02-29' =>
				array( '+0000000000010000-02-29T00:00:00Z' ),
			'10100-02-29' =>
				array( '+0000000000010100-03-01T00:00:00Z' ),
			'10400-02-29' =>
				array( '+0000000000010400-02-29T00:00:00Z' ),

			'Jan1 1991' =>
				array( '+1991-01-01T00:00:00Z' ),
			'1991-1-1' =>
				array( '+1991-01-01T00:00:00Z' ),
			'1991/1/1' =>
				array( '+1991-01-01T00:00:00Z' ),
			'1991 1 1' =>
				array( '+1991-01-01T00:00:00Z' ),
			'1991.1.1' =>
				array( '+1991-01-01T00:00:00Z' ),
			'1991.01.01' =>
				array( '+1991-01-01T00:00:00Z' ),
		);

		// Only supported from PHP 8.1.7 (https://bugs.php.net/bug.php?id=51987, included in PHP 8.1.7)
		if ( version_compare( PHP_VERSION, '8.1.7', '>=' ) ) {
			// YYYY-DDD (DDDth day of the year)
			$valid['2022-033'] = array( '+0000000000002022-02-02T00:00:00Z', TimeValue::PRECISION_DAY, $gregorian );
		}

		foreach ( $valid as $value => $args ) {
			$timestamp = $args[0];
			$precision = isset( $args[1] ) ? $args[1] : TimeValue::PRECISION_DAY;
			$calendarModel = isset( $args[2] ) ? $args[2] : $gregorian;

			$argList[] = array(
				// Because PHP magically turns numeric keys into ints/floats
				(string)$value,
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel )
			);
		}

		return $argList;
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		$argLists = parent::NON_VALID_CASES;

		$invalid = array(
			'June June June',
			'111 111 111',
			'101st July 2015',
			'2015-07-101',
			'10  .10  .2010',
			'10...10...2010',
			'00-00-00',
			'99-00-00',
			'111-00-00',
			'2015-00-00',
			'00000000099-00-00',
			'00000002015-00-00',
			'92015-00-00',
			'Jann 2014',
			'1980x',
			'1980s',
			'1980',
			'1980ss',
			'1980er',
			'1980UTC',
			'1980America/New_York',
			'1980 America/New_York',
			'1980+3',
			'1980+x',
			'x',
			'x x x',
			'zz',
			'America/New_York',
			'1991 2',
			', 1966',
		);

		foreach ( $invalid as $value ) {
			$argLists[] = array( $value );
		}

		return $argLists;
	}

}
