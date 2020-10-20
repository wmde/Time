<?php

namespace ValueFormatters\Test;

use DataValues\TimeValue;
use PHPUnit\Framework\TestCase;
use ValueFormatters\FormatterOptions;
use ValueFormatters\TimeFormatter;
use ValueFormatters\ValueFormatter;

/**
 * @covers ValueFormatters\TimeFormatter
 *
 * @group ValueFormatters
 * @group DataValueExtensions
 *
 * @license GPL-2.0-or-later
 * @author H. Snater < mediawiki@snater.com >
 * @author Thiemo Kreuz
 */
class TimeFormatterTest extends TestCase {

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

		$baseOptions = new FormatterOptions();

		$tests = array(
			'+2013-07-16T00:00:00Z' => array(
				'+2013-07-16T00:00:00Z',
			),
			'+0000-01-01T00:00:00Z' => array(
				'+0000-01-01T00:00:00Z',
			),

			// Different calendar models
			'+0001-01-14T00:00:00Z' => array(
				'+0001-01-14T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$julian
			),

			// Different years
			'+10000-01-01T00:00:00Z' => array(
				'+10000-01-01T00:00:00Z',
			),
			'-0001-01-01T00:00:00Z' => array(
				'-0001-01-01T00:00:00Z',
			),

			// Different precisions
			'+2013-07-17T00:00:00Z' => array(
				'+2013-07-17T00:00:00Z',
				TimeValue::PRECISION_MONTH,
			),
			'+2013-07-18T00:00:00Z' => array(
				'+2013-07-18T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'+2013-07-19T00:00:00Z' => array(
				'+2013-07-19T00:00:00Z',
				TimeValue::PRECISION_YEAR10,
			),
		);

		$argLists = array();

		foreach ( $tests as $expected => $args ) {
			$timestamp = $args[0];
			$precision = isset( $args[1] ) ? $args[1] : TimeValue::PRECISION_DAY;
			$calendarModel = isset( $args[2] ) ? $args[2] : $gregorian;
			$options = isset( $args[3] ) ? $args[3] : $baseOptions;

			$argLists[] = array(
				new TimeValue( $timestamp, 0, 0, 0, $precision, $calendarModel ),
				$expected,
				$options
			);
		}

		return $argLists;
	}

	/**
	 * @dataProvider validProvider
	 *
	 * @since 0.1
	 *
	 * @param mixed $value
	 * @param mixed $expected
	 * @param FormatterOptions|null $options
	 * @param ValueFormatter|null $formatter
	 */
	public function testValidFormat(
		$value,
		$expected,
		FormatterOptions $options = null,
		ValueFormatter $formatter = null
	) {
		if ( $formatter === null ) {
			$formatter = $this->getInstance( $options );
		}

		$this->assertSame( $expected, $formatter->format( $value ) );
	}

}
