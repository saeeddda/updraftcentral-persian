<?php
$base    = include __DIR__ . '/dict-fa.php';
$missing = include __DIR__ . '/dict-fa-missing.php';
$merged  = array_merge( $base, $missing );
ksort( $merged, SORT_STRING );

$export = var_export( $merged, true );
$export = preg_replace( '/^array \(/', '[', $export );
$export = preg_replace( '/\)$/', ']', $export );
file_put_contents( __DIR__ . '/dict-fa.php', "<?php\n/** Merged Persian dictionary. */\n\nreturn " . $export . ";\n" );

$same = 0;
$fa   = 0;
foreach ( $merged as $k => $v ) {
	if ( $k === $v ) {
		$same++;
	} elseif ( preg_match( '/[\x{0600}-\x{06FF}]/u', $v ) ) {
		$fa++;
	}
}
echo 'merged=' . count( $merged ) . " with_fa=$fa still_en=$same\n";
