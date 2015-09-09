<?php

namespace ValueParsers\Test;

use ValueParsers\EraParser;

/**
 * @covers ValueParsers\EraParser
 *
 * @group DataValue
 * @group DataValueExtensions
 * @group TimeParsers
 * @group ValueParsers
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class EraParserTest extends StringValueParserTest {

	/**
	 * @deprecated since 0.3, just use getInstance.
	 */
	protected function getParserClass() {
		throw new \LogicException( 'Should not be called, use getInstance' );
	}

	/**
	 * @see ValueParserTestBase::getInstance
	 *
	 * @return EraParser
	 */
	protected function getInstance() {
		return new EraParser();
	}

	/**
	 * @see ValueParserTestBase::requireDataValue
	 *
	 * @return bool
	 */
	protected function requireDataValue() {
		return false;
	}

	/**
	 * @see ValueParserTestBase::validInputProvider
	 */
	public function validInputProvider() {
		return array(
			// Strings with no explicit era should be echoed
			array( '1', array( '+', '1' ) ),
			array( '1 000 000', array( '+', '1 000 000' ) ),
			array( 'non-matching string', array( '+', 'non-matching string' ) ),

			// Strings with an era that should be split of
			array( '+100', array( '+', '100' ) ),
			array( '-100', array( '-', '100' ) ),
			array( '   -100', array( '-', '100' ) ),
			array( '100BC', array( '-', '100' ) ),
			array( '100 BC', array( '-', '100' ) ),
			array( '100 BCE', array( '-', '100' ) ),
			array( '100 AD', array( '+', '100' ) ),
			array( '100 A. D.', array( '+', '100' ) ),
			array( '   100   B.   C.   ', array( '-', '100' ) ),
			array( '   100   Common   Era   ', array( '+', '100' ) ),
			array( '100 CE', array( '+', '100' ) ),
			array( '100CE', array( '+', '100' ) ),
			array( '+100', array( '+', '100' ) ),
			array( '100 Common Era', array( '+', '100' ) ),
			array( '100 Current Era', array( '+', '100' ) ),
			array( '100 Christian Era', array( '+', '100' ) ),
			array( '100Common Era', array( '+', '100' ) ),
			array( '100 Before Common Era', array( '-', '100' ) ),
			array( '100 Before Current Era', array( '-', '100' ) ),
			array( '100 Before Christian Era', array( '-', '100' ) ),
			array( '1 July 2013 Before Common Era', array( '-', '1 July 2013' ) ),
			array( 'June 2013 Before Common Era', array( '-', 'June 2013' ) ),
			array( '10-10-10 Before Common Era', array( '-', '10-10-10' ) ),
			array( 'FooBefore Common Era', array( '-', 'Foo' ) ),
			array( 'Foo Before Common Era', array( '-', 'Foo' ) ),
			array( '-1 000 000', array( '-', '1 000 000' ) ),
			array( '1 000 000 B.C.', array( '-', '1 000 000' ) ),
		);
	}

	/**
	 * @see StringValueParserTest::invalidInputProvider
	 */
	public function invalidInputProvider() {
		return array(
			// Reject strings with two eras, no matter if conflicting or not
			array( '-100BC' ),
			array( '-100AD' ),
			array( '-100CE' ),
			array( '+100BC' ),
			array( '+100AD' ),
			array( '+100CE' ),
			array( '+100 Before Common Era' ),
			array( '+100 Common Era' ),
		);
	}

}
