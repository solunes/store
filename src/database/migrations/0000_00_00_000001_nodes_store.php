<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NodesStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('place_id')->nullable()->after('status');
        });
        // Módulo General de Empresa ERP
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('real_name')->nullable();
            $table->string('plural')->nullable();
            $table->enum('type', ['main', 'secondary'])->nullable()->default('secondary');
            $table->decimal('main_exchange', 10, 5)->nullable()->default(1);
            $table->boolean('in_accounts')->nullable()->defaul(1);
            $table->timestamps();
        });
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->enum('type', ['central', 'store',  'office', 'storage'])->nullable()->default('store');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('map')->nullable();
            $table->boolean('has_accountability')->nullable()->default(0);
            $table->timestamps();
        });
        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->enum('type', ['over_sales','over_profit'])->nullable();
            $table->integer('percentage')->nullable();
            $table->timestamps();
        });
        /* Módulo de Contabilidad */
        Schema::create('transaction_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->integer('code')->nullable();
            $table->timestamps();
        });
        Schema::create('concepts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order')->nullable()->default(0);
            $table->string('code')->nullable();
            $table->enum('type', ['asset','liability','equity','income','expense'])->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('concept_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->foreign('concept_id')->references('id')->on('concepts')->onDelete('cascade');
        });
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank')->nullable();
            $table->string('account_number')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        Schema::create('place_accountability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('transaction_code')->nullable();
            $table->integer('pending_payment_id')->nullable();
            $table->string('name')->nullable();
            $table->enum('type', ['credit','debit'])->nullable();
            $table->string('reference')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->decimal('exchange', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('real_amount', 10, 2)->nullable();
            $table->decimal('credit', 10, 2)->nullable();
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->decimal('currency_balance', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('place_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_from_id')->unsigned();
            $table->integer('place_to_id')->unsigned();
            $table->integer('account_from_id')->unsigned();
            $table->integer('account_to_id')->unsigned();
            $table->string('name')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->foreign('place_from_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('place_to_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('account_from_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('account_to_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('reference')->nullable();
            $table->integer('place_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('sale_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('reference')->nullable();
            $table->integer('place_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('partner_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('reference')->nullable();
            $table->enum('status', ['holding','paid','unpaid'])->nullable()->default('holding');
            $table->integer('place_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->date('due_date')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('accounts_receivable', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('reference')->nullable();
            $table->enum('status', ['holding','paid','unpaid'])->nullable()->default('holding');
            $table->integer('place_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->date('due_date')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->integer('sale_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        // Módulo de Productos e Inventario
        Schema::create('variations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->enum('type', ['normal', 'color', 'image'])->nullable()->default('normal');
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('level')->nullable();
            $table->integer('order')->nullable()->default(0);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('barcode')->nullable();
            $table->integer('category_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('product_size')->nullable(); // Retirar y cambiar por variantes
            $table->boolean('printed')->nullable()->default(0);
            $table->integer('currency_id')->unsigned();
            $table->integer('partner_id')->nullable();
            $table->integer('partner_transport_id')->nullable();
            $table->integer('external_currency_id')->unsigned();
            $table->decimal('currency_product_cost', 10, 2)->nullable();
            $table->decimal('currency_transport_cost', 10, 2)->nullable();
            $table->decimal('exchange', 10, 2)->nullable();
            $table->decimal('product_cost', 10, 2)->nullable();
            $table->decimal('transport_cost', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('no_invoice_price', 10, 2)->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->integer('product_group_id')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('product_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('product_variation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('variation_id')->unsigned();
            $table->string('value')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
        });
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->integer('initial_quantity')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });
        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->enum('type', ['normal', 'online'])->nullable()->default('normal');
            $table->string('name')->nullable();
            $table->text('files')->nullable();
            $table->enum('status', ['pending','delivered','paid'])->nullable()->default('pending');
            $table->timestamps();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->enum('status', ['holding','finished'])->nullable()->default('holding');
            $table->integer('initial_quantity')->nullable()->default(0);
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('currency_id')->unsigned();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('sale_item_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->integer('partner_transport_id')->nullable();
            $table->decimal('investment', 10, 2)->nullable()->default(0);
            $table->decimal('transport_investment', 10, 2)->nullable()->default(0);
            $table->decimal('return', 10, 2)->nullable()->default(0);
            $table->decimal('transport_return', 10, 2)->nullable()->default(0);
            $table->decimal('profit', 10, 2)->nullable()->default(0);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->string('name')->nullable();
            $table->enum('type', ['move_in','move_out'])->nullable()->default('move_in');
            $table->integer('quantity')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });
        /* Módulo de Ventas */
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('cash_bob', 10, 2)->nullable();
            $table->decimal('cash_usd', 10, 2)->nullable();
            $table->decimal('pos_bob', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->string('exchange')->nullable();
            $table->boolean('invoice')->nullable()->default(0);
            $table->string('invoice_name')->nullable();
            $table->string('invoice_nit')->nullable();
            $table->enum('type', ['normal','web','online'])->nullable()->default('normal');
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->boolean('credit')->nullable()->default(0);
            $table->decimal('credit_amount', 10, 2)->nullable();
            $table->date('credit_due')->nullable();
            $table->string('credit_details')->nullable();
            $table->string('transaction_code')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('sale_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('purchase_product_id')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->nullable();
            $table->decimal('pending', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->foreign('parent_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('sale_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('refund_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('initial_quantity')->nullable();
            $table->decimal('initial_amount', 10, 2)->nullable();
            $table->integer('refund_quantity')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->integer('sale_item_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('refunds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        /* Módulo de Capital */
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('account_id')->unsigned();
            $table->decimal('return_percentage', 10, 2)->nullable()->default(10);
            $table->decimal('capital', 10, 2)->nullable()->default(0);
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        /*Schema::create('partner_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->enum('status', ['holding','finished'])->nullable()->default('holding');
            $table->integer('currency_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('partner_transport_id')->nullable();
            $table->integer('sale_item_id')->nullable();
            $table->integer('initial_quantity')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('investment', 10, 2)->nullable()->default(0);
            $table->decimal('transport_investment', 10, 2)->nullable()->default(0);
            $table->decimal('return', 10, 2)->nullable()->default(0);
            $table->decimal('transport_return', 10, 2)->nullable()->default(0);
            $table->decimal('profit', 10, 2)->nullable()->default(0);
            $table->boolean('paid')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });*/
        Schema::create('partner_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->string('name')->nullable();
            $table->enum('type', ['move_in','move_out'])->nullable();
            $table->integer('currency_id')->unsigned();
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Módulo de Capital
        Schema::dropIfExists('partner_movements');
        Schema::dropIfExists('partner_details');
        Schema::dropIfExists('partners');
        // Módulo de Ventas
        Schema::dropIfExists('refund_items');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        // Módulo de Productos e Inventario
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('purchase_products');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('product_variation');
        Schema::dropIfExists('product_groups');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('variations');
        // Módulo de Contabilidad
        Schema::dropIfExists('accounts_payable');
        Schema::dropIfExists('accounts_receivable');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('place_movements');
        Schema::dropIfExists('place_accountability');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('concepts');
        Schema::dropIfExists('transaction_codes');
        // Módulo General de Empresa ERP
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('places');
        Schema::dropIfExists('currencies');
    }
}
