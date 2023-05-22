<?php

namespace Rutatiina\PurchaseOrder\Services;

use Rutatiina\FinancialAccounting\Services\ItemBalanceUpdateService;
use Rutatiina\FinancialAccounting\Services\AccountBalanceUpdateService;
use Rutatiina\FinancialAccounting\Services\ContactBalanceUpdateService;

trait PurchaseOrderApprovalService
{
    public static function run($txn)
    {
        if (strtolower($txn['status']) == 'draft')
        {
            //cannot update balances for drafts
            return false;
        }

        if (isset($txn['balances_where_updated']) && $txn['balances_where_updated'])
        {
            //cannot update balances for task already completed
            return false;
        }

        //inventory checks and inventory balance update if needed
        //$this->inventory(); //currentlly inventory update for sales order is disabled

        //Update the account balances
        AccountBalanceUpdateService::singleEntry($txn);

        //Update the contact balances
        ContactBalanceUpdateService::singleEntry($txn);

        //Update the item balances
        ItemBalanceUpdateService::entry($txn);

        $txn->status = 'approved';
        $txn->balances_where_updated = 1;
        $txn->save();

        return true;
    }

}
