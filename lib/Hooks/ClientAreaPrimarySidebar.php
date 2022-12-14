<?php

namespace WHMCS\Module\Registrar\Arsys\Hooks;

use Menu;
use Lang;

class ClientAreaPrimarySidebar {
	public function __construct() {
	}

	public function __invoke( $params ) {
		$primarySidebar = Menu::primarySidebar();

		if ( ! is_null( $primarySidebar->getChild( 'Domain Details Management' ) ) ) {
			$domain = Menu::context( 'domain' );

			$newMenu = $primarySidebar->addChild( 'Additional services', [
				'label' => Lang::Trans( 'arsysAdditionalServices' )
			] );

			$newMenu->moveDown();

			$newMenu->addChild( 'Whois Privacy', [
				'name'    => 'Home',
				'label'   => Lang::Trans( 'arsysWhoisPrivacy' ),
				'uri'     => 'clientarea.php?action=domaindetails&domainid=' . $domain->Id . '&modop=custom&a=whoisPrivacy',
				'current' => ( $_GET['a'] == 'whoisPrivacy' ) ? true : false
			] );
		}

		if ( $_GET['modop'] == 'custom' ) {
			$primarySidebar->removeChild( 'Domain Details Management' );
		}
	}
}
