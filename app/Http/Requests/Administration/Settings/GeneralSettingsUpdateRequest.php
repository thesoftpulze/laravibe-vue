<?php

declare(strict_types=1);

namespace App\Http\Requests\Administration\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class GeneralSettingsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manageSettings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siteName' => ['nullable', 'string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'supportEmail' => ['nullable', 'string', 'email', 'max:255'],
        ];
    }
}
