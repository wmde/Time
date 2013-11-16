<?php

namespace ValueFormatters;

/**
 * Time formatter.
 *
 * Some code in this class has been borrowed from the
 * MapsCoordinateParser class of the Maps extension for MediaWiki.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author H. Snater < mediawiki@snater.com >
 */
interface TimeIsoFormatter {

	/**
	 * Formats a given (extended) ISO timestamp according to a given precision.
	 * @since 0.1
	 *
	 * @param string $timestamp
	 * @param integer $precision
	 * @return string
	 */
	public function formatDate( $timestamp, $precision );
}