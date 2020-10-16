<?php

namespace ValueParsers;

/**
 * A rudimentary ValueParser capable of extracting era information from any date and time string. It
 * checks for signs as in "-150,000" as well as suffixes as in "150,000 BC".
 *
 * @since 0.8
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 * @author Thiemo Kreuz
 */
class EraParser extends StringValueParser {

	private const FORMAT_NAME = 'era';

	/**
	 * @since 0.8
	 */
	public const BEFORE_COMMON_ERA = '-';

	/**
	 * @since 0.8
	 */
	public const COMMON_ERA = '+';

	/**
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return string[] Array of the parsed era constant and the value with the era stripped.
	 */
	protected function stringParse( $value ) {
		$value = trim( $value );

		$eraFromSign = $this->parseEraFromSign( $value );
		$eraFromSuffix = $this->parseEraFromSuffix( $value );

		if ( $eraFromSign && $eraFromSuffix ) {
			throw new ParseException(
				'Parsed two eras from the same string',
				$value,
				self::FORMAT_NAME
			);
		}

		// Default to CE
		return $eraFromSign ?: $eraFromSuffix ?: array( self::COMMON_ERA, $value );
	}

	/**
	 * @param string $value
	 *
	 * @return string[]|null Array of the era constant and the value with the era stripped, or null
	 * if not successful.
	 */
	private function parseEraFromSign( $value ) {
		$sign = substr( $value, 0, 1 );

		if ( $sign === self::BEFORE_COMMON_ERA || $sign === self::COMMON_ERA ) {
			return array(
				$sign,
				substr( $value, 1 )
			);
		}

		return null;
	}

	/**
	 * @param string $value
	 *
	 * @return string[]|null Array of the era constant and the value with the era stripped, or null
	 * if not successful.
	 */
	private function parseEraFromSuffix( $value ) {
		if ( preg_match(
			'/(?:B\.?\s*C\.?(?:\s*E\.?)?|Before\s+C(?:hrist|(?:ommon|urrent|hristian)\s+Era))$/i',
			$value,
			$matches,
			PREG_OFFSET_CAPTURE )
		) {
			return array(
				self::BEFORE_COMMON_ERA,
				rtrim( substr( $value, 0, $matches[0][1] ) )
			);
		} elseif ( preg_match(
			'/(?:C\.?\s*E\.?|A\.?\s*D\.?|C(?:ommon|urrent|hristian)\s+Era|After\s+Christ|Anno\s+Domini)$/i',
			$value,
			$matches,
			PREG_OFFSET_CAPTURE
		) ) {
			return array(
				self::COMMON_ERA,
				rtrim( substr( $value, 0, $matches[0][1] ) )
			);
		}

		return null;
	}

}
