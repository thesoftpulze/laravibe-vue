<?php

declare(strict_types=1);

namespace App\Http\Requests\Administration;

use App\Enums\SortDirection;
use App\Enums\UserRoleFilter;
use App\Enums\UserSortColumn;
use App\Enums\UserVerificationFilter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('accessAdministration');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::enum(UserRoleFilter::class)],
            'verification' => ['nullable', Rule::enum(UserVerificationFilter::class)],
            'sort' => ['nullable', Rule::enum(UserSortColumn::class)],
            'direction' => ['nullable', Rule::enum(SortDirection::class)],
        ];
    }
}
