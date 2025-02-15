<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreFundRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'name' => 'required',
            'start_year' => 'required|numeric|min:1800|max:'.Carbon::now()->year,
            'fund_manager_id' => 'required|numeric|exists:companies,id',
            'aliases.*.name' => 'not_in:'.$request->name,
            'portfolio.*.id' => 'exists:companies,id',
        ];
    }
}
