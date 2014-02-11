<?php

namespace ValueFormatters\Test;

use DataValues\TimeValue;
use ValueParsers\CalenderModelParser;
use ValueParsers\Test\ValueParserTestBase;
use ValueFormatters\TimeFormatter;
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
	 * @see ValueParserTestBase::getParserClass
	 * @since 0.1
	 * @return string
	 */
	protected function getParserClass() {
		return 'ValueParsers\TimeParser';
	}

	/**
	 * @since 0.1
	 * @return TimeParser
	 */
	protected function getInstance() {
		$options = $this->newParserOptions();

		$class = $this->getParserClass();
		return new $class( new CalenderModelParser(), $options );
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function validInputProvider() {
		$argLists = array();

		$valid = array(
			'+0000000000002013-07-16T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000002013-07-16T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_DAY,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000002013-07-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000002013-07-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_MONTH,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000002013-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000002013-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_YEAR,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000002000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000002000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000020000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000020000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_10ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000200000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000000200000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_100ka,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000002000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000002000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000020000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000020000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_10Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000200000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000000200000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_100Ma,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000002000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000002000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000020000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000020000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000200000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0000200000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0002000000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0002000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0020000000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0020000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0200000000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+0200000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+2000000000000000-00-00T00:00:00Z' => TimeValue::newFromArray( array(
					'time' => '+2000000000000000-00-00T00:00:00Z',
					'timezone' => 0,
					'before' => 0,
					'after' => 0,
					'precision' => TimeValue::PRECISION_Ga,
					'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
				) ),
			'+0000000000002013-07-16T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '+0000000000002013-07-16T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'+0000000000000000-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '+0000000000000000-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'+0000000000000001-01-14T00:00:00Z (Julian)' => TimeValue::newFromArray( array(
				'time' => '+0000000000000001-01-14T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_JULIAN
			) ),
			'+0000000000010000-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '+0000000000010000-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'-0000000000000001-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '-0000000000000001-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'-00000000001-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '-0000000000000001-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'-000001-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '-0000000000000001-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
			'-1-01-01T00:00:00Z (Gregorian)' => TimeValue::newFromArray( array(
				'time' => '-0000000000000001-01-01T00:00:00Z',
				'timezone' => 0,
				'before' => 0,
				'after' => 0,
				'precision' => TimeValue::PRECISION_DAY,
				'calendarmodel' => TimeFormatter::CALENDAR_GREGORIAN
			) ),
		);

		foreach ( $valid as $value => $expected ) {
			// Because PHP turns some of them into ints/floats using black magic
			$value = (string)$value;
			$argLists[] = array( $value, $expected );
		}

		return $argLists;
	}

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