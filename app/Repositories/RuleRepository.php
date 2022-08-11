<?php


namespace App\Repositories;


use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RuleRepository
{
    public $client_account;

    public function __construct($client_account)
    {
        if (is_int($client_account)) {
            $this->client_account = ClientAccount::find($client_account);
        } elseif (is_string($client_account)) {
            $this->client_account = ClientAccount::whereSlug($client_account)->first();
        } else {
            $this->client_account = $client_account;
        }

    }

    /**
     * @param $search_term
     * @return Collection
     */
    public function all($search_term = null)
    {
        $cacheTag = 'rules-'.$this->client_account->slug;
        $tags = ['rules', $this->client_account->slug];

        $term = null;
        if ($search_term) {
            $term = Term::find($search_term);
            $cacheTag .= '-'.optional($term)->name;
        }

        $cache_duration = app()->environment('production') ? 60 * 60 * 24 : 60 * 60 * 24 * 30;

        /**
         *
         */
        return Cache::tags($tags)
            ->remember($cacheTag, $cache_duration, function () use ($term) {

                $rules_query = Rule::forClient($this->client_account)
                    ->with(['terms.taxonomy', 'users', 'teams', 'attachments'])
                    ->withCount('terms');

                if ($term) {
                    $rules_query->whereHas('terms', function ($query) use ($term) {
                        return $query->where('terms.id', '=', $term->id);
                    });
                }

                logger('built rule repo for '.$this->client_account->name);

                $rules = $rules_query->get()->each(function ($rule) {
                    $rule->content = str_replace('<img', '<img loading="lazy"', $rule->content);

                    if ($rule->terms_count == 0) {
                        $term = (new Term([
                            'name' => 'No term', 'taxonomy_id' => 0,
                            'taxonomy' => new Taxonomy(['name' => 'No category'])
                        ]));
                        $rule->terms->add($term);
                    }
                });

                return $rules->toJson();
            });
    }
}
