<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;


class Taxonomy extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Taxonomy::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'name'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Taxonomies');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Taxonomy');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('Id'), 'id')
                ->rules('required')
                ->sortable()
            ,
            BelongsTo::make(__('Taxonomy'), 'parent')
                ->searchable()
                ->singularLabel('Parent Taxonomy')
                ->sortable()
            ,
            HasMany::make('Taxonomies', 'taxonomies')
                ->sortable()
            ,
            Text::make(__('Name'), 'name')
                ->rules('required')
                ->sortable()
            ,
            Code::make(__('Config'), 'config')
                ->sortable()
            ,
            BelongsToMany::make('Client Accounts')
            ,
            HasMany::make('Terms', 'terms')
            ,
            HasOne::make('Field Mapping', 'mapping')
            ,
            //Stack::make('Stacked Terms', $terms_stack),
            Stack::make('Taxonomies', (function(){
                $stack_items = [];
                foreach($this->taxonomies as $vocabulary) {
                    $stack_items[] = Line::make('Anonymous')->resolveUsing(function() use ($vocabulary){
                        return $vocabulary->name;
                    })->asSmall();
                }
                return $stack_items;
            })()),

            Stack::make('Terms', (function(){
                $terms_stack = [];
                foreach($this->terms as $term) {
                    $terms_stack[] = Line::make('Anonymous')->resolveUsing(function() use ($term){
                        return $term->name;
                    })->asSmall();
                }
                return $terms_stack;
            })()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
