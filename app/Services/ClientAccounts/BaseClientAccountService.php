<?php


namespace App\Services\ClientAccounts;


use App\Services\BaseService;
use App\Models\ClientAccount;

abstract class BaseClientAccountService extends BaseService
{
    public ClientAccount $clientAccount;

    public function __construct(ClientAccount $clientAccount)
    {
        $this->clientAccount = $clientAccount;
    }
}
