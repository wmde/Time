<?php

namespace ValueParsers;

use InvalidArgumentException;

/**
 * Class to unlocalize month names using Mediawiki's Language object
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MonthNameUnlocalizer {

	/**
	 * @var string[]
	 */
	private $targetMonthNames;
	/**
	 * @var array[]
	 */
	private $monthNameMaps;

	/**
	 * @param string[] $targetMonthNames list of month names with keys 1 to 12 representing months
	 * @param array[] $monthNameMaps array of lists of month names each with keys 1 to 12 representing months
	 *                these lists will be processes in order, of list keys.
	 *                I.e. All month names in the first array will be checked first so full names should come first
	 *                     and abbreviations should come last
	 */
	public function __construct( array $targetMonthNames, array $monthNameMaps ) {
		$this->throwExceptionsOnBadTargetMonthNames( $targetMonthNames );
		$this->throwExceptionsOnBadMonthNameMaps( $monthNameMaps );
		$this->targetMonthNames = $targetMonthNames;
		$this->monthNameMaps = $monthNameMaps;
	}

	/**
	 * @param array $targetMonthNames
	 *
	 * @throws InvalidArgumentException
	 */
	private function throwExceptionsOnBadTargetMonthNames( $targetMonthNames ) {
		if( count( $targetMonthNames ) !== 12 ) {
			throw new InvalidArgumentException( '$targetMonthNames must have 12 elements' );
		}
		foreach( $targetMonthNames as $key => $value ) {
			if( $key > 12 || $key < 1 ) {
				throw new InvalidArgumentException( '$targetMonthNames must have keys between 1 and 12, got ' . strval( $key ) . ' as a key' );
			}
			if( !is_string( $value ) ) {
				throw new InvalidArgumentException( '$targetMonthNames must have string elements' );
			}
		}
	}

	/**
	 * @param array[] $monthNameMaps
	 *
	 * @throws InvalidArgumentException
	 */
	private function throwExceptionsOnBadMonthNameMaps( $monthNameMaps ) {
		foreach( $monthNameMaps as $map ) {
			if( !is_array( $map ) ) {
				throw new InvalidArgumentException( '$monthNameMaps must be an array of arrays' );
			}
			foreach( $map as $key => $mapElement ) {
				if( $key > 12 || $key < 1 ) {
					throw new InvalidArgumentException( 'Each month name map must have keys between 1 and 12, got ' . strval( $key ) . ' as a key' );
				}
				if( !is_string( $mapElement ) ) {
					throw new InvalidArgumentException( 'Each month name map must have string elements' );
				}
			}
		}
	}

	/**
	 * @param string $string string to process
	 *
	 * @return string unlocalized string
	 */
	public function unlocalize( $string ) {
		$initialString = $string;

		foreach( $this->monthNameMaps as $monthNameMap ) {
			foreach ( $monthNameMap as $monthInt => $monthName ) {
				$string = str_replace( $monthName, $this->targetMonthNames[$monthInt], $string );
				if( $string !== $initialString ) {
					return $string;
				}
			}
		}

		return $string;
	}

} 