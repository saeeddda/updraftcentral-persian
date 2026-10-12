<?php
/**
 * Generate Persian dictionary for all POT msgids via MyMemory + quality seed.
 *
 * Usage: php bin/generate-dict-from-pot.php
 *        php bin/generate-dict-from-pot.php --force   # re-fetch low-quality entries
 */

$root    = dirname( __DIR__ );
$pot     = 'D:/laragon/www/woodmart/wp-content/plugins/updraftcentral/languages/updraftcentral.pot';
$dict_f  = $root . '/bin/dict-fa.php';
$seed_f  = $root . '/bin/dict-fa-quality.php';
$cache_f = $root . '/bin/cache-fa.json';
$force   = in_array( '--force', $argv, true );

$seed = file_exists( $seed_f ) ? include $seed_f : array();
if ( ! is_array( $seed ) ) {
	$seed = array();
}

$existing = file_exists( $dict_f ) ? include $dict_f : array();
if ( ! is_array( $existing ) ) {
	$existing = array();
}

$cache = file_exists( $cache_f ) ? json_decode( file_get_contents( $cache_f ), true ) : array();
if ( ! is_array( $cache ) ) {
	$cache = array();
}

$content = str_replace( array( "\r\n", "\r" ), "\n", file_get_contents( $pot ) );
$blocks  = preg_split( "/\n\n+/", $content );
$msgids  = array();

foreach ( $blocks as $block ) {
	$msgid = null;
	$state = null;
	foreach ( explode( "\n", trim( $block ) ) as $line ) {
		if ( '' === $line || ( isset( $line[0] ) && '#' === $line[0] ) ) {
			continue;
		}
		if ( preg_match( '/^msgid\s+"(.*)"\s*$/', $line, $m ) ) {
			$state = 'msgid';
			$msgid = stripcslashes( $m[1] );
			continue;
		}
		if ( preg_match( '/^msgid_plural\s+"(.*)"\s*$/', $line, $m ) ) {
			$plural = stripcslashes( $m[1] );
			if ( $plural ) {
				$msgids[ $plural ] = true;
			}
			continue;
		}
		if ( preg_match( '/^msgstr/', $line ) ) {
			$state = null;
			continue;
		}
		if ( 'msgid' === $state && preg_match( '/^"(.*)"\s*$/', $line, $m ) ) {
			$msgid .= stripcslashes( $m[1] );
		}
	}
	if ( null !== $msgid && '' !== $msgid ) {
		$msgids[ $msgid ] = true;
	}
}

$keys   = array_keys( $msgids );
$merged = array();
$api    = 0;
$kept   = 0;
$total  = count( $keys );

echo "Total unique msgids: $total\n";

foreach ( $keys as $i => $en ) {
	$candidate = null;

	if ( isset( $seed[ $en ] ) && is_good_fa( $seed[ $en ], $en ) ) {
		$candidate = $seed[ $en ];
	} elseif ( isset( $cache[ $en ] ) && is_good_fa( $cache[ $en ], $en ) ) {
		$candidate = $cache[ $en ];
	} elseif ( ! $force && isset( $existing[ $en ] ) && is_good_fa( $existing[ $en ], $en ) ) {
		$candidate = $existing[ $en ];
	}

	if ( null === $candidate ) {
		$fa = mymemory( $en );
		if ( $fa && is_good_fa( $fa, $en ) ) {
			$cache[ $en ] = $fa;
			$candidate    = $fa;
			$api++;
			if ( 0 === $api % 15 ) {
				file_put_contents( $cache_f, json_encode( $cache, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
				echo '[' . ( $i + 1 ) . "/$total] API=$api kept=$kept\n";
			}
		} else {
			// Last resort: keep previous or English.
			$candidate = $seed[ $en ] ?? $cache[ $en ] ?? $existing[ $en ] ?? $en;
		}
		usleep( 140000 );
	} else {
		$kept++;
	}

	$merged[ $en ] = $candidate;
}

file_put_contents( $cache_f, json_encode( $cache, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
write_dict( $dict_f, $merged );

$good = 0;
foreach ( $merged as $en => $fa ) {
	if ( is_good_fa( $fa, $en ) ) {
		$good++;
	}
}

echo "Dict entries: " . count( $merged ) . " | good≈$good | API new=$api | kept=$kept\n";
echo "Wrote $dict_f\n";

/**
 * Accept translation if it looks meaningfully Persian (or intentionally English brand-only).
 *
 * @param string $fa Persian candidate.
 * @param string $en English source.
 * @return bool
 */
function is_good_fa( $fa, $en ) {
	if ( ! is_string( $fa ) || '' === trim( $fa ) ) {
		return false;
	}
	if ( $fa === $en ) {
		// Allow pure brand/tech tokens to stay English.
		return (bool) preg_match( '/^(UpdraftCentral|UpdraftPlus|UpdraftVault|WP-Optimize|WordPress|PHP|MySQL|HTTP|CORS|SSL|API|OK|JSON|RPC|UDRPC|InnoDB|jQuery|CSS|HTML|URL|FTP|S3|WebDAV|Google|Amazon|Dropbox|OneDrive|Azure|SFTP|SSH)$/i', $en );
	}
	// Must contain Persian letters.
	if ( ! preg_match( '/[\x{0600}-\x{06FF}]/u', $fa ) ) {
		return false;
	}
	// Reject mixed garbage: too many leftover English words (4+ letter latin tokens),
	// unless source was short.
	preg_match_all( '/\b[A-Za-z]{4,}\b/', $fa, $m );
	$latin_words = $m[0];
	// Allow known brands inside Persian sentences.
	$allow = array( 'UpdraftCentral', 'UpdraftPlus', 'UpdraftVault', 'WordPress', 'WP-Optimize', 'Easy', 'Updates', 'Manager', 'PHP', 'MySQL', 'HTTP', 'CORS', 'SSL', 'API', 'JSON', 'RPC', 'UDRPC', 'InnoDB', 'jQuery', 'CSS', 'HTML', 'Dropbox', 'Google', 'Amazon', 'OneDrive', 'Azure', 'WebDAV', 'SFTP', 'Dashboard', 'Premium' );
	$bad   = 0;
	foreach ( $latin_words as $w ) {
		$ok = false;
		foreach ( $allow as $a ) {
			if ( strcasecmp( $w, $a ) === 0 ) {
				$ok = true;
				break;
			}
		}
		if ( ! $ok ) {
			$bad++;
		}
	}
	// If more than 2 unexplained English words remain, treat as low quality.
	return $bad <= 2;
}

/**
 * @param string $text Text.
 * @return string|null
 */
function mymemory( $text ) {
	if ( strlen( $text ) > 480 ) {
		$parts = preg_split( '/(?<=[.!?])\s+/', $text );
		$out   = array();
		foreach ( $parts as $part ) {
			$t     = mymemory( $part );
			$out[] = $t ? $t : $part;
			usleep( 120000 );
		}
		return implode( ' ', $out );
	}

	$url  = 'https://api.mymemory.translated.net/get?q=' . rawurlencode( $text ) . '&langpair=en|fa';
	$ctx  = stream_context_create(
		array(
			'http' => array(
				'timeout' => 25,
				'header'  => "User-Agent: UpdraftCentral-Persian/1.0\r\n",
			),
		)
	);
	$json = @file_get_contents( $url, false, $ctx );
	if ( ! $json ) {
		return null;
	}
	$data = json_decode( $json, true );
	$fa   = $data['responseData']['translatedText'] ?? null;
	if ( ! is_string( $fa ) || '' === $fa || false !== stripos( $fa, 'MYMEMORY WARNING' ) || false !== stripos( $fa, 'INVALID' ) ) {
		return null;
	}
	return html_entity_decode( $fa, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * @param string               $path Path.
 * @param array<string,string> $dict Dict.
 */
function write_dict( $path, array $dict ) {
	ksort( $dict, SORT_STRING );
	$export = var_export( $dict, true );
	$export = preg_replace( '/^array \(/', '[', $export );
	$export = preg_replace( '/\)$/', ']', $export );
	$code   = "<?php\n/** Auto-generated Persian dictionary for UpdraftCentral. */\n\nreturn " . $export . ";\n";
	file_put_contents( $path, $code );
}
