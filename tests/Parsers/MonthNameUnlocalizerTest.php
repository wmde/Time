<?php

namespace DataValues\Time\Parsers\Tests;

use DataValues\Time\Parsers\MonthNameUnlocalizer;

/**
 * @covers DataValues\Time\Parsers\MonthNameUnlocalizer
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MonthNameUnlocalizerTest extends \PHPUnit_Framework_TestCase {

	private function getValidTargetMonthNames() {
		return array(
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December',
		);
	}

	private function getValidMonthNameMaps() {
		return array(
			// Actual first 6 german months
			array(
				1 => 'Januar',
				2 => 'Februar',
				3 => 'Marz',
				4 => 'April',
				5 => 'Mai',
				6 => 'Juni',
			),
			// Some random strings for testing
			array(
				12 => '12month12',
				11 => '11month11',
			),
			// first 6 EN abbreviations
			array(
				1 => 'Jan',
				2 => 'Feb',
				3 => 'Mar',
				4 => 'Apr',
				5 => 'May',
				6 => 'Jun',
			)
		);
	}

	private function getUnlocalizer() {
		return new MonthNameUnlocalizer(
			$this->getValidTargetMonthNames(),
			$this->getValidMonthNameMaps()
		);
	}

	/**
	 * @dataProvider provideUnlocalizations
	 */
	public function testUnlocalize( $string, $expected ) {
		$unlocalizer = $this->getUnlocalizer();
		$this->assertEquals( $expected, $unlocalizer->unlocalize( $string ) );
	}

	public function provideUnlocalizations() {
		return array(
			array( 'FOOBAR', 'FOOBAR' ),
			array( 'Januar', 'January' ),
			array( 'Marz', 'March' ),
			array( '12month12', 'December' ),
			array( '11month11', 'November' ),
			array( 'Jan', 'January' ),
			array( 'Mar', 'March' ),
			array( 'Jun', 'June' ),
		);
	}

	/**
	 * @dataProvider provideInvalidConstructions
	 */
	public function testBadConstruction( $targetMonthNames, $monthNameMaps, $message ) {
		$this->setExpectedException( 'InvalidArgumentException', $message );
		new MonthNameUnlocalizer( $targetMonthNames, $monthNameMaps );
	}

	public function provideInvalidConstructions() {
		return array(
			array( array(), $this->getValidMonthNameMaps(), '$targetMonthNames must have 12 elements' ),
			array( array(
				1 => 'January',
				2 => 'February',
				3 => 'March',
				4 => 'April',
				5 => 'May',
				6 => 'June',
				7 => 'July',
				8 => 'August',
				9 => 'September',
				10 => 'October',
				11 => 'November',
				12 => array(),
			), $this->getValidMonthNameMaps(), '$targetMonthNames must have string elements' ),
			array( array(
				0 => 'January',
				2 => 'February',
				3 => 'March',
				4 => 'April',
				5 => 'May',
				6 => 'June',
				7 => 'July',
				8 => 'August',
				9 => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December',
			), $this->getValidMonthNameMaps(), '$targetMonthNames must have keys between 1 and 12, got 0 as a key' ),
			array( $this->getValidTargetMonthNames(), array( 'Foobar' ), '$monthNameMaps must be an array of arrays' ),
			array( $this->getValidTargetMonthNames(), array( array( 'foo' => 'value' ) ), 'Each month name map must have keys between 1 and 12, got foo as a key' ),
			array( $this->getValidTargetMonthNames(), array( array( 1 => array() ) ), 'Each month name map must have string elements' ),
		);
	}

} 