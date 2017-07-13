<?php

namespace Solunes\Store\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class TruncateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
        // Módulo de Ventas
        \Solunes\Store\App\SpBankDeposit::truncate();
        \Solunes\Store\App\RefundItem::truncate();
        \Solunes\Store\App\Refund::truncate();
        \Solunes\Store\App\SaleCredit::truncate();
        \Solunes\Store\App\SaleDelivery::truncate();
        \Solunes\Store\App\SalePayment::truncate();
        \Solunes\Store\App\SaleItem::truncate();
        \Solunes\Store\App\Sale::truncate();
        \Solunes\Store\App\CartItem::truncate();
        \Solunes\Store\App\Cart::truncate();
        \Solunes\Store\App\ShippingCity::truncate();
        \Solunes\Store\App\Shipping::truncate();
        \Solunes\Store\App\Payment::truncate();
        // Módulo de Capital
        \Solunes\Store\App\PartnerMovement::truncate();
        //\Solunes\Store\App\PartnerDetail::truncate();
        \Solunes\Store\App\Partner::truncate();
        // Módulo de Contabilidad
        \Solunes\Store\App\AccountsPayable::truncate();
        \Solunes\Store\App\AccountsReceivable::truncate();
        \Solunes\Store\App\Expense::truncate();
        \Solunes\Store\App\Income::truncate();
        \Solunes\Store\App\PlaceMovement::truncate();
        \Solunes\Store\App\PlaceAccountability::truncate();
        \Solunes\Store\App\BankAccount::truncate();
        \Solunes\Store\App\Account::truncate();
        \Solunes\Store\App\Concept::truncate();
        // Módulo de Productos e Inventario
        \Solunes\Store\App\InventoryMovement::truncate();
        \Solunes\Store\App\PackageProduct::truncate();
        \Solunes\Store\App\Package::truncate();
        \Solunes\Store\App\PurchaseProduct::truncate();
        \Solunes\Store\App\Purchase::truncate();
        \Solunes\Store\App\ProductOffer::truncate();
        \Solunes\Store\App\ProductImage::truncate();
        \Solunes\Store\App\ProductStock::truncate();
        \Solunes\Store\App\ProductBenefit::truncate();
        \Solunes\Store\App\ProductGroup::truncate();
        \Solunes\Store\App\Product::truncate();
        \Solunes\Store\App\Category::truncate();
        \Solunes\Store\App\Variation::truncate();
        // Módulo General de Empresa ERP
        \Solunes\Store\App\Tax::truncate();
        \Solunes\Store\App\Place::truncate();
        \Solunes\Store\App\Currency::truncate();
        \Solunes\Store\App\TransactionCode::truncate();
        \Solunes\Store\App\City::truncate();
        \Solunes\Store\App\Region::truncate();

    }
}