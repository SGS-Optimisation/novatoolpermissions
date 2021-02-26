<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Services\MySgs\Api\JobApi;
use App\Services\MySgs\Mapping\Mapper;
use App\Services\Rule\Filter;
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
        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'jobNumber' => $jobNumber,
            'rules' => $jobNumber ? Filter::handle($jobNumber) : []
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $jobNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $jobNumber)
    {
        return response()->json(Filter::handle($jobNumber));
    }
}
