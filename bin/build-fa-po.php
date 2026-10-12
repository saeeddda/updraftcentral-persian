<?php
/**
 * Build Persian PO from POT using:
 * 1) Hand-crafted dictionary (bin/dict-fa.php)
 * 2) Cached API translations (bin/cache-fa.json)
 * 3) Optional live MyMemory lookups for remaining strings
 *
 * Usage:
 *   php bin/build-fa-po.php
 *   php bin/build-fa-po.php --fetch   # also call MyMemory for missing keys
 */

$root   = dirname( __DIR__ );
$pot    = 'D:/laragon/www/woodmart/wp-content/plugins/updraftcentral/languages/updraftcentral.pot';
$po_out = $root . '/languages/updraftcentral-fa_IR.po';
$cache  = $root . '/bin/cache-fa.json';
$dict_f = $root . '/bin/dict-fa.php';
$fetch  = in_array( '--fetch', $argv, true );

if ( ! file_exists( $pot ) ) {
	fwrite( STDERR, "POT missing\n" );
	exit( 1 );
}

$dict = file_exists( $dict_f ) ? include $dict_f : array();
if ( ! is_array( $dict ) ) {
	$dict = array();
}

$cached = file_exists( $cache ) ? json_decode( file_get_contents( $cache ), true ) : array();
if ( ! is_array( $cached ) ) {
	$cached = array();
}

$content = str_replace( array( "\r\n", "\r" ), "\n", file_get_contents( $pot ) );
$blocks  = preg_split( "/\n\n+/", $content );

$header = <<<'HDR'
# Persian (fa_IR) translation for UpdraftCentral
# Copyright (C) 2026 UpdraftCentral Persian
# This file is distributed under the same license as the UpdraftCentral package.
msgid ""
msgstr ""
"Project-Id-Version: UpdraftCentral Persian 1.0.0\n"
"Report-Msgid-Bugs-To: https://github.com/saeednoori/updraftcentral-persian\n"
"POT-Creation-Date: 2026-06-05 06:47+0000\n"
"PO-Revision-Date: 2026-10-12 12:00+0330\n"
"Last-Translator: UpdraftCentral Persian\n"
"Language-Team: Persian\n"
"Language: fa_IR\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n == 0 || n == 1);\n"
"X-Generator: updraftcentral-persian bin/build-fa-po.php\n"

HDR;

$out          = $header . "\n";
$translated   = 0;
$missing      = 0;
$missing_list = array();

foreach ( $blocks as $block ) {
	$block = trim( $block );
	if ( '' === $block ) {
		continue;
	}

	// Skip POT header block.
	if ( preg_match( '/^msgid\s+""\s*$/m', $block ) && preg_match( '/^msgstr\s+""/m', $block ) && ! preg_match( '/^msgid_plural/m', $block ) ) {
		// Heuristic: header has Project-Id-Version inside msgstr continuations.
		if ( false !== strpos( $block, 'Project-Id-Version' ) || false !== strpos( $block, 'Content-Type' ) ) {
			continue;
		}
	}

	if ( ! preg_match( '/^msgid\s+/m', $block ) ) {
		continue;
	}

	$comments     = array();
	$msgctxt      = null;
	$msgid        = null;
	$msgid_plural = null;
	$state        = null;

	foreach ( explode( "\n", $block ) as $line ) {
		if ( isset( $line[0] ) && '#' === $line[0] ) {
			$comments[] = $line;
			continue;
		}
		if ( '' === $line ) {
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

	if ( null === $msgid ) {
		continue;
	}
	// Empty msgid without plural = leftover header-like.
	if ( '' === $msgid && null === $msgid_plural ) {
		continue;
	}

	$key = $msgid;
	$fa  = null;

	if ( isset( $dict[ $key ] ) && '' !== $dict[ $key ] ) {
		$fa = $dict[ $key ];
	} elseif ( isset( $cached[ $key ] ) && '' !== $cached[ $key ] ) {
		$fa = $cached[ $key ];
	} elseif ( $fetch && '' !== $msgid ) {
		$fa = ucp_mymemory_translate( $msgid );
		if ( $fa ) {
			$cached[ $key ] = $fa;
		}
		usleep( 120000 );
	}

	if ( ! $fa ) {
		// Fallback: keep English so UI still works; mark for later.
		$fa = $msgid;
		$missing++;
		$missing_list[] = $msgid;
	} else {
		$translated++;
	}

	if ( $comments ) {
		$out .= implode( "\n", $comments ) . "\n";
	}
	if ( null !== $msgctxt ) {
		$out .= 'msgctxt ' . ucp_po_quote( $msgctxt ) . "\n";
	}
	$out .= 'msgid ' . ucp_po_quote( $msgid ) . "\n";

	if ( null !== $msgid_plural ) {
		$out .= 'msgid_plural ' . ucp_po_quote( $msgid_plural ) . "\n";
		$fa_plural = $dict[ $msgid_plural ] ?? $cached[ $msgid_plural ] ?? $msgid_plural;
		$out      .= 'msgstr[0] ' . ucp_po_quote( $fa ) . "\n";
		$out      .= 'msgstr[1] ' . ucp_po_quote( $fa_plural ) . "\n\n";
	} else {
		$out .= 'msgstr ' . ucp_po_quote( $fa ) . "\n\n";
	}
}

file_put_contents( $po_out, $out );
file_put_contents( $cache, json_encode( $cached, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );

echo "Wrote $po_out\n";
echo "Translated (dict/cache/api): $translated\n";
echo "Fallback English: $missing\n";

if ( $missing && $missing < 40 ) {
	echo "Missing samples:\n";
	foreach ( array_slice( $missing_list, 0, 20 ) as $m ) {
		echo ' - ' . substr( $m, 0, 100 ) . "\n";
	}
}

/**
 * @param string $s String.
 * @return string
 */
function ucp_po_quote( $s ) {
	$s = str_replace( array( '\\', '"', "\n", "\t", "\r" ), array( '\\\\', '\\"', "\\n\",\n\"", '\\t', '' ), $s );
	// If multiline was introduced, wrap properly.
	if ( false !== strpos( $s, "\",\n\"" ) ) {
		return "\"\"\n\"" . $s . '"';
	}
	return '"' . $s . '"';
}

/**
 * @param string $text English.
 * @return string|null Persian or null.
 */
function ucp_mymemory_translate( $text ) {
	if ( strlen( $text ) > 450 ) {
		// Split long strings roughly by sentence.
		$parts = preg_split( '/(?<=[.!?])\s+/', $text );
		$out   = array();
		foreach ( $parts as $part ) {
			$t = ucp_mymemory_translate( $part );
			$out[] = $t ? $t : $part;
			usleep( 100000 );
		}
		return implode( ' ', $out );
	}

	$url  = 'https://api.mymemory.translated.net/get?q=' . rawurlencode( $text ) . '&langpair=en|fa';
	$json = @file_get_contents( $url );
	if ( ! $json ) {
		return null;
	}
	$data = json_decode( $json, true );
	$fa   = $data['responseData']['translatedText'] ?? null;
	if ( ! $fa || false !== stripos( $fa, 'MYMEMORY WARNING' ) ) {
		return null;
	}
	return html_entity_decode( $fa, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}
