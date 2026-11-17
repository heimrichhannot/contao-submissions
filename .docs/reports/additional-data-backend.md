# Additional submission data backend verification

Runtime: DDEV project jzm, PHP 8.4.27. Package tests run in isolated dependency copies.

- Contao 5.3 / DBAL 3: 20 PHPUnit tests, 74 assertions pass.
- Contao 5.7 / DBAL 4: 20 PHPUnit tests, 74 assertions pass.
- Real FormSelect widgets cover dynamic option changes, selected-only captions, multiple-selection name normalization and snapshots for duplicated widgets no longer present as flat values.
- Real Twig renders field captions, selected labels, serialized lists, repeated rows, upload-name snapshots, multiline values, legacy data, malformed metadata and malformed JSON. Stored HTML is escaped, insert tags remain literal text, and no editable input/textarea is generated.
- jzm kernel boot + actual registered input-field callback + Contao Twig namespace loader: pass. German legend resolves correctly.
- Real SubmissionModel/SubmissionArchiveModel records in connection-local temporary copies of the schema verify section placement after regular fields and before publication, plus omission on empty records. No existing submission records were changed.
- Only the nullable additionalDataMeta column was added to jzm for future test submissions. The migration dry-run also listed unrelated pre-existing schema changes; these were not applied.
- Browser UI host is unavailable, so no visual screenshot validation was possible.
- Codex review findings concerning Contao 5.3 magic option access and []-suffixed select names were fixed and covered by real-widget regression assertions. Follow-up Codex review: no actionable findings.
- Existing Rector configuration failure is unrelated and remains documented in the PR.

Final review follow-up:
- Claude Opus 5.5 high findings on encoded text, deprecated activeRecord access and standard field permissions were validated. Real Contao Input + Twig regression tests reproduced the visible-entity bug; the fix decodes raw values before Twig escapes them, preserving encoded option keys for lookup.
- New callback uses getCurrentRecord(), supported since Contao 5.3, instead of activeRecord (deprecated since 5.0, removal in 6).
- Empty option captions do not masquerade as answers; upload fallback captions are normalized consistently.
- The display field explicitly keeps standard excluded-field permissions. README explains granting tl_submission::additionalDataView to non-admin editors. No user/group grants were changed.
- Final Codex review after these fixes: no actionable correctness issues. PHPStan both dependency combinations and ECS pass.
- jzm still reports JSON normalization alongside pre-existing core schema diffs in migration dry-run. No full migration run or unrelated drops were applied.
