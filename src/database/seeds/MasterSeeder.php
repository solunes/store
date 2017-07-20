<?php

namespace Solunes\Store\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class MasterSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Módulo General de Empresa ERP
        $node_region = \Solunes\Master\App\Node::create(['name'=>'region', 'location'=>'store', 'folder'=>'company']);
        $node_city = \Solunes\Master\App\Node::create(['name'=>'city', 'table_name'=>'cities', 'location'=>'store', 'folder'=>'company']);
        $node_currency = \Solunes\Master\App\Node::create(['name'=>'currency', 'table_name'=>'currencies', 'location'=>'store', 'folder'=>'company']);
        $node_place = \Solunes\Master\App\Node::create(['name'=>'place', 'location'=>'store', 'folder'=>'company']);
        $node_tax = \Solunes\Master\App\Node::create(['name'=>'tax', 'table_name'=>'taxes', 'location'=>'store', 'folder'=>'company']);
        // Módulo de Contabilidad
        $node_transaction_code = \Solunes\Master\App\Node::create(['name'=>'transaction-code', 'location'=>'store', 'folder'=>'company']);
        $node_concept = \Solunes\Master\App\Node::create(['name'=>'concept', 'location'=>'store', 'folder'=>'company']);
        $node_account = \Solunes\Master\App\Node::create(['name'=>'account', 'location'=>'store', 'folder'=>'company']);
        $node_bank_account = \Solunes\Master\App\Node::create(['name'=>'bank-account', 'location'=>'store', 'folder'=>'company']);
        $node_place_accountability = \Solunes\Master\App\Node::create(['name'=>'place-accountability', 'table_name'=>'place_accountability', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_account->id]);
        $node_place_movement = \Solunes\Master\App\Node::create(['name'=>'place-movement', 'location'=>'store', 'folder'=>'accounting']);
        $node_income = \Solunes\Master\App\Node::create(['name'=>'income', 'location'=>'store', 'folder'=>'accounting']);
        $node_expense = \Solunes\Master\App\Node::create(['name'=>'expense', 'location'=>'store', 'folder'=>'accounting']);
        $node_accounts_payable = \Solunes\Master\App\Node::create(['name'=>'accounts-payable', 'table_name'=>'accounts_payable', 'location'=>'store', 'folder'=>'accounting']);
        $node_accounts_receivable = \Solunes\Master\App\Node::create(['name'=>'accounts-receivable', 'table_name'=>'accounts_receivable', 'location'=>'store', 'folder'=>'accounting']);
        // Módulo de Productos e Inventario
        $node_variation = \Solunes\Master\App\Node::create(['name'=>'variation', 'location'=>'store', 'folder'=>'products']);
        $node_category = \Solunes\Master\App\Node::create(['name'=>'category', 'table_name'=>'categories', 'multilevel'=>true, 'location'=>'store', 'folder'=>'products']);
        $node_product = \Solunes\Master\App\Node::create(['name'=>'product', 'location'=>'store', 'folder'=>'products']);
        $node_product_group = \Solunes\Master\App\Node::create(['name'=>'product-group', 'location'=>'store', 'folder'=>'products']);
        $node_product_benefit = \Solunes\Master\App\Node::create(['name'=>'product-benefit', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_product->id]);
        $node_product_variation = \Solunes\Master\App\Node::create(['name'=>'product-variation', 'type'=>'field', 'model'=>'\App\Variation', 'location'=>'store', 'parent_id'=>$node_product->id]);
        $node_product_stock = \Solunes\Master\App\Node::create(['name'=>'product-stock', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_product->id]);
        \Solunes\Master\App\Node::create(['name'=>'product-image', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_product->id]);
        \Solunes\Master\App\Node::create(['name'=>'product-offer', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_product->id]);
        $node_purchase = \Solunes\Master\App\Node::create(['name'=>'purchase', 'location'=>'store', 'folder'=>'products']);
        $node_purchase_product = \Solunes\Master\App\Node::create(['name'=>'purchase-product', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_purchase->id]);
        $node_package = \Solunes\Master\App\Node::create(['name'=>'package', 'location'=>'store', 'folder'=>'products']);
        $node_package_product = \Solunes\Master\App\Node::create(['name'=>'package-product', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_package->id]);
        $node_inventory_movement = \Solunes\Master\App\Node::create(['name'=>'inventory-movement', 'location'=>'store', 'folder'=>'products']);
        // Módulo de Capital
        $node_partner = \Solunes\Master\App\Node::create(['name'=>'partner', 'location'=>'store', 'folder'=>'capital']);
        //$node_partner_detail = \Solunes\Master\App\Node::create(['name'=>'partner-detail', 'folder'=>'capital', 'type'=>'child', 'parent_id'=>$node_partner->id]);
        $node_partner_movement = \Solunes\Master\App\Node::create(['name'=>'partner-movement', 'folder'=>'capital', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_partner->id]);
        // Módulo de Ventas
        $node_payment = \Solunes\Master\App\Node::create(['name'=>'payment', 'location'=>'store', 'folder'=>'company']);
        $node_shipping = \Solunes\Master\App\Node::create(['name'=>'shipping', 'location'=>'store', 'folder'=>'company']);
        $node_shipping_city = \Solunes\Master\App\Node::create(['name'=>'shipping-city', 'table_name'=>'shipping_cities', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_shipping->id]);
        $node_cart = \Solunes\Master\App\Node::create(['name'=>'cart', 'location'=>'store', 'folder'=>'sales']);
        $node_cart_item = \Solunes\Master\App\Node::create(['name'=>'cart-item', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_cart->id]);
        $node_sale = \Solunes\Master\App\Node::create(['name'=>'sale', 'location'=>'store', 'folder'=>'sales']);
        $node_sale_item = \Solunes\Master\App\Node::create(['name'=>'sale-item', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_sale->id]);
        $node_sale_payment = \Solunes\Master\App\Node::create(['name'=>'sale-payment', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_sale->id]);
        $node_sale_delivery = \Solunes\Master\App\Node::create(['name'=>'sale-delivery', 'table_name'=>'sale_deliveries', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_sale->id]);
        $node_sale_credit = \Solunes\Master\App\Node::create(['name'=>'sale-credit', 'type'=>'child', 'location'=>'store', 'parent_id'=>$node_sale->id]);
        $node_refund = \Solunes\Master\App\Node::create(['name'=>'refund', 'location'=>'store', 'folder'=>'sales']);
        $node_refund_item = \Solunes\Master\App\Node::create(['name'=>'refund-item', 'type'=>'subchild', 'location'=>'store', 'parent_id'=>$node_refund->id]);
        $node_sp_bank_deposit = \Solunes\Master\App\Node::create(['name'=>'sp-bank-deposit', 'location'=>'store', 'folder'=>'company']);

        // Usuarios
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        $products_perm = \Solunes\Master\App\Permission::create(['name'=>'products', 'display_name'=>'Products']);
        $accounting_perm = \Solunes\Master\App\Permission::create(['name'=>'accounting', 'display_name'=>'Contabilidad']);
        $company_perm = \Solunes\Master\App\Permission::create(['name'=>'company', 'display_name'=>'Compañia']);
        $capital_perm = \Solunes\Master\App\Permission::create(['name'=>'capital', 'display_name'=>'Capital']);
        $sales_perm = \Solunes\Master\App\Permission::create(['name'=>'sales', 'display_name'=>'Ventas']);
        $admin->permission_role()->attach([$products_perm->id, $accounting_perm->id, $company_perm->id, $capital_perm->id, $sales_perm->id]);
        $member->permission_role()->attach([$products_perm->id, $sales_perm->id]);

    }
}