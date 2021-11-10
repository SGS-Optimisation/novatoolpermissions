<?php


namespace App\Services\Term;


use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreateTermService
{

    public Term $term;

    public function __construct(
        public string|int|ClientAccount $client,
        public string|int|Taxonomy $taxonomy,
        public string $term_name,
        public bool $auto_aliasing = false
    ) {

        if (is_numeric($this->client)) {
            $this->client = ClientAccount::find($client);
        } else {
            if (is_string($this->client)) {
                $this->client = ClientAccount::where('name', $client)->first();
            }
        }

        if (is_numeric($this->taxonomy)) {
            $this->taxonomy = Taxonomy::find($taxonomy);
        } else {
            if (is_string($this->taxonomy)) {
                $this->taxonomy = Taxonomy::where('name', $taxonomy)->first();
            }
        }
    }

    public function handle($sync = true, $clear_cache = true)
    {
        /** @var Term $term */
        $term = $this->taxonomy->terms()->withTrashed()->firstOrCreate(['name' => $this->term_name]);

        $restored = false;
        if ($term->deleted_at) {
            $term->restore();
            $restored = true;
        }

        if ($sync) {
            $this->client->terms()->syncWithoutDetaching($term);
        }

        $this->term = $term;

        if (!$restored && $this->auto_aliasing) {
            $this->makeAliases();
        }

        if ($clear_cache) {
            Cache::tags(['taxonomy'])->forget($this->client->slug.'-taxonomy-usage-data');
            Cache::tags(['taxonomy'])->forget($this->client->slug.'-rules-data');
        }

        return [$term, $restored];
    }

    public function makeAliases(): void
    {
        $name = trim($this->term->name);
        $term_config = $this->term->config;

        $aliases = [];

        /*
         * Start cleanup
         */
        $name = preg_replace('!\s+!', ' ', $name); // replace multiple spaces by single space

        if (Str::contains($name, '-/')) {
            $name = Str::replace('-/', '/', $name);
        }
        if (Str::contains($name, '/-')) {
            $name = Str::replace('/-', '/', $name);
        }

        /*
         * Variations of "A / B", "A/B", "A B"
         */
        if (Str::contains($name, '/')) {
            $parts = array_map('trim', explode('/', $name));

            $aliases[] = join(' / ', $parts);
            $aliases[] = join('/', $parts);
            $aliases[] = join(' ', $parts);
        }

        $term_config['aliases'] = $aliases;
        $this->term->config = $term_config;
        $this->term->save();

    }
}
