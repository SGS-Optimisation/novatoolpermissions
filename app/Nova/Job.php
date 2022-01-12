<?php

namespace App\Nova;

use App\Nova\Filters\DateFrom;
use App\Nova\Filters\DateRangeFilter;
use App\Nova\Filters\DateTo;
use dddeeemmmooonnn\NovaMulticolumnFilter\NovaMulticolumnFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Job extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Job::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'job_number',
        'metadata'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Job Number'), 'job_number')->sortable(),
            Code::make(__('Metadata'), 'metadata')
                ->json()
                ->sortable()
            ,
            Text::make('Client', function () {
                return isset($this->metadata->client) ? $this->metadata->client->name : '';
            })->onlyOnIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [

            (new DateFrom),
            (new DateTo),

            new NovaMulticolumnFilter([
                // Customizing all
                'designation' => [
                    'type' => 'text',
                    'label' => 'Designation',
                    'defaultOperator' => 'Contains',
                    'operators' => [
                        'LIKE' => 'Contains',
                        //...
                    ],
                    'placeholder' => 'Designation'
                ],
            ],
                $manual_update = false, // Apply filter with the button
                $default_column_type = 'text', // Default input type
                $name = 'Filter Content' // Filter name
            ),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
