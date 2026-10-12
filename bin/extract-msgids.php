<?php
/**
 * Extract unique msgids from UpdraftCentral POT to JSON for translation tooling.
 *
 * Usage: php bin/extract-msgids.php
 */

$root = dirname( __DIR__ );
$pot  = 'D:/laragon/www/woodmart/wp-content/plugins/updraftcentral/languages/updraftcentral.pot';
$out  = $root . '/bin/msgids.json';

if ( ! file_exists( $pot ) ) {
	fwrite( STDERR, "POT missing: $pot\n" );
	exit( 1 );
}

$content = str_replace( array( "\r\n", "\r" ), "\n", file_get_contents( $pot ) );
$blocks  = preg_split( "/\n\n+/", $content );
$items   = array();

foreach ( $blocks as $block ) {
	$block = trim( $block );
	if ( '' === $block || ! preg_match( '/^msgid\s+/m', $block ) ) {
		continue;
	}

	$msgctxt      = null;
	$msgid        = null;
	$msgid_plural = null;
	$state        = null;

	foreach ( explode( "\n", $block ) as $line ) {
		if ( '' === $line || '#' === $line[0] ) {
			continue;
		}
		if ( preg_match( '/^msgctxt\s+"(.*)"\s*$/', $line, $m ) ) {
			$state   = 'msgctxt';
			$msgctxt = stripcslashes( $m[1] );
			continue;
		}
		if ( preg_match( '/^msgid\s+"(.*)"\s*$/', $line, $m ) ) {
			$state = 'msgid';
			$msgid = stripcslashes( $m[1] );
			continue;
		}
		if ( preg_match( '/^msgid_plural\s+"(.*)"\s*$/', $line, $m ) ) {
			$state        = 'msgid_plural';
			$msgid_plural = stripcslashes( $m[1] );
			continue;
		}
		if ( preg_match( '/^msgstr/', $line ) ) {
			$state = null;
			continue;
		}
		if ( preg_match( '/^"(.*)"\s*$/', $line, $m ) ) {
			$chunk = stripcslashes( $m[1] );
			if ( 'msgctxt' === $state ) {
				$msgctxt .= $chunk;
			} elseif ( 'msgid' === $state ) {
				$msgid .= $chunk;
			} elseif ( 'msgid_plural' === $state ) {
				$msgid_plural .= $chunk;
			}
		}
	}

	if ( null === $msgid || '' === $msgid ) {
		continue;
	}

	$items[] = array(
		'msgctxt'      => $msgctxt,
		'msgid'        => $msgid,
		'msgid_plural' => $msgid_plural,
	);
}

file_put_contents( $out, json_encode( $items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
echo 'Extracted ' . count( $items ) . " msgids → $out\n";
