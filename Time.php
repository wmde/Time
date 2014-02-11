<?php

/**
 * Entry point of the DataValues Time library.
 *
 * @since 0.1
 * @codeCoverageIgnore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( defined( 'DATAVALUES_TIME_VERSION' ) ) {
	// Do not initialize more then once.
	return 1;
}

define( 'DATAVALUES_TIME_VERSION', '0.2' );

if ( defined( 'MEDIAWIKI' ) ) {
	$GLOBALS['wgExtensionCredits']['datavalues'][] = array(
		'path' => __DIR__,
		'name' => 'DataValues Time',
		'version' => DATAVALUES_TIME_VERSION,
		'author' => array(
			'The Wikidata team',
		),
		'url' => 'https://github.com/DataValues/Time',
		'description' => 'Time value objects, parsers and formatters',
	);
}
