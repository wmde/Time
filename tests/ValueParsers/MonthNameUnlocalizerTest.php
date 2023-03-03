<?php

namespace ValueParsers\Test;

use PHPUnit\Framework\TestCase;
use ValueParsers\MonthNameUnlocalizer;

/**
 * @covers ValueParsers\MonthNameUnlocalizer
 *
 * @group DataValue
 * @group DataValueExtensions
 * @group ValueParsers
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class MonthNameUnlocalizerTest extends TestCase {

	/**
	 * @dataProvider localizedDateProvider
	 */
	public function testUnlocalize(
		$date,
		$expected,
		array $replacements
	) {
		$unlocalizer = new MonthNameUnlocalizer( $replacements );

		$this->assertEquals( $expected, $unlocalizer->unlocalize( $date ) );
	}

	public function localizedDateProvider() {
		return array(
			// No replacements given
			array( '', '', array() ),
			array( 'Jul', 'Jul', array() ),

			// Longer strings do have higher priority
			array( 'Juli', 'July', array(
				'Jul' => 'bad',
				'Juli' => 'July',
			) ),
			array( 'Juli', 'July', array(
				'Juli' => 'July',
				'Jul' => 'bad',
			) ),

			// Do not mess with strings that are clearly not a valid date.
			array( 'July July', 'July July', array(
				'July' => 'bad',
			) ),

			// Do not mess with already unlocalized month names.
			array( 'July', 'July', array(
				'Jul' => 'July',
			) ),

			// But shortening is ok even if a substring looks like it's already unlocalized.
			array( 'July', 'Jul', array(
				'July' => 'Jul',
			) ),

			// Word boundaries currently do not prevent unlocalization on purpose.
			array( '1Jul2015', '1July2015', array(
				'Jul' => 'July',
			) ),
			array( '1stJulLastYear', '1stJulyLastYear', array(
				'Jul' => 'July',
			) ),

			// Capitalization is currently significant. This may need to depend on the languages.
			array( 'jul', 'jul', array(
				'Jul' => 'bad',
			) ),

			// Some translations (e.g. ko) just repeat the number of the month
			array( '2000', '2000', array(
				'2' => 'February',
			) ),
			// And others (e.g. cs) just repeat the number of the month with punctuation
			array( '5. 4. 1891', '5. 4. 1891', array(
				'5.' => 'May',
			) ),
		);
	}

}
