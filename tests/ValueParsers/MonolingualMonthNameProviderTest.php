<?php

namespace ValueParsers\Test;

use PHPUnit\Framework\TestCase;
use ValueParsers\MonolingualMonthNameProvider;

/**
 * @covers ValueParsers\MonolingualMonthNameProvider
 *
 * @group DataValue
 * @group DataValueExtensions
 * @group ValueParsers
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class MonolingualMonthNameProviderTest extends TestCase {

	public function testGetLocalizedMonthNames() {
		$instance = new MonolingualMonthNameProvider( array( 1 => 'January' ) );
		$this->assertSame( array( 1 => 'January' ), $instance->getLocalizedMonthNames( 'xx' ) );
	}

	public function testGetMonthNumbers() {
		$instance = new MonolingualMonthNameProvider( array( 1 => 'January' ) );
		$this->assertSame( array( 'January' => 1 ), $instance->getMonthNumbers( 'xx' ) );
	}

}
