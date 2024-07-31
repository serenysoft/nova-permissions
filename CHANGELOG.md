# Release Notes

## 1.6.2 Under development

- Add setter methods for Role and Permission policies.
- Add cache for `permissions` and `users count` query on Role resource, improving index performance.
- Fixed an issue when listing the Roles when Preventing Lazy Loading is active.

## 1.6.1 (2024-05-06)

- Add namespace to seeders.
- Fix issue where permission menu item did not respect `disablePermissions`.

## 1.6.0 (2024-03-19)

- Added `disableMenu` to allow handling custom menu.

## 1.5.0 (2024-02-06)

- Fixed an issue where permission and role resources was not displayed in custom menu.

## 1.4.0 (2023-11-17)

- Added support for "spatie/laravel-permission" 6.0.

## 1.3.2 (2023-08-20)

- Fixed issue publishing `seeders`.

## v1.3.1 (2023-06-01)

## v1.3.0 (2023-04-27)

- Role resource now uses `permission.models.role` configuration as resource model.

## v1.2.4 (2023-03-05)

- Fixed an issue where permissions field were showing duplicated values.

## v1.2.3 (2023-02-28)

- Menu section now is collapsable.

## v1.2.2 (2023-02-18)

- Fixed deprecated static trait property.

## v1.2.1 (2023-01-26)

- The `isSuperAdmin` user method now is optional.
- Disabled permissions global search.
- Fixed an issue where permissions field were not showing correctly.

## v1.2.0 (2023-01-20)

- Added ability to customize role and permission resources.

## v1.1.0 (2022-12-19)

- Fixed issues with model for guard.
