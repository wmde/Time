<?php

namespace ValueParsers\Test;

use DataValues\TimeValue;
use ValueParsers\CalendarModelParser;
use ValueParsers\ParserOptions;
use ValueFormatters\TimeFormatter;
use ValueParsers\BaseTimeParser;

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
	 * @return BaseTimeParser
	 */
	protected function getInstance() {
		$options = $this->newParserOptions();

		$class = $this->getParserClass();
		return new $class( $options );
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function validInputProvider() {
		$emptyOpts = new ParserOptions();

		$julianOpts = clone $emptyOpts;
		$julianOpts->setOption( BaseTimeParser::OPT_CALENDAR, BaseTimeParser::CALENDAR_JULIAN );

		$gregorianOpts = clone $emptyOpts;
		$gregorianOpts->setOption( BaseTimeParser::OPT_CALENDAR, BaseTimeParser::CALENDAR_GREGORIAN );

		$prec10aOpts = clone $emptyOpts;
		$prec10aOpts->setOption( BaseTimeParser::OPT_PRECISION, TimeValue::PRECISION_10a );

		$precDayOpts = clone $emptyOpts;
		$precDayOpts->setOption( BaseTimeParser::OPT_PRECISION, TimeValue::PRECISION_DAY );

		$noPrecOpts = clone $emptyOpts;
		$noPrecOpts->setOption( BaseTimeParser::OPT_PRECISION, BaseTimeParser::PRECISION_NONE );

		$valid = array(
			// BaseTimeParser
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

		);

		$argLists = array();
		foreach ( $valid as $key => $value ) {
			list( $timeValue, $opts ) = $value;
			// Because PHP turns some of them into ints/floats using black magic (string)
			$argLists[] = array(
				(string)$key,
				$timeValue,
				new BaseTimeParser( new CalendarModelParser( $opts ), $opts )
			);
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
