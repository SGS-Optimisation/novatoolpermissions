<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Repositories\RuleRepository;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->with(['teams', 'users'])->first();

        $search_term = $request->query('term');

        $ruleRepo = new RuleRepository($client_account);

        return $ruleRepo->all($search_term);
    }
}
