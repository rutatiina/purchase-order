<?php

namespace Rutatiina\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Rutatiina\Tenant\Scopes\TenantIdScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItemTax extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logName = 'TxnItem';
    protected static $logFillable = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['updated_at'];
    protected static $logOnlyDirty = true;

    protected $connection = 'tenant';

    protected $table = 'rg_purchase_order_item_taxes';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TenantIdScope);
    }

    public function tax()
    {
        return $this->hasOne('Rutatiina\Tax\Models\Tax', 'code', 'tax_code');
    }

    public function purchase_order()
    {
        return $this->belongsTo('Rutatiina\PurchaseOrder\Models\PurchaseOrder', 'purchase_order_id', 'id');
    }

    public function purchase_order_item()
    {
        return $this->belongsTo('Rutatiina\PurchaseOrder\Models\PurchaseOrderItem', 'purchase_order_item_id', 'id');
    }

}
