<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldMapping extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\FieldMapping::class;

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
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('Id'), 'id')
                ->rules('required')
                ->sortable()
            ,
            BelongsTo::make('Taxonomy')
                ->rules('required')
                ->searchable()
                ->sortable()
            ,
            Select::make(__('Api Name'), 'api_name')
                ->options([
                    'JobApi' => 'Job',
                    'ProductionApi' => 'Production',
                    'CustomerApi' => 'Customer',
                ])
                ->rules('required')
                ->sortable()
            ,
            Select::make(__('Api Version'), 'api_version')
                ->options([
                    '1.0' => 'v1.0',
                ])
                ->default('1.0')
                ->sortable()
            ,
            Text::make(__('Api Action'), 'api_action')
                ->rules('required')
                ->sortable()
            ,
            Text::make(__('Field Path'), 'field_path')
                ->rules('required')
                ->sortable()
            ,
            Text::make(__('Resolver Name'), 'resolver_name')
                ->sortable()
            ,
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
