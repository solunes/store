<?php

namespace Solunes\Store\App\Listeners;

class RegisterProduct {

    public function handle($event) {
        // Revisar que tenga una sesión y sea un modelo del sitio web.
        if($event){
            if(request()->segment(1)!='artisan'&&!\App::runningInConsole()){
                if(request()->has('purchase_id')){
                    $purchase_id = request()->input('purchase_id');
                    $purchase = \Solunes\Store\App\Purchase::find($purchase_id);
                } else {
                    $purchase = new \Solunes\Store\App\Purchase;
                    $purchase->place_id = auth()->user()->place_id;
                    $purchase->user_id = auth()->user()->id;
                    $purchase->currency_id = $event->currency_id;
                    $purchase->type = 'normal';
                    $purchase->name = 'Compra de Producto';
                    $purchase->save();
                    $purchase->status = 'delivered';
                    $purchase->save();
                }
                $purchase_product = new \Solunes\Store\App\PurchaseProduct;
                $purchase_product->parent_id = $purchase->id;
                $purchase_product->product_id = $event->id;
                if(request()->has('quantity')){
                    $purchase_product->quantity = request()->input('quantity');
                } else {
                    $purchase_product->quantity = 1;
                }
                $purchase_product->currency_id = $event->currency_id;
                $purchase_product->cost = $event->cost;
                $purchase_product->investment = $event->product_cost * $purchase_product->quantity;;
                $purchase_product->transport_investment = $event->transport_cost * $purchase_product->quantity;
                $purchase_product->partner_id = $event->partner_id;
                $purchase_product->partner_transport_id = $event->partner_transport_id;
                $purchase_product->save();
            }

            /*$partner = \Solunes\Store\App\Partner::find($event->partner_id);
            $partner_detail = new \Solunes\Store\App\PartnerDetail;
            $partner_detail->parent_id = $event->partner_id;
            $partner_detail->partner_transport_id = $event->partner_transport_id;
            $partner_detail->status = 'holding';
            $partner_detail->currency_id = $event->currency_id;
            $partner_detail->product_id = $event->id;
            $partner_detail->initial_quantity = 1;
            $partner_detail->quantity = 1;
            $partner_detail->investment = $event->product_cost;
            $partner_detail->transport_investment = $event->transport_cost;
            $partner_detail->save();*/

            return $event;
        }

    }

}
