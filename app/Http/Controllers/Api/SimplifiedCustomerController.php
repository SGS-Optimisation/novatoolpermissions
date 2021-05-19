<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MySgs\WarehousedData\Customers\CustomerAliasesGrouping;
use Illuminate\Http\Request;

class SimplifiedCustomerController extends Controller
{

    public function index(Request $request)
    {
        $data = CustomerAliasesGrouping::search($request->get('query'));
        $results = [];

        foreach($data as $datum => $items) {
            $results[] = ['name' => $datum, 'aliases' => $items ];
        }

        return response()->json([
            'results' => $results
        ]);
    }
}
