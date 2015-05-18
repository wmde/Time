<?php

namespace ValueParsers;

/**
 * @since 0.8
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class EraParser extends StringValueParser {

	const FORMAT_NAME = 'era';

	/**
	 * @since 0.8
	 */
	const BEFORE_COMMON_ERA = '-';

	/**
	 * @since 0.8
	 */
	const COMMON_ERA = '+';

	/**
	 * Parses the provided string and returns the era
	 *
	 * @param string $value
	 *
	 * @throws ParseException
	 * @return array( 0 => parsed era constant, 1 => $value with no era data )
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
		return array( $eraFromSign ?: $eraFromSuffix ?: self::COMMON_ERA, $value );
	}

	/**
	 * @param string &$value
	 *
	 * @return string|null
	 */
	private function parseEraFromSign( &$value ) {
		$sign = substr( $value, 0, 1 );

		if ( $sign === self::BEFORE_COMMON_ERA || $sign === self::COMMON_ERA ) {
			$value = substr( $value, 1 );
			return $sign;
		}

		return null;
	}

	/**
	 * @param string &$value
	 *
	 * @return string|null
	 */
	private function parseEraFromSuffix( &$value ) {
		if ( preg_match(
			'/(?:B\.?\s*C\.?(?:\s*E\.?)?|Before\s+C(?:hrist|(?:ommon|urrent|hristian)\s+Era))$/i',
			$value,
			$matches,
			PREG_OFFSET_CAPTURE )
		) {
			$value = rtrim( substr( $value, 0, $matches[0][1] ) );
			return self::BEFORE_COMMON_ERA;
		} elseif ( preg_match(
			'/(?:C\.?\s*E\.?|A\.?\s*D\.?|C(?:ommon|urrent|hristian)\s+Era|After\s+Christ|Anno\s+Domini)$/i',
			$value,
			$matches,
			PREG_OFFSET_CAPTURE
		) ) {
			$value = rtrim( substr( $value, 0, $matches[0][1] ) );
			return self::COMMON_ERA;
		}

		return null;
	}

}
