<?php

declare(strict_types=1);

namespace App\Toast\Exceptions;

use App\Enums\ToastType;
use App\Toast\DTOs\ToastActionPayload;
use App\Toast\DTOs\ToastPayload;
use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

final class ToastException extends Exception implements ShouldntReport
{
    /**
     * @param  array<int, ToastActionPayload>  $actions
     */
    public function __construct(
        string $message = '',
        private readonly ToastType $type = ToastType::ERROR,
        int $code = 0,
        ?Throwable $previous = null,
        private readonly ?string $description = null,
        private readonly array $actions = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): RedirectResponse
    {
        toast()->append(new ToastPayload(
            type: $this->type,
            message: $this->getMessage(),
            actions: $this->actions !== [] ? array_values($this->actions) : null,
            description: $this->description,
        ));

        return back();
    }
}
