<?php

namespace App\Nova\Actions;

use App\Models\Taxonomy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class EnablePmElements extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $pmsection = Taxonomy::whereName('PM Section Elements')->first();
        logger('pm section has id ' . $pmsection->id);

        foreach($models as $model) {
            Artisan::call('account:add:taxonomy', [
                'accountId' => $model->id,
                'taxonomyId' => $pmsection->id,
            ]);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
