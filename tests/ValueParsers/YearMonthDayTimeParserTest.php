<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\EraParser;
use ValueParsers\YearMonthDayTimeParser;

/**
 * @covers ValueParsers\YearMonthDayTimeParser
 *
 * @group ValueParsers
 * @group WikibaseLib
 * @group Wikibase
 * @group TimeParsers
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class YearMonthDayTimeParserTest extends StringValueParserTest {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return YearMonthDayTimeParser
	 */
	protected function getInstance() {
		return new YearMonthDayTimeParser(
			new EraParser()
		);
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$valid = array(
			// YMD
			'2015-12-31' => array( '+2015-12-31T00:00:00Z' ),
			'2015 12 31' => array( '+2015-12-31T00:00:00Z' ),
			'2015 12 11' => array( '+2015-12-11T00:00:00Z' ),

			// DMY
			'31.12.2015' => array( '+2015-12-31T00:00:00Z' ),
			'31. 12. 2015' => array( '+2015-12-31T00:00:00Z' ),
			'31/12/2015' => array( '+2015-12-31T00:00:00Z' ),
			'31 12 2015' => array( '+2015-12-31T00:00:00Z' ),
			'31th 12th 2015' => array( '+2015-12-31T00:00:00Z' ),
			'day 31, month 12, year 2015' => array( '+2015-12-31T00:00:00Z' ),

			// MDY
			'12/31/2015' => array( '+2015-12-31T00:00:00Z' ),
			'12-31-2015' => array( '+2015-12-31T00:00:00Z' ),
			'12 31 2015' => array( '+2015-12-31T00:00:00Z' ),

			// Julian
			'32-12-31' => array( '+0032-12-31T00:00:00Z', $julian ),
			'31.12.32' => array( '+0032-12-31T00:00:00Z', $julian ),
			'12/31/60' => array( '+0060-12-31T00:00:00Z', $julian ),

			// Negative years
			'-2015-12-31' => array( '-2015-12-31T00:00:00Z', $julian ),
			'year -2015-12-31' => array( '-2015-12-31T00:00:00Z', $julian ),
			'31 12 -2015' => array( '-2015-12-31T00:00:00Z', $julian ),
			'12/31/-2015' => array( '-2015-12-31T00:00:00Z', $julian ),
			'2015-12-31 BC' => array( '-2015-12-31T00:00:00Z', $julian ),
			'31 12 2015 BC' => array( '-2015-12-31T00:00:00Z', $julian ),
			'12/31/2015 BC' => array( '-2015-12-31T00:00:00Z', $julian ),

			// A negative number must be the year.
			'year -3-2-13' => array( '-0003-02-13T00:00:00Z', $julian ),
			'13. 2. -3' => array( '-0003-02-13T00:00:00Z', $julian ),
		);

		$cases = array();

		foreach ( $valid as $value => $args ) {
			$timestamp = $args[0];
			$calendarModel = isset( $args[1] ) ? $args[1] : $gregorian;

			$cases[] = array(
				// Because PHP magically turns numeric keys into ints/floats
				(string)$value,
				new TimeValue( $timestamp, 0, 0, 0, TimeValue::PRECISION_DAY, $calendarModel )
			);
		}

		return $cases;
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		$invalid = array(
			// This parser can only parse strings that contain exactly three numbers.
			'2015',
			'12.2015',
			'May 1 2015',
			'1. May 2015',
			'1 2015-12-31',
			'31.12.2015 23',
			'31.12.2015 23:59',
			'+2015-12-31T00:00:00Z',

			// Can be confused with a time (HMS)
			'12:31:59',
			'12:59:59',
			'23:12:59',
			'23:12:31',

			// No year can be identified if all numbers are smaller than 32.
			'12 12 12',
			'31 12 12',
			'12 31 12',
			'31 31 12',
			'12 12 31',
			'31 12 31',
			'12 31 31',
			'31 31 31',

			// Year can be identified, but month and day can not be distinguished.
			'12 32 12',
			'32 32 12',
			'12 12 32',
			'32 12 32',
			'12 32 32',
			'32 32 32',

			// A YDM format does really exist and should not be parsed.
			'2015 31 12',

			// Formats DYM and MYD do not exist and should not be parsed.
			'12 2015 31',
			'31 2015 12',

			// Duplicate era.
			'year -2015-12-31 BC',
			'31.12.-2015 BC',

			// Zeros.
			'2015-12-00',
			'0. 12. 2015',

			// To long.
			'2015-12-031',
			'2015-012-31',
		);

		$cases = parent::invalidInputProvider();

		foreach ( $invalid as $value ) {
			$cases[] = array( $value );
		}

		return $cases;
	}

}
