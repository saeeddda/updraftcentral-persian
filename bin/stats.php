<?php
$d = include __DIR__ . '/dict-fa.php';
$good = 0;
$same = 0;
$weak = 0;
$weak_samples = array();
foreach ( $d as $k => $v ) {
	if ( $k === $v ) {
		$same++;
		continue;
	}
	if ( preg_match( '/[\x{0600}-\x{06FF}]/u', $v ) ) {
		$good++;
	} else {
		$weak++;
		if ( count( $weak_samples ) < 10 ) {
			$weak_samples[] = $k;
		}
	}
}
echo 'entries=' . count( $d ) . " good_with_fa=$good same_as_en=$same other=$weak\n";
$mo = dirname( __DIR__ ) . '/languages/updraftcentral-fa_IR.mo';
$po = dirname( __DIR__ ) . '/languages/updraftcentral-fa_IR.po';
echo 'mo=' . ( file_exists( $mo ) ? filesize( $mo ) : 0 ) . " bytes\n";
echo 'po=' . ( file_exists( $po ) ? filesize( $po ) : 0 ) . " bytes\n";
if ( $weak_samples ) {
	echo "weak samples:\n";
	foreach ( $weak_samples as $s ) {
		echo " - $s\n";
	}
}
