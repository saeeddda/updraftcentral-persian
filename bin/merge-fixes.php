<?php
$base  = include __DIR__ . '/dict-fa.php';
$fixes = include __DIR__ . '/dict-fa-fixes.php';
$merged = array_merge( $base, $fixes );
ksort( $merged, SORT_STRING );

$export = var_export( $merged, true );
$export = preg_replace( '/^array \(/', '[', $export );
$export = preg_replace( '/\)$/', ']', $export );
file_put_contents( __DIR__ . '/dict-fa.php', "<?php\n/** Merged Persian dictionary. */\n\nreturn " . $export . ";\n" );
echo 'merged=' . count( $merged ) . ' fixes=' . count( $fixes ) . "\n";
