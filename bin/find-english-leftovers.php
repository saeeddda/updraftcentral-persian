<?php
$d = include __DIR__ . '/dict-fa.php';
$markers = array(
	'please', 'Please', 'from your', 'have yet', 'selected items',
	'updates,', 'compatible items', 'matching items', 'message was',
	'storage method', 'also send', 'web server', 'Storage Region',
	'themes, plugins', 'captures events', 'handy Analytics',
	'Click', 'click', 'Loading', 'Wait', 'wait...', 'Successfully',
	'Failed to', 'Unable to', 'There are no', 'There is no',
	'You can', 'You have', 'You must', 'You need', 'You should',
	'Are you sure', 'Do you want', 'This will', 'This is',
);

$hits = array();
foreach ( $d as $en => $fa ) {
	if ( ! is_string( $fa ) ) {
		continue;
	}
	// Still identical (non-brand)
	if ( $fa === $en && ! preg_match( '/^(UpdraftCentral|UpdraftPlus|UpdraftVault|WP-Optimize|WordPress)$/', $en ) ) {
		$hits[ $en ] = $fa;
		continue;
	}
	foreach ( $markers as $m ) {
		if ( false !== strpos( $fa, $m ) ) {
			$hits[ $en ] = $fa;
			break;
		}
	}
	// Mixed: has Persian but also leftover common English function words
	if ( preg_match( '/[\x{0600}-\x{06FF}]/u', $fa ) && preg_match( '/\b(the|and|from|your|with|for|this|that|will|have|been|are|was|were|not|only|when|then|also|please|wait|items|selected|updates|plugins|themes|server|storage|region|files|delete|remove|queue|found|given|translate|section|status|raw)\b/i', $fa ) ) {
		// Allow if those words are only inside known brands already handled - still flag
		$hits[ $en ] = $fa;
	}
}

file_put_contents( __DIR__ . '/leftover-en.json', json_encode( $hits, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
echo 'leftover=' . count( $hits ) . "\n";
$i = 0;
foreach ( $hits as $k => $v ) {
	echo substr( $k, 0, 100 ) . "\n => " . substr( $v, 0, 100 ) . "\n\n";
	if ( ++$i >= 60 ) {
		echo "...\n";
		break;
	}
}
