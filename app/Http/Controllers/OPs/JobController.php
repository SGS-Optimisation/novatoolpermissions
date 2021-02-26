<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Services\MySgs\Api\JobApi;
use App\Services\MySgs\Mapping\Mapper;
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

        foreach ($client->rules as $rule) {
            $matched = false;
            foreach ($rule->terms as $term) {

                if (Str::lower($term->name) === 'any') {
                    $matched = true;
                    continue;
                }

                if ($term->taxonomy->mapping) {
                    /**
                     * retrieve value from mysgs response with help of taxonomy
                     * some mapping logic here
                     */

                    $mysgsValue = Str::lower(Mapper::getMetaValue([
                        'jobVersionId' => $jobNumber
                    ], $term->taxonomy->mapping));

                    $termValue = Str::lower($term->name);

                    /**
                     * compare retrieved value with this term
                     */
                    if (Str::contains($termValue, $mysgsValue) || Str::contains($mysgsValue, $termValue)) {
                        $matched = true;
                    }

                    \Log::debug($mysgsValue . '===' . $term->name);
                }

            }
            \Log::debug('matched: '.(int) $matched);
            if ($matched) {
                $clientRules[] = $rule;
            }
        }

        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'jobNumber' => $jobNumber,
            'customerName' => $client->name,
            'rules' => $clientRules
        ]);
    }
}
