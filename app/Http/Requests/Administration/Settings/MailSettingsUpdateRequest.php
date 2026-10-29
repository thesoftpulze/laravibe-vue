<?php

declare(strict_types=1);

namespace App\Http\Requests\Administration\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class MailSettingsUpdateRequest extends FormRequest
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
            'fromName' => ['nullable', 'string', 'max:100'],
            'fromAddress' => ['nullable', 'string', 'email', 'max:255'],
        ];
    }
}
