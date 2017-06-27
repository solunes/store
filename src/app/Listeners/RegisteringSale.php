<?php

namespace Solunes\Store\App\Listeners;

class RegisteringSale {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){

            $event->transaction_code = \Store::generate_code('sale');
            return $event;
    	}

    }

}
