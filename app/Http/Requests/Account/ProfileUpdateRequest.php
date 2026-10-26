<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

final class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Unique|ValidationRule|array<mixed>|string>>
     */
    public function rules(): array
    {
        return $this->profileRules(authUser()->id);
    }
}
