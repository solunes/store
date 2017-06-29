<?php

namespace Solunes\Store\App\Listeners;

class RegisterSale {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){

            $cash_bob = $event->cash_bob;
            $cash_usd = $event->cash_usd;
            $pos_bob = $event->pos_bob;
            $credit_amount = 0;
            $income_sale_credit = \Solunes\Store\App\Account::getCode('income_sale_credit')->id;
            if($event->credit&&$event->credit_amount){
                $accounts_receivable = new \Solunes\Store\App\AccountsReceivable;
                $accounts_receivable->name = 'Crédito por venta de productos.';
                $accounts_receivable->place_id = $event->place_id;
                $accounts_receivable->account_id = $income_sale_credit;
                $accounts_receivable->currency_id = $event->currency_id;
                $accounts_receivable->sale_id = $event->id;
                $accounts_receivable->due_date = $event->credit_due;
                $accounts_receivable->amount = $event->credit_amount;
                $accounts_receivable->reference = $event->credit_details;
                $accounts_receivable->created_at = $event->created_at;
                $accounts_receivable->save();
                $credit_amount = $event->credit_amount;
            }

            /* Crear cuentas de ventas */
            $asset_cash = \Solunes\Store\App\Account::getCode('asset_cash_small')->id;
            $asset_bank = \Solunes\Store\App\Concept::getCode('asset_bank')->account->id;
            $asset_ctc = \Solunes\Store\App\Account::getCode('asset_ctc')->id;
            $liability_ctp = \Solunes\Store\App\Account::getCode('liability_ctp')->id;
            $expense_sale_tax = \Solunes\Store\App\Account::getCode('expense_sale_tax')->id;
            $income_sale = \Solunes\Store\App\Account::getCode('income_sale')->id;
            $income_sale_credit = \Solunes\Store\App\Account::getCode('income_sale_credit')->id;
            $name = 'Venta de mercaderia';
            $arr = [];
            $sale_amount = $event->amount;
            $taxes = 0;
            // Ajustes si incluye factura, no descuenta venta, se asigna como costo de impuestos.
            if($event->invoice){
                $taxes = $sale_amount * 0.16;
                //$sale_amount -= $taxes;
            }
            // Ajuste si hay crédito.
            if($credit_amount>0){
                $sale_amount -= $credit_amount;
            } else {
                $cash_bob -= $event->change;
            }
            if($cash_bob>0){
                $arr[] = \Store::register_account($event->place_id, 'debit', $asset_cash, 1, $cash_bob, $name);
            } else if($cash_bob<0){
                $arr[] = \Store::register_account($event->place_id, 'credit', $asset_cash, 1, abs($cash_bob), $name.': Cambio devuelto');
            }
            if($cash_usd>0){
                $exchange = $event->exchange;
                $arr[] = \Store::register_account($event->place_id, 'debit', $asset_cash, 2, $cash_usd, $name, $exchange);
            }
            if($pos_bob>0){
                $arr[] = \Store::register_account($event->place_id, 'debit', $asset_bank, 1, $pos_bob, $name);
            }
            if($taxes>0){
                $arr[] = \Store::register_account($event->place_id, 'credit', $liability_ctp, 1, $taxes, $name);
                $arr[] = \Store::register_account($event->place_id, 'debit', $expense_sale_tax, 1, $taxes, $name);
            }
            $arr[] = \Store::register_account($event->place_id, 'credit', $income_sale, $event->currency_id, $sale_amount, $name);
            if($credit_amount>0){
                $arr[] = \Store::register_account($event->place_id, 'debit', $asset_ctc, 1, $credit_amount, $name);
                $arr[] = \Store::register_account($event->place_id, 'credit', $income_sale_credit, 1, $credit_amount, $name);
            }
            \Store::register_account_array($arr, $event->created_at, $event->transaction_code);
            /* Crear cuentas de costo de envío */
            /*if($event->shipping_cost>0){
                $concept = \Solunes\Store\App\Account::getCode('expense_operating_com')->id;
                \Store::register_account($event->place_id, 'credit', $income_sale, $event->currency_id, $event->amount, $name);
            }*/
            return $event;
    	}

    }

}
