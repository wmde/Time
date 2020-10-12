<?php

namespace ValueParsers;

/**
 * A monolingual month name and month number provider, based on a static array.
 *
 * @since 0.8.4
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
class MonolingualMonthNameProvider implements MonthNameProvider {

	/**
	 * @var string[]
	 */
	private $monthNames;

	/**
	 * @param string[] $monthNames Array mapping month numbers (1 to 12) to localized month names.
	 */
	public function __construct( array $monthNames ) {
		$this->monthNames = $monthNames;
	}

	/**
	 * @param string $languageCode Ignored in this implementation.
	 *
	 * @return string[] Array mapping month numbers (1 to 12) to localized month names.
	 */
	public function getLocalizedMonthNames( $languageCode ) {
		return $this->monthNames;
	}

	/**
	 * @param string $languageCode Ignored in this implementation.
	 *
	 * @return int[] Array mapping localized month names to month numbers (1 to 12).
	 */
	public function getMonthNumbers( $languageCode ) {
		return array_flip( $this->monthNames );
	}

}
