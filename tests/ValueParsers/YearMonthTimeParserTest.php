<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\MonthNameProvider;
use ValueParsers\YearMonthTimeParser;

/**
 * @covers ValueParsers\YearMonthTimeParser
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
class YearMonthTimeParserTest extends ValueParserTestCase {

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return YearMonthTimeParser
	 */
	protected function getInstance() {
		$monthNameProvider = $this->getMockBuilder( MonthNameProvider::class )
			->disableOriginalConstructor()
			->getMock();
		$monthNameProvider->expects( $this->once() )
			->method( 'getMonthNumbers' )
			->with( 'en' )
			->willReturn( array(
				'January' => 1,
				'Jan' => 1,
				// to test Unicode (it's Czech)
				'Březen' => 3,
				'April' => 4,
				'June' => 6,
			) );

		return new YearMonthTimeParser( $monthNameProvider );
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$argLists = array();

		$valid = array(
			// Whitespace
			"January 2016\n" =>
				array( '+2016-01-00T00:00:00Z' ),
			' January 2016 ' =>
				array( '+2016-01-00T00:00:00Z' ),
			' January 2016 CE ' =>
				array( '+2016-01-00T00:00:00Z' ),
			' January 2016 BCE ' =>
				array( '-2016-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),

			// leading zeros
			'1 00001999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1 0000000100001999' =>
				array( '+100001999-01-00T00:00:00Z' ),

			// Negative years
			'4 -1998' =>
				array( '-1998-04-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'April -1998' =>
				array( '-1998-04-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'-1998 4' =>
				array( '-1998-04-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'-1998 April' =>
				array( '-1998-04-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),

			// use string month names
			'Jan/1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'January/1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'January/1' =>
				array( '+0001-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'1999 January' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1999 January CE' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1999 January BCE' =>
				array( '-1999-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'January 1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'January 1999 CE' =>
				array( '+1999-01-00T00:00:00Z' ),
			'January 1999 BCE' =>
				array( '-1999-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'January-1' =>
				array( '+0001-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'JanuARY-1' =>
				array( '+0001-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'JaN/1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'januARY-1' =>
				array( '+0001-01-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'jan/1999' =>
				array( '+1999-01-00T00:00:00Z' ),

			// Unicode
			'Březen 1999' => array( '+1999-03-00T00:00:00Z' ),
			'Březen 1999 BCE' => array( '-1999-03-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'březen 1999' => array( '+1999-03-00T00:00:00Z' ),
			'březen 1999 BCE' => array( '-1999-03-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),

			// use different date separators
			'1-1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1/1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1 / 1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1 1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1,1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1.1999' =>
				array( '+1999-01-00T00:00:00Z' ),
			'1. 1999' =>
				array( '+1999-01-00T00:00:00Z' ),

			// presume mm/yy unless impossible month, in which case switch
			'12/12' =>
				array( '+0012-12-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'12/11' =>
				array( '+0011-12-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'11/12' =>
				array( '+0012-11-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'13/12' =>
				array( '+0013-12-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'12/13' =>
				array( '+0013-12-00T00:00:00Z', TimeValue::PRECISION_MONTH, $julian ),
			'2000 1' =>
				array( '+2000-01-00T00:00:00Z' ),

			// big years
			'April-1000000001' =>
				array( '+1000000001-04-00T00:00:00Z' ),
			'April 1000000001' =>
				array( '+1000000001-04-00T00:00:00Z' ),
			'1000000001 April' =>
				array( '+1000000001-04-00T00:00:00Z' ),
			'1 13000' =>
				array( '+13000-01-00T00:00:00Z' ),

			// parse 0 month as if no month has been entered
			'0.1999' =>
				array( '+1999-00-00T00:00:00Z', TimeValue::PRECISION_YEAR ),
			'1999 0' =>
				array( '+1999-00-00T00:00:00Z', TimeValue::PRECISION_YEAR ),
		);

		foreach ( $valid as $value => $expected ) {
			$timestamp = $expected[0];
			$precision = isset( $expected[1] ) ? $expected[1] : TimeValue::PRECISION_MONTH;
			$calendarModel = isset( $expected[2] ) ? $expected[2] : $gregorian;

			$argLists[] = array(
				(string)$value,
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel )
			);
		}

		return $argLists;
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		$argLists = parent::NON_VALID_CASES;

		$invalid = array(
			'',
			' ',
			'+',
			'-',

			// These are just wrong
			'June June June',
			'June June',
			'111 111 111',
			'Jann 2014',
			'13/13',
			'13,1999',
			'1999,13',
			"12 1950\n12",

			// Months with signs or more than two digits are most probably not a month
			'-0 1999',
			'-4 1999',
			'-4 -1999',
			'-April 1998',
			'000 1999',
			'012 1999',
			'00001 1999',
			'000000001 100001999',

			// Possible years BCE with digit groups
			'1 2 BC',
			'1 23 BC',
			'12 3 BC',
			'12 30 BC',
			'1 000 BC',
			'1,000 BC',
			'1 234 BCE',
			'1.234 BCE',
			'12 345 BCE',
			'12,345 BCE',

			// Don't parse stuff with separators in the year
			'june 200,000,000',
			'june 200.000.000',

			// Not within the scope of this parser
			'1 June 20000',
			'20000',
			'-1998',
			'1998 BCE',

			// era in conjunction with sign
			'April -1998 BCE',
			'April -1998 CE',
			'-1998 April BCE',
			'-1998 April CE',
		);

		foreach ( $invalid as $value ) {
			$argLists[] = array( $value );
		}

		return $argLists;
	}

}
