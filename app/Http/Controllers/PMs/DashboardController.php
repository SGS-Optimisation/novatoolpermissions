<?php


namespace App\Http\Controllers\PMs;


use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {

        /** @var ClientAccount $clientAccount */
        $client_account = $request->user()->currentTeam->clientAccount;

        //dd($client_account->rules);

        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rules' => $client_account->rules ?? [],
        ]);
    }
}
