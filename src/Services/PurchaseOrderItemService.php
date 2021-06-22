<?php

namespace Rutatiina\PurchaseOrder\Services;

use Rutatiina\PurchaseOrder\Models\PurchaseOrderItem;
use Rutatiina\PurchaseOrder\Models\PurchaseOrderItemTax;

class PurchaseOrderItemService
{
    public static $errors = [];

    public function __construct()
    {
        //
    }

    public static function store($data)
    {
        //print_r($data['items']); exit;

        //Save the items >> $data['items']
        foreach ($data['items'] as &$item)
        {
            $item['purchase_order_id'] = $data['id'];

            $itemTaxes = (is_array($item['taxes'])) ? $item['taxes'] : [] ;
            unset($item['taxes']);

            $itemModel = PurchaseOrderItem::create($item);

            foreach ($itemTaxes as $tax)
            {
                //save the taxes attached to the item
                $itemTax = new PurchaseOrderItemTax;
                $itemTax->tenant_id = $item['tenant_id'];
                $itemTax->purchase_order_id = $item['purchase_order_id'];
                $itemTax->purchase_order_item_id = $itemModel->id;
                $itemTax->tax_code = $tax['code'];
                $itemTax->amount = $tax['total'];
                $itemTax->inclusive = $tax['inclusive'];
                $itemTax->exclusive = $tax['exclusive'];
                $itemTax->save();
            }
            unset($tax);
        }
        unset($item);

    }

}
