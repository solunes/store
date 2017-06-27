<?php

namespace Solunes\Store\App\Listeners;

class RegisteringPartner {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $equity_id = \Solunes\Store\App\Account::getCode('equity')->id;
            $code = str_replace(' ', '_', $event->name);
            $code = strtolower($code);
            $account = new \Solunes\Store\App\Account;
            $account->concept_id = $equity_id;
            $account->name = 'Socio: '.$event->name;
            $account->code = 'capital_'.$code;
            $account->save();
            $event->account_id = $account->id;
            return $event;
    	}

    }

}
