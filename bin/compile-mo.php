<?php
/**
 * Compile languages/updraftcentral-fa_IR.po → updraftcentral-fa_IR.mo
 *
 * Usage: php bin/compile-mo.php
 *
 * @package UpdraftCentral_Persian
 */

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "CLI only.\n" );
	exit( 1 );
}

$root = dirname( __DIR__ );
$po   = $root . '/languages/updraftcentral-fa_IR.po';
$mo   = $root . '/languages/updraftcentral-fa_IR.mo';

if ( ! file_exists( $po ) ) {
	fwrite( STDERR, "PO not found: $po\n" );
	exit( 1 );
}

$entries = ucp_parse_po( file_get_contents( $po ) );
if ( empty( $entries ) ) {
	fwrite( STDERR, "No entries parsed from PO.\n" );
	exit( 1 );
}

$binary = ucp_write_mo( $entries );
if ( false === file_put_contents( $mo, $binary ) ) {
	fwrite( STDERR, "Failed writing MO: $mo\n" );
	exit( 1 );
}

$count = count( $entries );
$size  = filesize( $mo );
echo "Compiled $count entries → $mo ($size bytes)\n";
exit( 0 );

/**
 * Parse a PO file into msgid => msgstr map (context via \x04).
 *
 * @param string $content PO contents.
 * @return array<string,string>
 */
function ucp_parse_po( $content ) {
	$content = str_replace( array( "\r\n", "\r" ), "\n", $content );
	$blocks  = preg_split( "/\n\n+/", $content );
	$map     = array();

	foreach ( $blocks as $block ) {
		$block = trim( $block );
		if ( '' === $block ) {
			continue;
		}

		$msgctxt = null;
		$msgid   = null;
		$msgstr  = null;
		$msgid_plural = null;
		$msgstr_plural = array();
		$state   = null;

		$lines = explode( "\n", $block );
		foreach ( $lines as $line ) {
			if ( '' === $line || '#' === $line[0] ) {
				continue;
			}

			if ( preg_match( '/^msgctxt\s+"(.*)"\s*$/s', $line, $m ) ) {
				$state   = 'msgctxt';
				$msgctxt = ucp_po_unquote( $m[1] );
				continue;
			}
			if ( preg_match( '/^msgid\s+"(.*)"\s*$/s', $line, $m ) ) {
				$state = 'msgid';
				$msgid = ucp_po_unquote( $m[1] );
				continue;
			}
			if ( preg_match( '/^msgid_plural\s+"(.*)"\s*$/s', $line, $m ) ) {
				$state        = 'msgid_plural';
				$msgid_plural = ucp_po_unquote( $m[1] );
				continue;
			}
			if ( preg_match( '/^msgstr\s+"(.*)"\s*$/s', $line, $m ) ) {
				$state  = 'msgstr';
				$msgstr = ucp_po_unquote( $m[1] );
				continue;
			}
			if ( preg_match( '/^msgstr\[(\d+)\]\s+"(.*)"\s*$/s', $line, $m ) ) {
				$state                   = 'msgstr_plural';
				$msgstr_plural[ (int) $m[1] ] = ucp_po_unquote( $m[2] );
				continue;
			}
			if ( preg_match( '/^"(.*)"\s*$/s', $line, $m ) ) {
				$chunk = ucp_po_unquote( $m[1] );
				if ( 'msgctxt' === $state ) {
					$msgctxt .= $chunk;
				} elseif ( 'msgid' === $state ) {
					$msgid .= $chunk;
				} elseif ( 'msgid_plural' === $state ) {
					$msgid_plural .= $chunk;
				} elseif ( 'msgstr' === $state ) {
					$msgstr .= $chunk;
				} elseif ( 'msgstr_plural' === $state && ! empty( $msgstr_plural ) ) {
					$last = array_key_last( $msgstr_plural );
					$msgstr_plural[ $last ] .= $chunk;
				}
			}
		}

		if ( null === $msgid ) {
			continue;
		}

		$key = ( null !== $msgctxt && '' !== $msgctxt ) ? $msgctxt . "\x04" . $msgid : $msgid;

		if ( null !== $msgid_plural && ! empty( $msgstr_plural ) ) {
			ksort( $msgstr_plural );
			$map[ $key ] = implode( "\0", $msgstr_plural );
			// Also store plural original for completeness when readers expect it.
			continue;
		}

		if ( null === $msgstr ) {
			continue;
		}

		// Header entry (empty msgid) is required in MO.
		$map[ $key ] = $msgstr;
	}

	return $map;
}

/**
 * Unescape a PO quoted string fragment.
 *
 * @param string $str Fragment.
 * @return string
 */
function ucp_po_unquote( $str ) {
	return stripcslashes( $str );
}

/**
 * Build GNU MO binary (little-endian).
 *
 * @param array<string,string> $entries Entries.
 * @return string
 */
function ucp_write_mo( array $entries ) {
	ksort( $entries, SORT_STRING );

	$ids  = '';
	$strs = '';
	$ids_offsets  = array();
	$strs_offsets = array();

	foreach ( $entries as $id => $str ) {
		$ids_offsets[]  = array( strlen( $ids ), strlen( $id ) );
		$ids           .= $id . "\0";
		$strs_offsets[] = array( strlen( $strs ), strlen( $str ) );
		$strs          .= $str . "\0";
	}

	$count      = count( $entries );
	$header_len = 28;
	$table_len  = $count * 8 * 2;
	$ids_start  = $header_len + $table_len;
	$strs_start = $ids_start + strlen( $ids );

	$out  = pack( 'V*', 0x950412de, 0, $count, $header_len, $header_len + $count * 8, 0, $ids_start );
	// Originals table.
	foreach ( $ids_offsets as $o ) {
		$out .= pack( 'VV', $o[1], $ids_start + $o[0] );
	}
	// Translations table.
	foreach ( $strs_offsets as $o ) {
		$out .= pack( 'VV', $o[1], $strs_start + $o[0] );
	}
	$out .= $ids;
	$out .= $strs;

	return $out;
}
