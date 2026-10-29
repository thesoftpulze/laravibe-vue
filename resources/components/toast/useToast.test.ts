import { beforeEach, describe, expect, it, vi } from 'vite-plus/test'
import { registerToaster, unregisterToaster, useToast } from './useToast'

const { router, sonner } = vi.hoisted(() => ({
    router: { on: vi.fn() },
    sonner: { success: vi.fn(), error: vi.fn(), warning: vi.fn(), info: vi.fn(), loading: vi.fn(), dismiss: vi.fn() },
}))

vi.mock('@inertiajs/vue3', () => ({ router }))
vi.mock('vue-sonner', () => ({ toast: sonner }))
vi.mock('vue', () => ({ markRaw: (value: unknown) => value, createApp: () => ({ mount: vi.fn(), unmount: vi.fn() }) }))
vi.mock('./', () => ({ Toast: {}, ToastProvider: {} }))

beforeEach(() => {
    unregisterToaster()
    vi.clearAllMocks()
})

const invalidDurations = [-1, NaN, Infinity, -Infinity, null, '4000']
const validDurations = [undefined, 0, 1, 12.5, 7500]

describe('toast durations', () => {
    it.each(invalidDurations)('rejects invalid client durations: %s', (duration) => {
        expect(() => useToast().success('Saved', { duration: duration as number })).toThrow(RangeError)
        expect(sonner.success).not.toHaveBeenCalled()
    })

    it.each(validDurations)('preserves valid client durations: %s', (duration) => {
        useToast().success('Saved', { duration })
        expect(sonner.success).toHaveBeenCalledWith(
            expect.anything(),
            expect.objectContaining({ duration: duration === 0 ? Infinity : (duration ?? 4000) }),
        )
    })

    it('filters invalid flashed durations while keeping valid and omitted durations', () => {
        vi.stubGlobal('document', { createElement: () => ({ remove: vi.fn() }), body: { appendChild: vi.fn() } })
        try {
            registerToaster()
            const [event, onFlash] = router.on.mock.calls[0]!
            expect(event).toBe('flash')
            onFlash({
                detail: {
                    flash: {
                        toasts: [...invalidDurations, ...validDurations].map((duration) => ({ type: 'success', message: 'Saved', duration })),
                    },
                },
            })
            expect(sonner.success).toHaveBeenCalledTimes(validDurations.length)
            expect(sonner.success.mock.calls.map(([, options]) => options.duration)).toEqual([4000, Infinity, 1, 12.5, 7500])
        } finally {
            unregisterToaster()
            vi.unstubAllGlobals()
        }
    })
})

describe('promise toast settlement', () => {
    it('returns the resolved value when the success formatter throws', async () => {
        const data = { id: 42 }
        const success = vi.fn(() => {
            throw new Error('Formatter failure')
        })
        const error = vi.fn(() => 'Failed')
        await expect(useToast().promise(Promise.resolve(data), { loading: 'Saving', success, error })).resolves.toBe(data)
        expect(success).toHaveBeenCalledWith(data)
        expect(error).not.toHaveBeenCalled()
        expect(sonner.error).not.toHaveBeenCalled()
        expect(sonner.dismiss).toHaveBeenCalledTimes(1)
    })

    it.each([new Error('Original rejection'), undefined])('preserves the original rejection when the error formatter throws: %s', async (reason) => {
        const error = vi.fn(() => {
            throw new Error('Formatter failure')
        })
        await expect(useToast().promise(Promise.reject(reason), { loading: 'Saving', success: 'Saved', error })).rejects.toBe(reason)
        expect(error).toHaveBeenCalledWith(reason)
        expect(sonner.dismiss).toHaveBeenCalledTimes(1)
    })

    it('preserves synchronous factory failures', async () => {
        const reason = new Error('Factory failure')
        await expect(
            useToast().promise(
                () => {
                    throw reason
                },
                { loading: 'Saving', success: 'Saved', error: 'Failed' },
            ),
        ).rejects.toBe(reason)
        expect(sonner.error).toHaveBeenCalledWith(
            expect.anything(),
            expect.objectContaining({ componentProps: expect.objectContaining({ toast: { type: 'error', message: 'Failed' } }) }),
        )
    })

    it('replaces the loading toast with a formatted success and returns the original value', async () => {
        await expect(
            useToast().promise(() => Promise.resolve(42), { loading: 'Saving', success: (value) => `Saved ${value}`, error: 'Failed' }),
        ).resolves.toBe(42)
        expect(sonner.success).toHaveBeenCalledWith(
            expect.anything(),
            expect.objectContaining({ componentProps: expect.objectContaining({ toast: { type: 'success', message: 'Saved 42' } }) }),
        )
        expect(sonner.dismiss).toHaveBeenCalledWith(sonner.loading.mock.calls[0]![1].id)
    })
})
