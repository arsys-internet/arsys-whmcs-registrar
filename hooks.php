<?php

// This is a Hook Loader, only $hooks must be modifiable

$hooks = [
	'ClientAreaSidebars',
	'ClientAreaPrimarySidebar'
];

foreach ( $hooks as $hook ) {
	$className = 'WHMCS\Module\Registrar\Arsys\Hooks\\' . $hook;

	if ( ! class_exists( $className ) ) {
		continue;
	}

	if ( ! method_exists( $className, '__invoke' ) ) {
		continue;
	}

	add_hook( $hook, 1, new $className() );
}
