<?php

namespace ValueParsers;

/**
 * A common interface for both localizing month names in formatters as well as unlocalizing month
 * names in parsers.
 *
 * @since 0.8.4
 *
 * @license GPL-2.0-or-later
 * @author Thiemo Kreuz
 */
interface MonthNameProvider {

	/**
	 * @param string $languageCode
	 *
	 * @return string[] Array mapping month numbers (1 to 12) to localized month names.
	 */
	public function getLocalizedMonthNames( $languageCode );

	/**
	 * @param string $languageCode
	 *
	 * @return int[] Array mapping localized month names (possibly including full month names,
	 * genitive names and abbreviations) to month numbers (1 to 12).
	 */
	public function getMonthNumbers( $languageCode );

}
