<?php

namespace App\Http\Requests\Teams;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'team_data' => 'sometimes|array|required',
            'team_data.name' => 'sometimes|string|required',
            'user_ids' => 'sometimes|array|required',
            'user_ids.*' => 'sometimes|int|required',
        ];
    }
}
