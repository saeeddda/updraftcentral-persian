<?php
/**
 * List translations that are still mostly English or identical to source.
 */
$d = include __DIR__ . '/dict-fa.php';
$weak = array();
$allow = array(
	'UpdraftCentral', 'UpdraftPlus', 'UpdraftVault', 'WordPress', 'WP-Optimize',
	'PHP', 'MySQL', 'HTTP', 'CORS', 'SSL', 'API', 'JSON', 'RPC', 'UDRPC', 'InnoDB',
	'jQuery', 'HTML', 'CSS', 'Dropbox', 'Google', 'Amazon', 'OneDrive', 'Azure',
	'WebDAV', 'SFTP', 'Premium', 'Digest', 'Basic', 'SaaS', 'IAM', 'S3', 'FTP',
	'Rackspace', 'OK', 'URL', 'KPIs', 'Analytics',
);

foreach ( $d as $en => $fa ) {
	if ( ! is_string( $fa ) ) {
		continue;
	}
	if ( $fa === $en ) {
		// Pure brand OK.
		if ( preg_match( '/^(UpdraftCentral|UpdraftPlus|UpdraftVault|WP-Optimize|WordPress)$/i', $en ) ) {
			continue;
		}
		$weak[ $en ] = $fa;
		continue;
	}
	if ( ! preg_match( '/[\x{0600}-\x{06FF}]/u', $fa ) ) {
		$weak[ $en ] = $fa;
		continue;
	}
	preg_match_all( '/\b[A-Za-z]{4,}\b/', $fa, $m );
	$bad = 0;
	foreach ( $m[0] as $w ) {
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
	// Many leftover English words = weak machine mix.
	if ( $bad >= 3 ) {
		$weak[ $en ] = $fa;
	}
}

file_put_contents( __DIR__ . '/weak-en.json', json_encode( $weak, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
echo 'weak=' . count( $weak ) . " → bin/weak-en.json\n";
foreach ( array_slice( $weak, 0, 40, true ) as $k => $v ) {
	echo substr( $k, 0, 90 ) . "\n  => " . substr( $v, 0, 90 ) . "\n";
}
