<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Domains\DomainLookup\ResultsList;

class CheckAvailability extends Action {
	public function __invoke() {
		$resultList = new ResultsList();

		$premiumEnabled = (bool) $this->getParam( 'premiumEnabled' );

		$main = explode( '.', $this->getParam( 'searchTerm' ) );
		$main = current ( $main );
		if ( empty( $main ) ) {
			$main = $this->getParam( 'searchTerm' );
		}

		foreach ( $this->getParam( 'tldsToInclude' ) as $tld ) {
			$response   = $this->getApp()->getService( 'api' )->getDomainSuggestions(
				$main, '', [ $tld ]
			);

			$domainInfo = ($response->getResponseData())[0];

			/**
			 * [STATUS_REGISTERED] => registered
			[STATUS_NOT_REGISTERED] => available for registration
			[STATUS_RESERVED] => reserved
			[STATUS_UNKNOWN] => unknown
			[STATUS_TLD_NOT_SUPPORTED] => tld not supported
			 */
			switch ( $domainInfo['state'] ) {
				case 'registered_transferable':
				case 'registered_not_transferable':
				case 'internally_registered_not_transferable':
				case 'trademark_protected':
					$status =  SearchResult::STATUS_REGISTERED;
					break;
				case 'available':
					$status = SearchResult::STATUS_NOT_REGISTERED;
			}
//			$status = $domainInfo['state'] ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED;

			if ( ! $premiumEnabled && $domainInfo['premium'] ) {
				$status = SearchResult::STATUS_RESERVED;
			}

			$temp = explode( '.', $domainInfo['name'] );
			$name = $main;

			$searchResult = new SearchResult( $name, $tld );
			$searchResult->setStatus( $status );

			if ( $premiumEnabled && $domainInfo['premium'] ) {
				$searchResult->setPremiumDomain( true );
				$searchResult->setPremiumCostPricing( [
					'register'     => $domainInfo['price'],
					'renew'        => $domainInfo['price'],
					'CurrencyCode' => $domainInfo['currency'],
				] );
			}

			$resultList->append( $searchResult );
		}

		return $resultList;
	}
}
