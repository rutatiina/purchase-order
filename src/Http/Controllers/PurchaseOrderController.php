<?php

namespace Rutatiina\PurchaseOrder\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Rutatiina\PurchaseOrder\Models\PurchaseOrder;
use Rutatiina\FinancialAccounting\Traits\FinancialAccountingTrait;
use Rutatiina\Contact\Traits\ContactTrait;
use Rutatiina\PurchaseOrder\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    use FinancialAccountingTrait;
    use ContactTrait;

    // >> get the item attributes template << !!important

    public function __construct()
    {
        $this->middleware('permission:purchase-orders.view');
        $this->middleware('permission:purchase-orders.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-orders.update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-orders.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $query = PurchaseOrder::query();

        if ($request->contact)
        {
            $query->where(function ($q) use ($request)
            {
                $q->where('contact_id', $request->contact);
            });
        }

        $txns = $query->latest()->paginate($request->input('per_page', 20));

        return [
            'tableData' => $txns
        ];

    }

    public function create()
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $tenant = Auth::user()->tenant;

        $txnAttributes = (new PurchaseOrder())->rgGetAttributes();

        $txnAttributes['number'] = PurchaseOrderService::nextNumber();

        $txnAttributes['status'] = 'approved';
        $txnAttributes['contact_id'] = '';
        $txnAttributes['contact'] = json_decode('{"currencies":[]}'); #required
        $txnAttributes['date'] = date('Y-m-d');
        $txnAttributes['base_currency'] = $tenant->base_currency;
        $txnAttributes['quote_currency'] = $tenant->base_currency;
        $txnAttributes['taxes'] = json_decode('{}');
        $txnAttributes['contact_notes'] = null;
        $txnAttributes['terms_and_conditions'] = null;
        $txnAttributes['items'] = [[
            'selectedTaxes' => [], #required
            'selectedItem' => json_decode('{}'), #required
            'displayTotal' => 0,
            'name' => '',
            'description' => '',
            'rate' => 0,
            'quantity' => 1,
            'total' => 0,
            'taxes' => [],

            'item_id' => '',
            'contact_id' => '',
            'tax_id' => '',
            'units' => '',
            'batch' => '',
            'expiry' => ''
        ]];

        return [
            'pageTitle' => 'Create Purchase Order', #required
            'pageAction' => 'Create', #required
            'txnUrlStore' => '/purchase-orders', #required
            'txnAttributes' => $txnAttributes, #required
        ];
    }

    public function store(Request $request)
    {
        $storeService = PurchaseOrderService::store($request);

        if ($storeService == false)
        {
            return [
                'status' => false,
                'messages' => PurchaseOrderService::$errors
            ];
        }

        return [
            'status' => true,
            'messages' => ['Purchase Order saved'],
            'number' => 0,
            'callback' => route('purchase-orders.show', [$storeService->id], false)
        ];

    }

    public function show($id)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $txn = PurchaseOrder::findOrFail($id);
        $txn->load('contact', 'financial_account', 'items.taxes');
        $txn->setAppends([
            'taxes',
            'number_string',
            'total_in_words',
        ]);

        return $txn->toArray();
    }

    public function edit($id)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $txnAttributes = PurchaseOrderService::edit($id);

        return [
            'pageTitle' => 'Edit Purchase order', #required
            'pageAction' => 'Edit', #required
            'txnUrlStore' => '/purchase-orders/' . $id, #required
            'txnAttributes' => $txnAttributes, #required
        ];
    }

    public function update(Request $request)
    {
        //print_r($request->all()); exit;

        $storeService = PurchaseOrderService::update($request);

        if ($storeService == false)
        {
            return [
                'status' => false,
                'messages' => PurchaseOrderService::$errors
            ];
        }

        return [
            'status' => true,
            'messages' => ['Purchase order updated'],
            'callback' => route('purchase-orders.show', [$storeService->id], false)
        ];
    }

    public function destroy($id)
    {
        $destroy = PurchaseOrderService::destroy($id);

        if ($destroy)
        {
            return [
                'status' => true,
                'messages' => ['Purchase order deleted'],
                'callback' => route('purchase-orders.index', [], false)
            ];
        }
        else
        {
            return [
                'status' => false,
                'messages' => PurchaseOrderService::$errors
            ];
        }
    }

    #-----------------------------------------------------------------------------------

    public function approve($id)
    {
        $approve = PurchaseOrderService::approve($id);

        if ($approve == false)
        {
            return [
                'status' => false,
                'messages' => PurchaseOrderService::$errors
            ];
        }

        return [
            'status' => true,
            'messages' => ['Purchase Order Approved'],
        ];

    }

    public function copy($id)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $txnAttributes = PurchaseOrderService::copy($id);

        return [
            'pageTitle' => 'Copy Purchase Order', #required
            'pageAction' => 'Copy', #required
            'txnUrlStore' => '/purchase-orders', #required
            'txnAttributes' => $txnAttributes, #required
        ];
    }

    public function exportToExcel(Request $request)
    {
        $txns = collect([]);

        $txns->push([
            'DATE',
            'DOCUMENT #',
            'REFERENCE',
            'SUPPLIER / VENDOR',
            'STATUS',
            'EXPIRY DATE',
            'TOTAL',
            ' ', //Currency
        ]);

        foreach (array_reverse($request->ids) as $id)
        {
            $txn = Transaction::transaction($id);

            $txns->push([
                $txn->date,
                $txn->number,
                $txn->reference,
                $txn->contact_name,
                $txn->status,
                $txn->expiry_date,
                $txn->total,
                $txn->base_currency,
            ]);
        }

        $export = $txns->downloadExcel(
            'maccounts-purchase-orders-export-' . date('Y-m-d-H-m-s') . '.xlsx',
            null,
            false
        );

        //$books->load('author', 'publisher'); //of no use

        return $export;
    }

}
