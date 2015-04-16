<?php

namespace ValueFormatters\Test;

use DataValues\TimeValue;
use ValueFormatters\FormatterOptions;
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
	 * @deprecated since 0.2, just use getInstance.
	 */
	protected function getFormatterClass() {
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueFormatterTestBase::getInstance
	 *
	 * @param FormatterOptions|null $options
	 *
	 * @return TimeFormatter
	 */
	protected function getInstance( FormatterOptions $options = null ) {
		return new TimeFormatter( $options );
	}

	/**
	 * @see ValueFormatterTestBase::validProvider
	 */
	public function validProvider() {
		$tests = array(
			'+2013-07-16T00:00:00Z' => array(
				'+00000002013-07-16T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+0000-01-01T00:00:00Z' => array(
				'+00000000000-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+0001-01-14T00:00:00Z' => array(
				'+00000000001-01-14T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_JULIAN
			),
			'+10000-01-01T00:00:00Z' => array(
				'+00000010000-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'-0001-01-01T00:00:00Z' => array(
				'-00000000001-01-01T00:00:00Z',
				0,
				0,
				0,
				11,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+2013-07-17T00:00:00Z' => array(
				'+00000002013-07-17T00:00:00Z',
				0,
				0,
				0,
				10,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+2013-07-18T00:00:00Z' => array(
				'+00000002013-07-18T00:00:00Z',
				0,
				0,
				0,
				9,
				TimeFormatter::CALENDAR_GREGORIAN
			),
			'+2013-07-19T00:00:00Z' => array(
				'+00000002013-07-19T00:00:00Z',
				0,
				0,
				0,
				8,
				TimeFormatter::CALENDAR_GREGORIAN
			),
		);

		$argLists = array();

		// TODO: Test with different parser options.
		$options = new FormatterOptions();

		foreach ( $tests as $expected => $args ) {
			$timeValue = new TimeValue( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
			$argLists[] = array( $timeValue, $expected, $options );
		}

		return $argLists;
	}

}
