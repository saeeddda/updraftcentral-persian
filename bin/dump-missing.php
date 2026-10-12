<?php
$d   = include __DIR__ . '/dict-fa.php';
$out = array();
foreach ( $d as $k => $v ) {
	if ( $k === $v ) {
		$out[] = $k;
	}
}
file_put_contents( __DIR__ . '/missing-en.json', json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
echo 'missing=' . count( $out ) . " → bin/missing-en.json\n";
