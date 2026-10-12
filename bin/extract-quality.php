<?php
$src = __DIR__ . '/dict-fa.php';
$d   = include $src;
$q   = array();
$allow = array( 'UpdraftCentral', 'UpdraftPlus', 'UpdraftVault', 'WordPress', 'WP-Optimize', 'PHP', 'MySQL', 'HTTP', 'CORS', 'SSL', 'API', 'JSON', 'RPC', 'UDRPC', 'InnoDB', 'jQuery', 'HTML', 'CSS', 'Dropbox', 'Google', 'Amazon', 'OneDrive', 'Azure', 'WebDAV', 'SFTP', 'Premium', 'Digest', 'Basic', 'Dashboard', 'Updates', 'Manager', 'Easy', 'FAQs', 'FAQ' );

foreach ( $d as $k => $v ) {
	if ( ! is_string( $v ) || $v === $k ) {
		continue;
	}
	if ( ! preg_match( '/[\x{0600}-\x{06FF}]/u', $v ) ) {
		continue;
	}
	preg_match_all( '/\b[A-Za-z]{4,}\b/', $v, $m );
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
	if ( $bad <= 1 ) {
		$q[ $k ] = $v;
	}
}

$export = var_export( $q, true );
file_put_contents( __DIR__ . '/dict-fa-quality.php', "<?php\n/** Quality seed extracted from current dict. */\n\nreturn " . $export . ";\n" );
echo 'quality=' . count( $q ) . ' total=' . count( $d ) . "\n";
