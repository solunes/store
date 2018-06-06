<?php

return [

	// GENERAL
	'taxes' => true,

	// PAYMENTS
	'pagostt_code' => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c',

	// CUSTOM
	'after_seed' => true,
	'check_permission' => true,
	'get_options_relation' => true,

	// CUSTOM FORMS
    'item_get_after_vars' => ['purchase','product'], // array de nodos: 'node'
    'item_child_after_vars' => ['product'],
    'item_remove_scripts' => ['purchase'=>['leave-form']],
    'item_add_script' => ['purchase'=>['barcode-product'], 'product'=>['product']],

];