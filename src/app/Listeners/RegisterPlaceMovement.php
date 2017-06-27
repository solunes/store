<?php

namespace Solunes\Store\App\Listeners;

class RegisterPlaceMovement {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $equity = \Solunes\Store\App\Account::getCode('equity')->id;
            /* Cuenta Desde */
            $account_id = $event->account_from_id;
            $place_id = $event->place_from_id;
            $name = 'Transferencia enviada a: '.$event->place_to->name;
            $arr[] = \Store::register_account($place_id, 'credit', $account_id, $event->currency_id, $event->amount, $name);
            $arr[] = \Store::register_account($place_id, 'debit', $equity, $event->currency_id, $event->amount, $name);
            /* Cuenta Hasta */
            $account_id = $event->account_from_id;
            $place_id = $event->place_to_id;
            $name = 'Transferencia recibida desde: '.$event->place_from->name;
            $arr[] = \Store::register_account($place_id, 'debit', $account_id, $event->currency_id, $event->amount, $name);
            $arr[] = \Store::register_account($place_id, 'credit', $equity, $event->currency_id, $event->amount, $name);
            \Store::register_account_array($arr, $event->created_at);

            return $event;
    	}

    }

}
