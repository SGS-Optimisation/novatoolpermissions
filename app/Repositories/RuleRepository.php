<?php


namespace App\Repositories;


use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\Term;
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

        logger('built rule repo for ' . $client_account->name);
    }

    public function all($search_term = null)
    {
        $cacheTag = 'rules-'.$this->client_account->slug;
        $tags = ['rules', $this->client_account->slug];

        $term = null;
        if ($search_term) {
            $term = Term::find($search_term);
            $cacheTag .= '-'.optional($term)->name;
        }

        return Cache::tags($tags)->remember($cacheTag, 3600, function () use ($term) {

            $rules = $this->client_account->rules()->with('terms.taxonomy')->withCount('terms');

            if($term){
                $rules = $rules->whereHas('terms', function ($query) use ($term) {
                    return $query->where('id', '=', $term->id);
                });
            }

            $rules = $rules->get()->each(function ($rule) {
                $rule->content = str_replace('<img', '<img loading="lazy"', $rule->content);

                if($rule->terms_count === 0) {
                    $term = (new Term(['name' => 'No term', 'taxonomy_id' => 0,
                        'taxonomy' => new Taxonomy(['name' => 'No category'])]));
                    $rule->terms->add($term);
                }
            });

            return $rules;
        });
    }
}
