<?php

namespace Solunes\Store\App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Solunes\Master\App\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        
        // Módulo de Cuentas
        $events->listen('eloquent.creating: Solunes\Store\App\BankAccount', '\Solunes\Store\App\Listeners\RegisteringBankAccount');
        $events->listen('eloquent.creating: Solunes\Store\App\PlaceAccountability', '\Solunes\Store\App\Listeners\RegisterPlaceAccountability');
        $events->listen('eloquent.created: Solunes\Store\App\PlaceMovement', '\Solunes\Store\App\Listeners\RegisterPlaceMovement');
        $events->listen('eloquent.created: Solunes\Store\App\Income', '\Solunes\Store\App\Listeners\RegisterIncome');
        $events->listen('eloquent.created: Solunes\Store\App\Expense', '\Solunes\Store\App\Listeners\RegisterExpense');
        // Módulo de Inventario
        $events->listen('eloquent.created: Solunes\Store\App\Product', '\Solunes\Store\App\Listeners\RegisterProduct');
        $events->listen('eloquent.saving: Solunes\Store\App\Purchase', '\Solunes\Store\App\Listeners\SavePurchase');
        $events->listen('eloquent.created: Solunes\Store\App\PurchaseProduct', '\Solunes\Store\App\Listeners\RegisterPurchaseProduct');
        $events->listen('eloquent.saving: Solunes\Store\App\PurchaseProduct', '\Solunes\Store\App\Listeners\SavePurchaseProduct');
        $events->listen('eloquent.created: Solunes\Store\App\InventoryMovement', '\Solunes\Store\App\Listeners\RegisterInventoryMovement');
        // Módulo de Capital
        $events->listen('eloquent.creating: Solunes\Store\App\Partner', '\Solunes\Store\App\Listeners\RegisteringPartner');
        $events->listen('eloquent.created: Solunes\Store\App\PartnerMovement', '\Solunes\Store\App\Listeners\RegisterPartnerMovement');
        $events->listen('eloquent.created: Solunes\Store\App\PartnerDetail', '\Solunes\Store\App\Listeners\RegisterPartnerDetail');
        $events->listen('eloquent.saving: Solunes\Store\App\PartnerDetail', '\Solunes\Store\App\Listeners\SavePartnerDetail');
        // Módulo de Ventas
        $events->listen('eloquent.creating: Solunes\Store\App\Sale', '\Solunes\Store\App\Listeners\RegisteringSale');
        $events->listen('eloquent.saving: Solunes\Store\App\Sale', '\Solunes\Store\App\Listeners\SavingSale');
        $events->listen('eloquent.created: Solunes\Store\App\SaleCredit', '\Solunes\Store\App\Listeners\RegisterSaleCredit');
        $events->listen('eloquent.saving: Solunes\Store\App\SalePayment', '\Solunes\Store\App\Listeners\SavingSalePayment');
        $events->listen('eloquent.created: Solunes\Store\App\SaleItem', '\Solunes\Store\App\Listeners\RegisterSaleItem');
        $events->listen('eloquent.created: Solunes\Store\App\Refund', '\Solunes\Store\App\Listeners\RegisterRefund');
        $events->listen('eloquent.created: Solunes\Store\App\RefundItem', '\Solunes\Store\App\Listeners\RegisterRefundItem');

        parent::boot($events);
    }
}
