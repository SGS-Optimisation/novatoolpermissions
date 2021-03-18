<?php


namespace App\Http\Controllers\PMs;


use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class ClientAccountTaxonomyController extends Controller
{
    use TaxonomyBuilder;

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @param  string  $client_account
     */
    public function show(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        return Jetstream::inertia()->render($request, 'ClientAccount/Configuration', array_merge([
            'team' => $request->user()->currentTeam,
        ],
            $this->buildTaxonomyWithUsage($client_account)
        ));
    }
}
