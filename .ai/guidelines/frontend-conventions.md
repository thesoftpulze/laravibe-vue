# Frontend Conventions for AI Agents

## Scope

Use this guide when creating or modifying Vue pages, layouts, components, composables, or frontend types. It overrides the generic Inertia guidance about `resources/js/Pages` and `Inertia::render()`.

## Directory Rules

- `resources/` is the frontend root and the `@/` alias. There is no `resources/js`; never create it.
- Inertia pages live in `resources/views`, grouped by area (`auth/`, `account/`, `administration/`). The page name passed to `vue()` is the path under `resources/views` without `.vue`, e.g. `vue('account/Profile')`.
- Page-only partials live next to their page in a `partial/` folder.
- Layouts live in `resources/layouts`, shared components in `resources/components`, composables in `resources/composables`, and helpers in `resources/lib`.
- Blade templates (the root view and mail) live in `resources/blades`, not `resources/views`.
- Do not edit generated shadcn-vue components in `resources/components/ui` unless the change is intended for every use.

## Page Rules

- Render pages from controllers with `vue($name, $props, new PageMeta(...))`. Set at least `title`; use `heading`, `subheading` and `breadcrumbs` (a collection of `BreadCrumb`) when the layout shows them.
- Read page meta in Vue with `usePageMeta()`, not `usePage().props.meta`.
- Default layouts are resolved by page name in `resources/lib/helpers.ts`: `administration/*` gets `AdministrationLayout`, `auth/*` gets `AuthLayout`, everything else gets `GuestLayout`. Wrap section-specific layouts (such as `AccountLayout` or `AdministrationSettingsLayout`) inside the page template.
- Declare props with `defineProps<Props>()` and type them from `resources/types`.

## Routing Rules

- Link and submit to backend routes through Wayfinder imports from `@/routes` or `@/actions`. Never hardcode URLs.
- Wayfinder output (`resources/actions`, `resources/routes`, `resources/wayfinder`) is generated and gitignored. The app overrides `wayfinder:generate` to write to `resources/`, so never pass `--path`.

## Type Rules

- Keep model shapes in `resources/types/models.d.ts`, DTO and page-meta shapes in `resources/types/data.d.ts`, and enum shapes in `resources/types/enums.d.ts`.
- Each interface must mirror what its backend resource or DTO serializes. Update both in the same change.
- Use `Nullable<T>` for nullable fields.

## Date Rules

- Format API date-times in the browser with `formatDate()` or `formatRelativeDate()` from `@/lib/helpers`, so they follow the viewer's timezone.
