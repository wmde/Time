<?php

namespace DataValues\Tests;

use DataValues\TimeValue;

/**
 * @covers DataValues\TimeValue
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class TimeValueTest extends DataValueTest {

	/**
	 * @see DataValueTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return 'DataValues\TimeValue';
	}

	public function validConstructorArgumentsProvider() {
		return array(
			'1 January' => array(
				'+2013-01-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Maximum timezone' => array(
				'+2013-01-01T00:00:00Z',
				7200, 9001, 9001,
				TimeValue::PRECISION_YEAR1G,
				'http://nyan.cat/original.php'
			),
			'Minimum timezone' => array(
				'+2013-01-01T00:00:00Z',
				-7200, 0, 42,
				TimeValue::PRECISION_YEAR,
				'http://nyan.cat/original.php'
			),
			'Negative year' => array(
				'-0005-01-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'No day' => array(
				'+2015-01-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_YEAR,
				'http://nyan.cat/original.php'
			),
			'No day and month' => array(
				'+2015-00-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_YEAR,
				'http://nyan.cat/original.php'
			),
			'Zero' => array(
				'+0000-00-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Minimum timestamp' => array(
				'-9999999999999999-12-31T23:59:61Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Maximum timestamp' => array(
				'+9999999999999999-12-31T23:59:61Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Leap year' => array(
				'+2000-02-29T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://nyan.cat/original.php'
			),
			'Non-leap year 29 February' => array(
				'+2015-02-29T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://nyan.cat/original.php'
			),
			'31 November' => array(
				'+2015-11-31T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://nyan.cat/original.php'
			),
		);
	}

	public function invalidConstructorArgumentsProvider() {
		return array(
			'String timezone' => array(
				'+00000002013-01-01T00:00:00Z',
				'0', 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Float timezone' => array(
				'+00000002013-01-01T00:00:00Z',
				4.2, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Timezone out of range' => array(
				'+00000002013-01-01T00:00:00Z',
				-20 * 3600, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Precision out of range' => array(
				'+00000002013-01-01T00:00:00Z',
				0, 0, 0,
				15,
				'http://nyan.cat/original.php'
			),
			'Integer timestamp' => array(
				42,
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Float before' => array(
				'+00000002013-01-01T00:00:00Z',
				0, 4.2, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Negative after' => array(
				'+00000002013-01-01T00:00:00Z',
				0, 0, -1,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Non-ISO timestamp' => array(
				'bla',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Invalid separators' => array(
				'+00000002013/01/01 00:00:00',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'No month' => array(
				'+2015-00-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://nyan.cat/original.php'
			),
			'Month out of range' => array(
				'+00000002013-13-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Day out of range' => array(
				'+00000002013-01-32T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Hour out of range' => array(
				'+00000002013-01-01T24:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Minute out of range' => array(
				'+00000002013-01-01T00:60:00Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Second out of range' => array(
				'+00000002013-01-01T00:00:62Z',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Invalid timezone' => array(
				'+00000002013-01-01T00:00:00+60',
				0, 0, 0,
				TimeValue::PRECISION_SECOND,
				'http://nyan.cat/original.php'
			),
			'Year to long' => array(
				'+00000000000000001-01-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://nyan.cat/original.php'
			),
		);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetTime( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[0], $time->getTime() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetTimezone( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[1], $time->getTimezone() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetBefore( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[2], $time->getBefore() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetAfter( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[3], $time->getAfter() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetPrecision( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[4], $time->getPrecision() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetCalendarModel( TimeValue $time, array $arguments ) {
		$this->assertEquals( $arguments[5], $time->getCalendarModel() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetValue( TimeValue $time, array $arguments ) {
		$this->assertTrue( $time->equals( $time->getValue() ) );
	}

	/**
	 * @dataProvider unpaddedYearsProvider
	 */
	public function testGivenUnpaddedYear_yearIsPadded( $year, $expected ) {
		$timeValue = new TimeValue(
			$year . '-01-01T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_DAY,
			'Stardate'
		);
		$this->assertSame( $expected . '-01-01T00:00:00Z', $timeValue->getTime() );
	}

	public function unpaddedYearsProvider() {
		return array(
			array( '+1', '+0001' ),
			array( '-10', '-0010' ),
			array( '+2015', '+2015' ),
			array( '+02015', '+2015' ),
			array( '+00000010000', '+10000' ),
			array( '+0000000000000001', '+0001' ),
			array( '+9999999999999999', '+9999999999999999' ),
		);
	}

}
