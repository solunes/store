<?php

namespace Solunes\Store\App\Listeners;

class RegisteringBankAccount {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $asset_bank_id = \Solunes\Store\App\Concept::getCode('asset_bank')->id;
            $code = str_replace(' ', '_', $event->name);
            $code = strtolower($code);
            $account = new \Solunes\Store\App\Account;
            $account->concept_id = $asset_bank_id;
            $account->name = 'Banco '.$event->name;
            $account->code = 'asset_bank_'.$code;
            $account->save();
            $event->account_id = $account->id;
            return $event;
    	}

    }

}
