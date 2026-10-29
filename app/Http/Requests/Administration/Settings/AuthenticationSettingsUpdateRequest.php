<?php

declare(strict_types=1);

namespace App\Http\Requests\Administration\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Override;

final class AuthenticationSettingsUpdateRequest extends FormRequest
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
            'allowRegistration' => ['required', 'boolean'],
            'requireEmailVerification' => ['required', 'boolean'],
        ];
    }

    /**
     * Treat an unchecked checkbox, which the form leaves out, as false.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'allowRegistration' => $this->boolean('allowRegistration'),
            'requireEmailVerification' => $this->boolean('requireEmailVerification'),
        ]);
    }
}
