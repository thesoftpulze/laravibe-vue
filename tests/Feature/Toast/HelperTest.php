<?php

declare(strict_types=1);

use App\Enums\ToastActionType;
use App\Enums\ToastType;
use App\Toast\DTOs\ToastActionPayload;

beforeEach(fn () => Session::flush());

describe('toastActionCopy', function () {
    it('creates a toast action with type of copy, payload and label', function () {
        expect(toastActionCopy('text-to-copy', 'Copy Text'))
            ->toBeInstanceOf(ToastActionPayload::class)
            ->type->toBe(ToastActionType::COPY)
            ->payload->toBe('text-to-copy')
            ->label->toBe('Copy Text');
    });

    it('creates a toast action with type of copy, payload only', function () {
        expect(toastActionCopy('copy-this'))
            ->type->toBe(ToastActionType::COPY)
            ->payload->toBe('copy-this')
            ->label->toBeNull();
    });
});

describe('toastActionRedirect', function () {
    it('creates a toast action with type of redirect, URL and label', function () {
        expect(toastActionRedirect('https://example.com', 'Example Label'))
            ->toBeInstanceOf(ToastActionPayload::class)
            ->type->toBe(ToastActionType::REDIRECT)
            ->payload->toBe('https://example.com')
            ->label->toBe('Example Label');
    });

    it('creates a redirect action with URL only', function () {
        expect(toastActionRedirect('https://example.com'))
            ->type->toBe(ToastActionType::REDIRECT)
            ->payload->toBe('https://example.com')
            ->label->toBeNull();
    });
});

describe('toastActionVisit', function () {
    it('creates a visit action with URL and label', function () {
        expect(toastActionVisit('/account', 'View'))
            ->type->toBe(ToastActionType::VISIT)
            ->payload->toBe('/account')
            ->label->toBe('View');
    });
});

describe('toastError', function () {
    it('creates an error toast message', function () {
        toastError('An error occurred');

        $toast = toast()->pull();
        expect($toast)
            ->toBeArray()
            ->toHaveCount(1)
            ->and($toast[0])
            ->toHaveKey('type', ToastType::ERROR->value)
            ->toHaveKey('message', 'An error occurred');
    });

    it('creates an error toast message with actions', function () {
        toastError('Something went wrong', toastActionCopy('error-123', 'Copy Error ID'), toastActionRedirect('/support', 'Get Help'));

        $toast = toast()->pull();
        expect($toast[0])->toHaveKey('actions')
            ->and($toast[0]['actions'])->toHaveCount(2)
            ->and($toast[0]['actions'][0])
            ->toHaveKey('type', ToastActionType::COPY->value)
            ->toHaveKey('payload', 'error-123')
            ->toHaveKey('label', 'Copy Error ID')
            ->and($toast[0]['actions'][1])
            ->toHaveKey('type', ToastActionType::REDIRECT->value)
            ->toHaveKey('payload', '/support')
            ->toHaveKey('label', 'Get Help');
    });
});

describe('toastSuccess', function () {
    it('creates a success toast message', function () {
        toastSuccess('Operation completed successfully');

        $toast = toast()->pull();
        expect($toast)
            ->toBeArray()
            ->toHaveCount(1)
            ->and($toast[0])
            ->toHaveKey('type', ToastType::SUCCESS->value)
            ->toHaveKey('message', 'Operation completed successfully');
    });

    it('returns a builder for adding more details to the toast', function () {
        toastSuccess('Saved', toastActionCopy('ref-1'))->description('Reference ref-1.');

        expect(toast()->pull()[0])
            ->toHaveKey('description', 'Reference ref-1.')
            ->toHaveKey('actions', [['type' => 'copy', 'payload' => 'ref-1', 'label' => null]]);
    });
});

describe('toastWarning', function () {
    it('creates a warning toast message', function () {
        toastWarning('Please verify your email');

        expect(toast()->pull()[0])
            ->toHaveKey('type', ToastType::WARNING->value)
            ->toHaveKey('message', 'Please verify your email');
    });
});

describe('toastInfo', function () {
    it('creates an info toast message', function () {
        toastInfo('New features available');

        expect(toast()->pull()[0])
            ->toHaveKey('type', ToastType::INFO->value)
            ->toHaveKey('message', 'New features available');
    });
});
