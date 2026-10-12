<?php
foreach ( include __DIR__ . '/dict-fa.php' as $k => $v ) {
	if ( $k === $v ) {
		echo $k, PHP_EOL;
	}
}
