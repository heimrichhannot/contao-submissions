# Direct additional-data display — 2026-11-17

The extra `additionalDataView` DCA field was unnecessary. Contao 5.3 and 5.7 both return the `input_field` callback output before widget creation, validation and saving in `DataContainer::row()`.

- The callback now targets `fields.additionalData.input_field`; its listener is named `FieldsAdditionalDataInputFieldListener`.
- The conditional edit palette contains `additionalData` itself. Its JSON SQL definition, reserved-name handling, `noSubmissionField` and `doNotShow` settings remain intact. At the user's subsequent request, `exclude` is explicitly false so every editor with record access sees the additional values.
- Removed the display-only DCA entry and its obsolete reserved-name handling. Translation keys and README now refer to `additionalData`.
- No separate `tl_submission::additionalData` grant is required. Existing record/archive access control remains in place; user/group grants were not changed. No schema or stored-data migration is required because the removed display field never had a database column.

Verification:

- A regression test runs the actual Contao row renderer and real Twig output with a forged POST value for `additionalData` and a denied field-permission checker. Stored output remains visible, injected output is absent, the storage value is unchanged and the save method is never invoked. Callback attribute targeting is checked too. The access check failed before setting `exclude` to false and passes afterwards.
- DDEV jzm: both Contao 5.3/DBAL3 and Contao 5.7/DBAL4 pass 23 tests / 90 assertions. Rector and PHPStan pass in both environments; ECS passes.
- The live jzm kernel resolves the actual registered callback on the JSON field and renders it through the Contao Twig loader. Real model records in connection-local temporary tables verify section placement and empty-record omission. No existing submission records or access grants were modified.

Earlier review reports describe the former display-only field as it existed at those dates; this follow-up supersedes that structure.
