<?php

namespace Solunes\Store\App\Listeners;

class RegisterPartnerDetail {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            $partner = $event->parent;
            $partner->capital = $partner->capital - $event->investment;
            $partner->save();
            $event->load('partner_transport');
            $partner_transport = $event->partner_transport;
            $partner_transport->capital = $partner_transport->capital - $event->transport_investment;
            $partner_transport->save();
            return $event;
    	}

    }

}
