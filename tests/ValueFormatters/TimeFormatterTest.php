<?php

namespace ValueFormatters\Test;

use DataValues\TimeValue;
use ValueFormatters\FormatterOptions;
use ValueFormatters\TimeFormatter;
use ValueFormatters\ValueFormatter;

/**
 * @covers ValueFormatters\TimeFormatter
 *
 * @group ValueFormatters
 * @group DataValueExtensions
 *
 * @license GPL-2.0+
 * @author H. Snater < mediawiki@snater.com >
 * @author Thiemo Mättig
 */
class TimeFormatterTest extends ValueFormatterTestBase {

	/**
	 * @deprecated since DataValues Interfaces 0.2, just use getInstance.
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
	 * @return ValueFormatter
	 */
	private function getTimestampFormatter() {
		$mock = $this->getMock( 'ValueFormatters\ValueFormatter' );
		$mock->expects( $this->any() )
			->method( 'format' )
			->will( $this->returnValue( '<timestamp>' ) );

		return $mock;
	}

	/**
	 * @see ValueFormatterTestBase::validProvider
	 */
	public function validProvider() {
		$gregorian = 'http://www.wikidata.org/entity/Q1985727';
		$julian = 'http://www.wikidata.org/entity/Q1985786';

		$baseOptions = new FormatterOptions();
		$baseOptions->setOption( TimeFormatter::OPT_CALENDARNAMES, array(
			$gregorian => '<Gregorian>',
			$julian => '<Julian>',
		) );

		$timestampFormatterOptions = new FormatterOptions();
		$timestampFormatterOptions->setOption(
			TimeFormatter::OPT_TIME_ISO_FORMATTER,
			$this->getTimestampFormatter()
		);

		$tests = array(
			'2013-07-16' => array(
				'+2013-07-16T00:00:00Z',
			),

			// Custom timestamp formatter
			'<timestamp>' => array(
				'+2013-07-16T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$gregorian,
				$timestampFormatterOptions,
			),

			// Different calendar models
			'1701-12-14' => array(
				'+1701-12-14T00:00:00Z',
				TimeValue::PRECISION_DAY,
				$julian,
			),
			'1702-12-14' => array(
				'+1702-12-14T00:00:00Z',
				TimeValue::PRECISION_DAY,
				'Stardate',
			),

			// Different years
			"\xE2\x88\x9210000-01-01" => array(
				'-010000-01-01T00:00:00Z',
			),
			"\xE2\x88\x920001-01-01" => array(
				'-1-01-01T00:00:00Z',
			),
			"\xE2\x88\x920100-01-01" => array(
				'-100-01-01T00:00:00Z',
			),
			"\xE2\x88\x920000-01-01" => array(
				'-0-01-01T00:00:00Z',
			),
			'0000-01-01' => array(
				'+0-01-01T00:00:00Z',
			),
			'0001-01-01' => array(
				'+1-01-01T00:00:00Z',
			),
			'0100-01-01' => array(
				'+100-01-01T00:00:00Z',
			),
			'10000-01-01' => array(
				'+010000-01-01T00:00:00Z',
			),

			// Different precisions
			'2000' => array(
				'+2000-01-01T00:00:00Z',
				TimeValue::PRECISION_YEAR1G,
			),
			'2008' => array(
				'+2008-01-08T00:00:00Z',
				TimeValue::PRECISION_YEAR10,
			),
			'2009' => array(
				'+2009-01-09T00:00:00Z',
				TimeValue::PRECISION_YEAR,
			),
			'2010-07' => array(
				'+2010-07-10T00:00:00Z',
				TimeValue::PRECISION_MONTH,
			),
			'2011-07-11' => array(
				'+2011-07-11T00:00:00Z',
				TimeValue::PRECISION_DAY,
			),
			'2012-07-12T00' => array(
				'+2012-07-12T00:00:00Z',
				TimeValue::PRECISION_HOUR,
			),
			'2013-07-13T00:00' => array(
				'+2013-07-13T00:00:00Z',
				TimeValue::PRECISION_MINUTE,
			),
			'2014-07-14T00:00:00' => array(
				'+2014-07-14T00:00:00Z',
				TimeValue::PRECISION_SECOND,
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
				(string)$expected,
				$options
			);
		}

		return $argLists;
	}

}
