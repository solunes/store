<?php

namespace Solunes\Store\App\Listeners;

class RegisterExpense {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){

            /* Crear cuentas de ventas */
            $asset_cash = \Solunes\Store\App\Account::getCode('asset_cash_small')->id;
            $name = $event->name;
            $arr[] = \Store::register_account($event->place_id, 'credit', $asset_cash, $event->currency_id, $event->amount, $name);
            $arr[] = \Store::register_account($event->place_id, 'debit', $event->account_id, $event->currency_id, $event->amount, $name);
            \Store::register_account_array($arr, $event->created_at);
            return $event;
    	}

    }

}
