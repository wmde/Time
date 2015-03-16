<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueFormatters\TimeFormatter;
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
 */
class TimeParserTest extends ValueParserTestBase {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		return 'ValueParsers\TimeParser';
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
		$emptyOpts = new ParserOptions();

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
				TimeValue::newFromArray( array(
					'time' => '+0000000000002013-07-16T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000002013-07-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002013-07-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_MONTH,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000002013-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002013-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000002000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000008000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000008000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000020000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000020000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_10ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000200000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000200000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_100ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000002000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000002000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000020000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000020000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_10Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000200000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000200000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_100Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000002000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000002000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000020000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000020000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000200000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000200000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0002000000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0002000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0020000000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0020000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0200000000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0200000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+2000000000000000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+2000000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$emptyOpts,
			),
			'+0000000000002013-07-16T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002013-07-16T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'+0000000000000000-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000000000-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'+0000000000000001-01-14T00:00:00Z (Julian)' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000000001-01-14T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_JULIAN
			) ),
				$emptyOpts,
			),
			'+0000000000010000-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000010000-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'-0000000000000001-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'-00000000001-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'-000001-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),
			'-1-01-01T00:00:00Z (Gregorian)' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-01T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$emptyOpts,
			),

			//Tests with different options
			'-1-01-02T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-02T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$gregorianOpts,
			),
			'-1-01-03T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-03T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_JULIAN
			) ),
				$julianOpts,
			),
			'-1-01-04T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-04T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_10a,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$prec10aOpts,
			),
			'-1-01-05T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '-0000000000000001-01-05T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$noPrecOpts,
			),

			'+1999-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000001999-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$noPrecOpts,
			),
			'+2000-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$noPrecOpts,
			),
			'+2010-00-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000002010-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
				$noPrecOpts,
			),

			//Tests for correct precision when a bad precision is passed through the opts
			//@see https://bugzilla.wikimedia.org/show_bug.cgi?id=62730
			'+0000000000000012-12-00T00:00:00Z' => array(
				TimeValue::newFromArray( array(
					'time' => '+0000000000000012-12-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_MONTH,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
				$precDayOpts,
			),

		);

		$argLists = array();
		foreach ( $valid as $key => $value ) {
			list( $timeValue, $opts ) = $value;
			// Because PHP turns some of them into ints/floats using black magic (string)
			$argLists[] = array(
				(string)$key,
				$timeValue,
				new TimeParser( new CalendarModelParser( $opts ), $opts )
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
