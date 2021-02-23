<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRuleRequest;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Services\ClientAccounts\BuildTaxonomyLists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;

class RuleController extends Controller
{

    protected function buildData($request, $client_account_slug)
    {
        return Cache::remember(
            $client_account_slug.'-rules-data',
            3600,
            function () use ($request, $client_account_slug) {
                $client_account = ClientAccount::whereSlug($client_account_slug)->first();

                $taxonomy_builder = (new BuildTaxonomyLists($client_account))->handle();

                return [
                    'clientAccount' => $client_account,
                    'taxonomyHierarchy' => $taxonomy_builder->taxonomy_hierarchy,
                    'topTaxonomies' => $taxonomy_builder->top_taxonomies,
                ];
            });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function create(Request $request, $client_account_slug)
    {
        return Jetstream::inertia()->render($request, 'ClientAccount/CreateRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => new Rule(['content' => '<p>taki taki</p>'])
        ],
            $this->buildData($request, $client_account_slug)
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateRuleRequest $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rule_fields = $request->only(['name', 'content', 'flagged', 'metadata']);
        $rule = $client_account->rules()->create($rule_fields);

        Cache::tags(['rules'])->clear();

        logger('rule added: ' . $rule->id);

        return $request->wantsJson()
            ? new JsonResponse(['id' => $rule->id], 200)
            : redirect(route('rules.edit', [$client_account_slug, $rule->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $client_account_slug, $id)
    {

        return Jetstream::inertia()->render($request, 'ClientAccount/EditRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => Rule::find($id),
        ],
            $this->buildData($request, $client_account_slug)
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $client_account_slug, $id)
    {
        $rule_fields = $request->only(['name', 'content', 'flagged', 'metadata']);

        $rule = Rule::find($id);
        $rule->update($rule_fields);

        Cache::tags(['rules'])->clear();

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
