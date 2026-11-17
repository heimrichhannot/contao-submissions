# Read-only additional submission data

Approved design: conversation, read-only section in edit form; labels and selected option labels snapshotted at submission; legacy data remains readable.

1. Capture validated Widget references per form; snapshot current labels/types/selected options into reserved nullable JSON additionalDataMeta after prepareFormData. Keep additionalData wire format unchanged. Include metadata for nested/duplicated fields.
2. Add internal metadata DCA and callback-only additionalDataView, with a conditional legend after submission_legend and before publication. Replace the user-authorized temporary textarea integration.
3. Build a recursive display model and render through namespaced Twig with escaping, list/table/group layout and serialization support. Use saved option labels and legacy humanized keys. Resolve file UUIDs to names without exposing public download URLs. Handle malformed JSON visibly and depth-bound rendering.
4. Run native metadata/actual Twig rendering tests, existing suites on Contao5.3/DBAL3 and5.7/DBAL4 in jzm, PHPStan/ECS; verify backend rendering in jzm. Review, fix valid findings, commit/push and update PR10. Existing unrelated Rector failure remains documented.

Success: no editable controls in additional-data output; no raw HTML/insert-tag execution; snapshots survive changes in source widget configuration; groups, serialized arrays, old data and selected labels render intelligibly. No extra value transformations for existing DCA fields.
