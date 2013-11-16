<?php

namespace ValueFormatters\Test;

use DataValues\TimeValue;
use ValueFormatters\TimeFormatter;

/**
 * @covers ValueFormatters\TimeFormatter
 *
 * @group ValueFormatters
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author H. Snater < mediawiki@snater.com >
 */
class TimeFormatterTest extends ValueFormatterTestBase {

	/**
	 * @see ValueFormatterTestBase::validProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function validProvider() {
		$tests = array(
			'+00000002013-07-16T00:00:00Z (Gregorian)' => array(
				'+00000002013-07-16T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+00000000000-01-01T00:00:00Z (Gregorian)' => array(
				'+00000000000-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+00000000001-01-14T00:00:00Z (Gregorian)' => array(
				'+00000000001-01-14T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_JULIAN
			),
			'+00000010000-01-01T00:00:00Z (Gregorian)' => array(
				'+00000010000-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'-00000000001-01-01T00:00:00Z (Gregorian)' => array(
				'-00000000001-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+00000002013-07-16T00:00:00Z (Gregorian)' => array(
				'+00000002013-07-16T00:00:00Z',
				0,
				0,
				0,
				10,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+00000002013-07-16T00:00:00Z (Gregorian)' => array(
				'+00000002013-07-16T00:00:00Z',
				0,
				0,
				0,
				9,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+00000002013-07-16T00:00:00Z (Gregorian)' => array(
				'+00000002013-07-16T00:00:00Z',
				0,
				0,
				0,
				8,
				TimeFormatter::CALENDAR_GREGORIAN
			),
		);

		$argLists = array();

		// TODO: Test with different parser options.
		$options = new \ValueFormatters\FormatterOptions();

		foreach ( $tests as $expected => $args ) {
			$timeValue = new TimeValue( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
			$argLists[] = array( $timeValue, $expected, $options );
		}

		return $argLists;
	}

	/**
	 * @see ValueFormatterTestBase::getFormatterClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	protected function getFormatterClass() {
		return 'ValueFormatters\TimeFormatter';
	}

}
