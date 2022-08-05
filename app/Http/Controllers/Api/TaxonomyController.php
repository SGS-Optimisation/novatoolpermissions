<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    use TaxonomyBuilder;

    public function show(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        return $this->buildTaxonomyWithUsage($client_account);
    }
}
