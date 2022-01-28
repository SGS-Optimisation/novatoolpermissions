<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRoleWithPermission('manageTeams')
            || $this->user()->hasRoleWithPermission('createTeamsOnBehalfOfUsers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'region' => 'required',
            'owner_id' => 'required',
            'client_account_id' => 'required',
        ];
    }
}
