<?php 

namespace Solunes\Store\App\Helpers;

use Form;

class CustomStore {
   
    public static function after_seed_actions() {
        $node_array['currency'] = ['action_field'=>['edit']];
        $node_array['place'] = ['action_field'=>['edit']];
        $node_array['tax'] = ['action_field'=>['edit']];
        $node_array['transaction-code'] = ['action_field'=>['edit']];
        $node_array['concept'] = ['action_field'=>['edit']];
        $node_array['account'] = ['action_field'=>['edit']];
        $node_array['bank-account'] = ['action_field'=>['edit']];
        $node_array['user'] = ['action_field'=>['edit']];
        $node_array['income'] = ['action_field'=>['edit']];
        $node_array['expense'] = ['action_field'=>['edit']];
        $node_array['inventory-movement'] = ['action_field'=>['edit']];
        $node_array['accounts-receivable'] = ['action_field'=>['edit']];
        $node_array['accounts-payable'] = ['action_field'=>['edit']];
        $node_array['product'] = ['action_field'=>['edit']];
        $node_array['product-stock'] = ['action_field'=>['edit']];
        $node_array['partner'] = ['action_field'=>['edit']];
        $node_array['partner-movement'] = ['action_field'=>['view']];
        $node_array['sale'] = ['action_field'=>['view'], 'action_node'=>['back','excel']];
        foreach($node_array as $node_name => $node_detail){
            $node = \Solunes\Master\App\Node::where('name', $node_name)->first();
            foreach($node_detail as $extra_type => $extra_value) {
                $node_extra = new \Solunes\Master\App\NodeExtra;
                $node_extra->parent_id = $node->id;
                $node_extra->type = $extra_type;
                $node_extra->value_array = json_encode($extra_value);
                $node_extra->save();
            }
        }
        // Borrar opciones del menú
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 1)->whereTranslation('name', 'Global')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 1)->whereTranslation('name', 'Sitio')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Ventas')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Devoluciones')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Grupos de Productos')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Detalles de Resultados')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Ordenes de Compras')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Variaciones')->delete();

        // Menú
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Contabilidad')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Crear Ingreso','link'=>'admin/model/income/create'];
        //$menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Ingresos','link'=>'admin/account-cash-flow/credit'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Crear Egreso','link'=>'admin/model/expense/create'];
        //$menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Egresos','link'=>'admin/account-cash-flow/debit'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Compañia')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Usuarios','link'=>'admin/model-list/user'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Ventas')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'calculator','name'=>'Realizar Venta','link'=>'admin/create-sale'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'calculator','name'=>'Realizar Devolución','link'=>'admin/create-refund'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte Diario','link'=>'admin/sales-report?period=day&initial_date=&initial_date_submit=&end_date=&end_date_submit=&currency_id=1&place_id=all'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Ventas','link'=>'admin/sales-detail-report'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Inventario')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Agregar Producto','link'=>'admin/model/product/create'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Inventario','link'=>'admin/products-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'print','name'=>'Imprimir Etiquetas','link'=>'admin/generate-barcodes-pdf'];
        $pm = new \Solunes\Master\App\Menu;
        $pm->level = 1;
        $pm->type = 'blank';
        $pm->menu_type = 'admin';
        $pm->icon = 'area-chart';
        $pm->name = 'Reportes';
        $pm->save();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Resumen de Ventas','link'=>'admin/sales-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Detalle de Ventas','link'=>'admin/sales-detail-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Reporte de Productos','link'=>'admin/products-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Cuentas por Pagar','link'=>'admin/pending-payments-report/accounts-payable'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Cuentas por Cobrar','link'=>'admin/pending-payments-report/accounts-receivable'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Aportes de Socios','link'=>'admin/partners-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Estado de Resultados','link'=>'admin/account-income-statement'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Balance General','link'=>'admin/balance-sheet'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Arqueo de Cuentas','link'=>'admin/account-book-detail'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Libro Mayor','link'=>'admin/account-book'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Estadísticas de Ventas','link'=>'admin/statistics-sales'];
        foreach($menu_array as $new_menu){
            $menu = new \Solunes\Master\App\Menu;
            if(isset($new_menu['parent_id'])){
                $menu->parent_id = $new_menu['parent_id'];
            }
            $menu->level = $new_menu['level'];
            $menu->menu_type = 'admin';
            $menu->icon = $new_menu['icon'];
            $menu->name = $new_menu['name'];
            $menu->link = $new_menu['link'];
            if(isset($new_menu['order'])){
                $menu->order = $new_menu['order'];
            }
            $menu->save();
        }
        return 'After seed realizado correctamente.';
    }
       
    public static function get_custom_field($name, $parameters, $array, $label, $col, $i, $value, $data_type) {
        // Type = list, item
        $return = NULL;
        if($name=='parcial_cost'){
            $return .= \Field::form_input($i, $data_type, ['name'=>'quantity', 'required'=>true, 'type'=>'string'], ['value'=>1, 'label'=>'Cantidad Comprada', 'cols'=>4]);
            //$return .= \Field::form_input($i, $data_type, ['name'=>'total_cost', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Costo Total de Lote', 'cols'=>6], ['readonly'=>true]);
            if(request()->has('purchase_id')){
                $return .= '<input type="hidden" name="purchase_id" value="'.request()->input('purchase_id').'" />';
            }
        } else if($name=='variations'){
            /*if($i&&count($i->product_variation)>0){
                foreach($i->product_variations as $variation){
                    $return .= '<div id="variation-'.$variation->id.'" class="col-sm-6 flex-item">';
                    $return .= '<label for="variation-'.$variation->id.'" class="control-label">'.$variation->name.'</label>';
                    $return .= \Form::text('variation-'.$variation->id, $variation->pivot->value, ['class'=>'form-control input-lg']);
                    $return .= '</div>';
                }
            }
            $return .= '<div id="variations" class="col-sm-12 flex-item">';
            $return .= '<label for="variations" class="control-label">Variaciones</label>';
            $return .= '<div>- <a class="add-product-variation" href="#">Agregar variación</a>';
            $return .= '</div></div>';*/
        } else if($name=='barcodes'&&$i){
            $categories = \App\Category::has('products')->with('products')->orderBy('name', 'ASC')->get();
            $product_options = [''=>'-'];
            foreach($categories as $category){
                foreach($category->products as $product){
                    $product_options[$category->name][$product->id] = $product->name.' ('.$product->barcode.')';
                }
            }
            if($i->status=='pending'){
                $return .= '</div><h3>Agregar Productos</h3><p id="notification-bar"></p>';
                $return .= '<div class="row">';
                $return .= \Field::form_input($i, $data_type, ['name'=>'barcode-reader', 'required'=>true, 'type'=>'string'], ['label'=>'Introduzca el código de barras o utilce el lector de código de barras', 'cols'=>4]);
                $return .= \Field::form_input($i, $data_type, ['name'=>'search-product', 'required'=>true, 'type'=>'select', 'options'=>$product_options], ['label'=>'Seleccione un producto para agregar', 'cols'=>4]);
                $return .= '<div class="col-sm-4"><br><a class="btn btn-site lightbox" href="'.url('admin/child-model/product/create').'?purchase_id='.$i->id.'&printed=0&lightbox[width]=1000&lightbox[height]=600" style="width: 100%;">Crear Nuevo Producto</a></div>';
            }
            $return .= '</div><h3>Lista de Productos</h3>';
            $return .= '<table class="table" id="products">';
            $return .= '<thead><tr class="title"><td>Nº</td><td>Nombre Producto</td><td>Costo Unitario</td><td>Moneda</td><td>Cantidad</td><td>Subtotal</td></tr></thead>';
            $return .= '<tbody>';
            foreach($i->purchase_products as $key => $batch){
              $return .= '<tr><td class="count">'.($key+1).'</td><td>'.$batch->product->name.'</td><td>'.$batch->cost.'</td>';
              $return .= '<td>'.$batch->currency->name.'</td><td>'.$batch->quantity.'</td><td>'.$batch->quantity * $batch->cost.' '.$batch->currency->name.'</td></tr>';
            }
            $return .= '</tbody></table><div class="row">';
            $return .= '<br>'.\Field::form_input($i, $data_type, ['name'=>'total', 'required'=>true, 'type'=>'string'], ['label'=>'Total de la compra', 'cols'=>6, 'disabled'=>true]);
            $return .= '</div><div class="row">';
            if(count($i->purchase_products)>0){
                if($i->status=='pending'){
                    $status = 'paid';
                    $status_text = 'Marcar como pagado (Ya no se podrá editar)';
                } else if($i->status=='paid') {
                    $status = 'delivered';
                    $status_text = 'Marcar como recibido y finalizar';
                } else {
                    $status = NULL;
                }
                if($status){
                    $return .= '<div class="col-sm-3"><a href="'.url('admin/change-purchase-status/'.$i->id.'/'.$status).'" class="btn btn-site">'.$status_text.'</a></div>';
                }
            }
        } else if($name=='pending_payment'&&$i){
            if($i->status=='holding'){
                if($i->sale){
                    $return .= '<div class="col-sm-3"><br><a target="_blank" class="btn btn-site" href="'.url('admin/model/sale/edit/'.$i->sale_id).'" style="width: 100%;">Revisar Venta Asociada</a></div>';
                    $return .= '<div class="col-sm-3"><br><a class="btn btn-site" href="'.url('admin/pending-payment-unpaid/'.request()->segment(3).'/'.$i->id).'" style="width: 100%;">Dar de Baja Cuenta Pendiente</a></div>';
                }
                $return .= '<div class="col-sm-6"><br><a class="btn btn-site lightbox" href="'.url('admin/pending-payment-register/'.request()->segment(3).'/'.$i->id).'?lightbox[width]=1000&lightbox[height]=600" style="width: 100%;">Registrar Nuevo Pago</a></div>';
            }
            $return .= '<div class="col-sm-12"><h3>Pagos Realizados</h3>';
            if(count($i->account_details)>0){
                $return .= '<table class="table">';
                $return .= '<tr class="title"><td>Nº</td><td>Fecha</td><td>Monto</td></tr>';
                $total = 0;
                foreach($i->account_details as $key => $account){
                    $total += $account->amount;
                    $return .= '<tr><td>'.($key+1).'</td><td>'.$account->created_at->format("d/m/Y").'</td><td>'.$account->amount.'</td></tr>';
                }
                $return .= '<tr class="title"><td>-</td><td>Total</td><td>'.$total.'</td></tr>';
                $return .= '</table>';
            } else {
                $return .= '<h4>No se realizó ningún pago aún.</h4>';
            }
            $return .= '</div>';
        }
        return $return;
    }

    public static function check_permission($type, $module, $node, $action, $id = NULL) {
        // Type = list, item
        $return = 'none';
        if($node->name=='accounts-payable'||$node->name=='accounts-receivable'){
            if($type=='item'&&$action=='edit'){
                if($node->name=='accounts-payable'){
                    $pending = \App\AccountsPayable::find($id);
                } else if($node->name=='accounts-receivable'){
                    $pending = \App\AccountsReceivable::find($id);
                }
                if($pending->status=='paid'){
                    $return = 'false';
                }
            }
        } else if($node->name=='sale'||$node->name=='purchase'){
            if($type=='item'&&$action=='edit'){
                if($node->name=='sale'){
                    $pending = \App\Sale::find($id);
                } else if($node->name=='purchase'){
                    $pending = \App\Purchase::find($id);
                }
                if($pending->status=='paid'||$pending->status=='delivered'){
                    $return = 'false';
                }
            }
        } else if($node->name=='account-detail'||$node->name=='account-movement'||$node->name=='partner-movement'){
            if($type=='item'&&$action=='edit'){
                $return = 'false';
            }
        }
        return $return;
    }

    public static function get_options_relation($submodel, $field, $subnode, $id = NULL) {
        if($field->relation_cond=='account_concepts'){
            $node_name = request()->segment(3);
            if($id){
                $node = \Solunes\Master\App\Node::where('name', request()->segment(3))->first();
                $model = \FuncNode::node_check_model($node);
                $model = $model->find($id);
                $submodel = $submodel->where('id', $model->account_id);
            } else {
                if(auth()->check()&&auth()->user()->hasRole('admin')){
                    if($node_name=='income'||$node_name=='accounts-receivable'){
                        $submodel = $submodel->where('code', 'income_other');
                    } else if($node_name=='expense'||$node_name=='accounts-payable'){
                        $submodel = $submodel->whereIn('code', ['expense_operating_com','expense_operating_adm','expense_operating_dep','expense_operating_int','expense_other']);
                    }
                } else {
                    if($node_name=='income'){
                        $submodel = $submodel->where('code', 'income_other');
                    } else if($node_name=='expense'){
                        $submodel = $submodel->where('code', 'expense_other');
                    }
                }
            }
        } else if($field->relation_cond=='account_currency'){
            $submodel = $submodel->where('in_accounts', 1);
        } else if($field->relation_cond=='main_currency'){
            $submodel = $submodel->where('type', 'main');
        }
        return $submodel;
    }

}