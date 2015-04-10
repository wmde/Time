<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\CalendarModelParser;
use ValueParsers\IsoTimestampParser;
use ValueParsers\ParserOptions;

/**
 * @covers ValueParsers\IsoTimestampParser
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class IsoTimestampParserTest extends ValueParserTestBase {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return IsoTimestampParser
	 */
	protected function getInstance() {
		return new IsoTimestampParser();
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$julianOpts = new ParserOptions();
		$julianOpts->setOption( IsoTimestampParser::OPT_CALENDAR, $julian );

		$gregorianOpts = new ParserOptions();
		$gregorianOpts->setOption( IsoTimestampParser::OPT_CALENDAR, $gregorian );

		$prec10aOpts = new ParserOptions();
		$prec10aOpts->setOption( IsoTimestampParser::OPT_PRECISION, TimeValue::PRECISION_10a );

		$precDayOpts = new ParserOptions();
		$precDayOpts->setOption( IsoTimestampParser::OPT_PRECISION, TimeValue::PRECISION_DAY );

		$noPrecOpts = new ParserOptions();
		$noPrecOpts->setOption( IsoTimestampParser::OPT_PRECISION, IsoTimestampParser::PRECISION_NONE );

		$valid = array(
			// Empty options tests
			'+0000000000002013-07-16T00:00:00Z' => array(
				'+0000000000002013-07-16T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'+0000000000002013-07-00T00:00:00Z' => array(
				'+0000000000002013-07-00T00:00:00Z',
				TimeValue::PRECISION_MONTH,
			),
			'+0000000000002013-00-00T00:00:00Z' => array(
				'+0000000000002013-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+0000000000000000-00-00T00:00:00Z' => array(
				'+0000000000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+0000000000002000-00-00T00:00:00Z' => array(
				'+0000000000002000-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+0000000000008000-00-00T00:00:00Z' => array(
				'+0000000000008000-00-00T00:00:00Z',
				TimeValue::PRECISION_ka,
			),
			'+0000000000020000-00-00T00:00:00Z' => array(
				'+0000000000020000-00-00T00:00:00Z',
				TimeValue::PRECISION_10ka,
			),
			'+0000000000200000-00-00T00:00:00Z' => array(
				'+0000000000200000-00-00T00:00:00Z',
				TimeValue::PRECISION_100ka,
			),
			'+0000000002000000-00-00T00:00:00Z' => array(
				'+0000000002000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ma,
			),
			'+0000000020000000-00-00T00:00:00Z' => array(
				'+0000000020000000-00-00T00:00:00Z',
				TimeValue::PRECISION_10Ma,
			),
			'+0000000200000000-00-00T00:00:00Z' => array(
				'+0000000200000000-00-00T00:00:00Z',
				TimeValue::PRECISION_100Ma,
			),
			'+0000002000000000-00-00T00:00:00Z' => array(
				'+0000002000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0000020000000000-00-00T00:00:00Z' => array(
				'+0000020000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0000200000000000-00-00T00:00:00Z' => array(
				'+0000200000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0002000000000000-00-00T00:00:00Z' => array(
				'+0002000000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0020000000000000-00-00T00:00:00Z' => array(
				'+0020000000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0200000000000000-00-00T00:00:00Z' => array(
				'+0200000000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+2000000000000000-00-00T00:00:00Z' => array(
				'+2000000000000000-00-00T00:00:00Z',
				TimeValue::PRECISION_Ga,
			),
			'+0000000000002013-07-16T00:00:00Z (Gregorian)' => array(
				'+0000000000002013-07-16T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'+0000000000000000-01-01T00:00:00Z (Gregorian)' => array(
				'+0000000000000000-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,

			),
			'+0000000000000001-01-14T00:00:00Z (Julian)' => array(
				'+0000000000000001-01-14T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$julian,
			),
			'+0000000000010000-01-01T00:00:00Z (Gregorian)' => array(
				'+0000000000010000-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'-0000000000000001-01-01T00:00:00Z (Gregorian)' => array(
				'-0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'-00000000001-01-01T00:00:00Z (Gregorian)' => array(
				'-0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'-000001-01-01T00:00:00Z (Gregorian)' => array(
				'-0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'-1-01-01T00:00:00Z (Gregorian)' => array(
				'-0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Tests with different options
			'-1-01-02T00:00:00Z' => array(
				'-0000000000000001-01-02T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$gregorian,
				$gregorianOpts,
			),
			'-1-01-03T00:00:00Z' => array(
				'-0000000000000001-01-03T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$julian,
				$julianOpts,
			),
			'-1-01-04T00:00:00Z' => array(
				'-0000000000000001-01-04T00:00:00Z',
				TimeValue::PRECISION_10a,
				$gregorian,
				$prec10aOpts,
			),
			'-1-01-05T00:00:00Z' => array(
				'-0000000000000001-01-05T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$gregorian,
				$noPrecOpts,
			),

			'+1999-00-00T00:00:00Z' => array(
				'+0000000000001999-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+2000-00-00T00:00:00Z' => array(
				'+0000000000002000-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+2010-00-00T00:00:00Z' => array(
				'+0000000000002010-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),

			// Optional sign character
			'2015-01-01T00:00:00Z' => array(
				'+0000000000002015-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Optional time zone
			'2015-01-01T00:00:00' => array(
				'+0000000000002015-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Actual minus character from Unicode; roundtrip with TimeDetailsFormatter
			"\xE2\x88\x922015-01-01T00:00:00" => array(
				'-0000000000002015-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Optional second
			'2015-01-01T00:00' => array(
				'+0000000000002015-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Optional hour and minute
			'2015-01-01' => array(
				'+0000000000002015-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'60-01-01' => array(
				'+0000000000000060-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),

			// Years < 60 require either the time part or a year with more than 2 digits
			'1-01-01T00:00' => array(
				'+0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'001-01-01' => array(
				'+0000000000000001-01-01T00:00:00Z',
				TimeValue::PRECISION_DAY,

			),

			// Day zero
			'2015-01-00' => array(
				'+0000000000002015-01-00T00:00:00Z',
				TimeValue::PRECISION_MONTH,
			),

			// Month zero
			'2015-00-00' => array(
				'+0000000000002015-00-00T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),

			// Leap seconds are a valid concept
			'+2015-01-01T00:00:60Z' => array(
				'+0000000000002015-01-01T00:00:60Z',
				TimeValue::PRECISION_SECOND,
			),

			// Tests for correct precision when a bad precision is passed through the opts
			// @see https://bugzilla.wikimedia.org/show_bug.cgi?id=62730
			'+0000000000000012-12-00T00:00:00Z' => array(
				'+0000000000000012-12-00T00:00:00Z',
				TimeValue::PRECISION_MONTH,
				$gregorian,
				$precDayOpts,
			),

		);

		$argLists = array();
		foreach ( $valid as $key => $value ) {
			$timestamp = $value[0];
			$precision = isset( $value[1] ) ? $value[1] : TimeValue::PRECISION_DAY;
			$calendareModel = isset( $value[2] ) ? $value[2] : $gregorian;
			$options = isset( $value[3] ) ? $value[3] : null;

			$argLists[] = array(
				// Because PHP magically turns numeric keys into ints/floats
				(string)$key,
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendareModel ),
				new IsoTimestampParser( new CalendarModelParser( $options ), $options )
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
			'59-01-01',
			'+59-01-01',
			'+2015-13-01T00:00:00Z',
			'+2015-01-32T00:00:00Z',
			'+2015-01-01T24:00:00Z',
			'+2015-01-01T00:60:00Z',
			'+2015-01-01T00:00:63Z',
			'1234567890873',
			2134567890
		);

		foreach ( $invalid as $value ) {
			$argLists[] = array( $value );
		}

		return $argLists;
	}

}
