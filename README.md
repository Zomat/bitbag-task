# Order Admin Note

Adds a single free-text note per order, manageable only from the admin panel, with a 500-character limit.

## What was built

-   **Entity**: `App\Entity\Order\Order::$adminNote` (nullable `TEXT` column `admin_note` on `sylius_order`), behind a dedicated `App\Entity\Order\AdminNoteAwareInterface` (`getAdminNote()` / `setAdminNote()` / `hasAdminNote()` / `MAX_ADMIN_NOTE_LENGTH`). `setAdminNote()` trims whitespace and normalizes an empty/blank string to `null`.
-   **Validation**: `Length` constraint (max 500) declared in `config/validator/Order.yaml`, scoped to a dedicated `app_order_admin_note` validation group so it never interferes with unrelated `Order` validation.
-   **Form**: `App\Form\Type\Order\OrderAdminNoteType`, a single-field form bound directly to the `Order` entity.
-   **Routes / controller**: two REST-ish, method-based routes on the same path, handled by `App\Controller\Admin\OrderAdminNoteController`:
    -   `PUT /admin/orders/{id}/admin-note` → `update()` — validates and saves the note.
    -   `DELETE /admin/orders/{id}/admin-note` → `remove()` — clears the note (no form; CSRF checked manually since there's no data to bind).
    -   Both submit through the standard Symfony `_method` override, matching how Sylius itself submits its own PUT-based admin actions.
-   **Use case / persistence**: `App\Command\UpdateOrderAdminNoteCommand` + `App\Command\UpdateOrderAdminNoteHandler` — the only place that mutates and flushes the entity. Keeps the controller a thin HTTP-to-use-case adapter.
-   **UI**: a "Admin note" card injected into the order show page in the admin panel via a Twig Hook (`sylius_admin.order.show.content.sections#right`), rendered by `App\Twig\OrderAdminNoteFormExtension` (builds the `FormView` for that hook, since no controller normally supplies one there) and `templates/admin/order/component/admin_note_form.html.twig`.
-   **UX detail**: on a failed validation (note too long), the submitted value and errors are flashed for one redirect cycle so the textarea re-renders with the rejected text and Bootstrap's `is-invalid` styling — a classic post/redirect/get pattern, no JavaScript required.

## Admin-only visibility

The note is **only** reachable through the admin controller/template above. It is not part of:

-   any shop-facing Twig template or the checkout `OrderType`,
-   any API Platform serialization group (admin or shop) — properties are opt-in there, not auto-exposed,
-   customer emails or PDF/invoice generation.

## Tests

```bash
docker compose exec php vendor/bin/phpunit tests/Unit
```

Covers: entity note normalization, the update/remove use-case handler (with mocked repository/entity manager), and form-level validation (valid note, >500 chars rejected, exactly 500 accepted, empty note clears an existing one).

## Known technical debt / possible improvements

-   No fine-grained permission check — any logged-in admin can edit/remove the note, not just a specific role.
-   No automated end-to-end test — only unit tests; the HTTP flow was verified manually (no test DB configured).
-   `migrations/Version20260925101325.php` is unrelated diff noise (messenger table) and should be split out.
-   No audit trail (who/when changed the note).
-   No Behat coverage, only PHPUnit.
-   A few CSRF/flash-key string literals are duplicated instead of centralized as constants.
