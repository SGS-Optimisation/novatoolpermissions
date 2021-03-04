<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;

class TermController extends Controller
{

    /**
     * Update a term.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        Cache::tags(['taxonomy'])->clear();

        return back();
    }

    /**
     * Update a term.
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $term = Term::find($id);
        $term->update(['name' => $request->name]);

        Cache::tags(['taxonomy'])->clear();

        return back();
    }

    /**
     * Delete a term.
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $term = Term::find($id);
        $term->delete();

        Cache::tags(['taxonomy'])->clear();

        return back();
    }
}
