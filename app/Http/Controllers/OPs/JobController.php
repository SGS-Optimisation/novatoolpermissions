<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Services\MySgs\Api\JobApi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class JobController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $jobNumber
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(Request $request, $jobNumber = null)
    {

        $jobDetails = JobApi::basicDetails($jobNumber);

        $client = ClientAccount::whereRaw('LOWER(alias) LIKE "%' . Str::lower($jobDetails->retailer->customerName) . '%"')->first();

        $clientRules = [];

        $client->rules->each(function ($rule) use ($clientRules) {
            $matched = false;
            foreach ($rule->terms as $term) {
                if ('Account Structure' === trim($term->taxonomy->parent->name)) {
                    /**
                     * retrieve value from mysgs response with help of taxonomy
                     * some mapping logic here
                     */
                    $term->taxonomy->name;

                    /**
                     * compare retrieved value with this term
                     */
                    $term->name;

                    if ($term->taxonomy->name === $term->name) {
                        $matched = true;
                    }
                }
            }
            if ($matched) {
                $clientRules[] = $rule;
            }
        });

        //\Log::debug(print_r($clientRules, true));

        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'jobNumber' => $jobNumber,
            'customerName' => $client->name,
            'rules' => []
        ]);
    }
}
