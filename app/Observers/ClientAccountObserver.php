<?php

namespace App\Observers;

use App\Models\ClientAccount;
use App\Services\ClientAccounts\AssociateDefaultVocabulary;

class ClientAccountObserver
{
    /**
     * Handle the ClientAccount "created" event.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return void
     */
    public function created(ClientAccount $clientAccount)
    {
        (new AssociateDefaultVocabulary($clientAccount))->handle();
    }

    /**
     * Handle the ClientAccount "updated" event.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return void
     */
    public function updated(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the ClientAccount "deleted" event.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return void
     */
    public function deleted(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the ClientAccount "restored" event.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return void
     */
    public function restored(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the ClientAccount "force deleted" event.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return void
     */
    public function forceDeleted(ClientAccount $clientAccount)
    {
        //
    }
}
