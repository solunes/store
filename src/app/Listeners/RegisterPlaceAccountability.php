<?php

namespace Solunes\Store\App\Listeners;

class RegisterPlaceAccountability {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $parent = $event->parent;
            if($account_detail = $parent->place_accountability()->where('account_id', $event->account_id)->orderBy('id', 'DESC')->first()){
                $balance = $account_detail->balance;
            } else {
                $balance = 0;
            }
            $currency = \Solunes\Store\App\Currency::find(1);
            if($account_currency_detail = $parent->place_accountability()->where('account_id', $event->account_id)->where('currency_id', $event->currency_id)->orderBy('id', 'DESC')->first()){
                $currency_balance = $account_currency_detail->currency_balance;
            } else {
                $currency_balance = 0;
            }
            $amount = \Store::calculate_currency($event->amount, $currency, $event->currency, $event->exchange);
            if($event->type=='debit'){
                $event->debit = $amount;
                $currency_balance += $event->amount;
            } else if($event->type=='credit'){
                $event->credit = $amount;
                $amount = -$amount;
                $currency_balance -= $event->amount;
            }
            $event->real_amount += $amount;
            $balance += $amount;
            $event->balance = $balance;
            $event->currency_balance = $currency_balance;
            return $event;
    	}

    }

}
