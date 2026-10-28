<?php

declare(strict_types=1);

if (! function_exists('toast')) {
    function toast(): App\Toast\Toast
    {
        return resolve(App\Toast\Toast::class);
    }
}

if (! function_exists('toastError')) {
    function toastError(string $message, App\Toast\DTOs\ToastActionPayload ...$actions): App\Toast\PendingToast
    {
        return toast()->error($message)->action(...$actions);
    }
}

if (! function_exists('toastSuccess')) {
    function toastSuccess(string $message, App\Toast\DTOs\ToastActionPayload ...$actions): App\Toast\PendingToast
    {
        return toast()->success($message)->action(...$actions);
    }
}

if (! function_exists('toastWarning')) {
    function toastWarning(string $message, App\Toast\DTOs\ToastActionPayload ...$actions): App\Toast\PendingToast
    {
        return toast()->warning($message)->action(...$actions);
    }
}

if (! function_exists('toastInfo')) {
    function toastInfo(string $message, App\Toast\DTOs\ToastActionPayload ...$actions): App\Toast\PendingToast
    {
        return toast()->info($message)->action(...$actions);
    }
}

if (! function_exists('toastActionCopy')) {
    function toastActionCopy(string $payloadToCopy, ?string $label = null): App\Toast\DTOs\ToastActionPayload
    {
        return App\Toast\DTOs\ToastActionPayload::copy($payloadToCopy, $label);
    }
}

if (! function_exists('toastActionRedirect')) {
    function toastActionRedirect(string $redirectURL, ?string $label = null): App\Toast\DTOs\ToastActionPayload
    {
        return App\Toast\DTOs\ToastActionPayload::redirect($redirectURL, $label);
    }
}

if (! function_exists('toastActionVisit')) {
    function toastActionVisit(string $url, ?string $label = null): App\Toast\DTOs\ToastActionPayload
    {
        return App\Toast\DTOs\ToastActionPayload::visit($url, $label);
    }
}
