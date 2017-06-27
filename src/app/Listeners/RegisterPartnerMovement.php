<?php

namespace Solunes\Store\App\Listeners;

class RegisterPartnerMovement {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $parent = $event->parent;
            if($parent->capital>0){
                $amount = $parent->capital;
            } else {
                $amount = 0;
            }
            $currency = \Solunes\Store\App\Currency::find(1);
            if($event->type=='move_in'){
                $amount += \Store::calculate_currency($event->amount, $currency, $event->currency);
                $asset_cash_type = 'debit';
                $equity_type = 'credit';
                $name = 'Inversión realizada por: '.$event->parent->name;
            } else {
                $amount -= \Store::calculate_currency($event->amount, $currency, $event->currency);
                $asset_cash_type = 'credit';
                $equity_type = 'debit';
                $name = 'Retiro de capital realizado por: '.$event->parent->name;
            }
            $parent->capital = $amount;
            $parent->save();
            $asset_cash = \Solunes\Store\App\Account::getCode('asset_cash_big')->id;
            $equity = \Solunes\Store\App\Account::getCode('equity')->id;
            $arr[] = \Store::register_account($event->place_id, $asset_cash_type, $asset_cash, $event->currency_id, $event->amount, $name);
            $arr[] = \Store::register_account($event->place_id, $equity_type, $equity, $event->currency_id, $event->amount, $name);
            \Store::register_account_array($arr, $event->created_at);
            return $event;
    	}

    }

}
