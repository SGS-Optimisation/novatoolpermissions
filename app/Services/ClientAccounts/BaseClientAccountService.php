<?php


namespace App\Services\ClientAccounts;


use App\Services\BaseService;
use App\Models\ClientAccount;

abstract class BaseClientAccountService extends BaseService
{
    public ClientAccount $clientAccount;

    /**
     * BaseClientAccountService constructor.
     * @param ClientAccount|int $clientAccount
     */
    public function __construct($clientAccount)
    {
        if(is_int($clientAccount)) {
            $clientAccount = ClientAccount::find($clientAccount);
        }

        $this->clientAccount = $clientAccount;
    }
}
