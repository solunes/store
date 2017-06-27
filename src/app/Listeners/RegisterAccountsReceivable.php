<?php

namespace Solunes\Store\App\Listeners;

class RegisterAccountsReceivable {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){

            /* Crear cuentas de ventas */
            $asset_ctc = \Solunes\Store\App\Concept::getCode('asset_ctc')->account->id;
            $name = $event->name;
            $arr[] = \Store::register_account($event->place_id, 'debit', $asset_ctc, $event->currency_id, $event->amount, $name);
            $arr[] = \Store::register_account($event->place_id, 'credit', $event->account_id, $event->currency_id, $event->amount, $name);
            \Store::register_account_array($arr);
            return $event;
    	}

    }

}
