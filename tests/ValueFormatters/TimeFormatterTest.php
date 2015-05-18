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
 * @author Thiemo Mättig
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
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$tests = array(
			'+2013-07-16T00:00:00Z' => array(
				'+00000002013-07-16T00:00:00Z',
			),
			'+0000-01-01T00:00:00Z' => array(
				'+00000000000-01-01T00:00:00Z',
			),
			'+0001-01-14T00:00:00Z' => array(
				'+00000000001-01-14T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$julian
			),
			'+10000-01-01T00:00:00Z' => array(
				'+00000010000-01-01T00:00:00Z',
			),
			'-0001-01-01T00:00:00Z' => array(
				'-00000000001-01-01T00:00:00Z',
			),
			'+2013-07-17T00:00:00Z' => array(
				'+00000002013-07-17T00:00:00Z',
				TimeValue::PRECISION_MONTH,
			),
			'+2013-07-18T00:00:00Z' => array(
				'+00000002013-07-18T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+2013-07-19T00:00:00Z' => array(
				'+00000002013-07-19T00:00:00Z',
				TimeValue::PRECISION_10a,
			),
		);

		$argLists = array();

		// TODO: Test with different parser options.
		$options = new FormatterOptions();

		foreach ( $tests as $expected => $args ) {
			$timestamp = $args[0];
			$precision = isset( $args[1] ) ? $args[1] : TimeValue::PRECISION_DAY;
			$calendarModel = isset( $args[2] ) ? $args[2] : $gregorian;

			$argLists[] = array(
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel ),
				$expected,
				$options
			);
		}

		return $argLists;
	}

}
