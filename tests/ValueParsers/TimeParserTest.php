<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\CalendarModelParser;
use ValueParsers\ParserOptions;
use ValueParsers\TimeParser;

/**
 * @covers \ValueParsers\TimeParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class TimeParserTest extends ValueParserTestBase {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return TimeParser
	 */
	protected function getInstance() {
		return new TimeParser();
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$julianOpts = new ParserOptions();
		$julianOpts->setOption( TimeParser::OPT_CALENDAR, TimeParser::CALENDAR_JULIAN );

		$gregorianOpts = new ParserOptions();
		$gregorianOpts->setOption( TimeParser::OPT_CALENDAR, TimeParser::CALENDAR_GREGORIAN );

		$prec10aOpts = new ParserOptions();
		$prec10aOpts->setOption( TimeParser::OPT_PRECISION, TimeValue::PRECISION_10a );

		$precDayOpts = new ParserOptions();
		$precDayOpts->setOption( TimeParser::OPT_PRECISION, TimeValue::PRECISION_DAY );

		$noPrecOpts = new ParserOptions();
		$noPrecOpts->setOption( TimeParser::OPT_PRECISION, TimeParser::PRECISION_NONE );

		$valid = array(
			// Empty options tests
			'+0000000000002013-07-16T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002013-07-16T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000002013-07-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002013-07-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_MONTH,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000002013-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002013-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_YEAR,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000002000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_YEAR,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000008000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000008000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_ka,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000020000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000020000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_10ka,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000200000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000200000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_100ka,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000002000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000002000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ma,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000020000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000020000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_10Ma,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000200000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000200000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_100Ma,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000002000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000002000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000020000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000020000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000200000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000200000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0002000000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0002000000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0020000000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0020000000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0200000000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0200000000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+2000000000000000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+2000000000000000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_Ga,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000002013-07-16T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'+0000000000002013-07-16T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000000000-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'+0000000000000000-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+0000000000000001-01-14T00:00:00Z (Julian)' => array(
				new TimeValue(
					'+0000000000000001-01-14T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_JULIAN
				),
			),
			'+0000000000010000-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'+0000000000010000-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'-0000000000000001-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'-0000000000000001-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'-00000000001-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'-0000000000000001-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'-000001-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'-0000000000000001-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'-1-01-01T00:00:00Z (Gregorian)' => array(
				new TimeValue(
					'-0000000000000001-01-01T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
			),

			// Tests with different options
			'-1-01-02T00:00:00Z' => array(
				new TimeValue(
					'-0000000000000001-01-02T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
				$gregorianOpts,
			),
			'-1-01-03T00:00:00Z' => array(
				new TimeValue(
					'-0000000000000001-01-03T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_JULIAN
				),
				$julianOpts,
			),
			'-1-01-04T00:00:00Z' => array(
				new TimeValue(
					'-0000000000000001-01-04T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_10a,
					TimeParser::CALENDAR_GREGORIAN
				),
				$prec10aOpts,
			),
			'-1-01-05T00:00:00Z' => array(
				new TimeValue(
					'-0000000000000001-01-05T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_DAY,
					TimeParser::CALENDAR_GREGORIAN
				),
				$noPrecOpts,
			),

			'+1999-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000001999-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_YEAR,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+2000-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002000-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_YEAR,
					TimeParser::CALENDAR_GREGORIAN
				),
			),
			'+2010-00-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000002010-00-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_YEAR,
					TimeParser::CALENDAR_GREGORIAN
				),
			),

			// Tests for correct precision when a bad precision is passed through the opts
			// @see https://bugzilla.wikimedia.org/show_bug.cgi?id=62730
			'+0000000000000012-12-00T00:00:00Z' => array(
				new TimeValue(
					'+0000000000000012-12-00T00:00:00Z',
					0, 0, 0,
					TimeValue::PRECISION_MONTH,
					TimeParser::CALENDAR_GREGORIAN
				),
				$precDayOpts,
			),

		);

		$argLists = array();
		foreach ( $valid as $key => $value ) {
			$timeValue = $value[0];
			$options = isset( $value[1] ) ? $value[1] : null;

			$argLists[] = array(
				// Because PHP magically turns numeric keys into ints/floats
				(string)$key,
				$timeValue,
				new TimeParser( new CalendarModelParser( $options ), $options )
			);
		}

		return $argLists;
	}

	/**
	 * @see ValueParserTestBase::invalidInputProvider
	 */
	public function invalidInputProvider() {
		$argLists = array();

		$invalid = array(
			true,
			false,
			null,
			array(),
			'foooooooooo',
			'1 June 2014',
			'1234567890873',
			2134567890
		);

		foreach ( $invalid as $value ) {
			$argLists[] = array( $value );
		}

		return $argLists;
	}

}
