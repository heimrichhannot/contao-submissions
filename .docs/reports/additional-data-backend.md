# Additional submission data backend verification

Runtime: DDEV project jzm, PHP 8.4.27. Package tests run in isolated dependency copies.

- Contao 5.3 / DBAL 3: 20 PHPUnit tests, 74 assertions pass.
- Contao 5.7 / DBAL 4: 20 PHPUnit tests, 74 assertions pass.
- Real FormSelect widgets cover dynamic option changes, selected-only captions, multiple-selection name normalization and snapshots for duplicated widgets no longer present as flat values.
- Real Twig renders field captions, selected labels, serialized lists, repeated rows, upload-name snapshots, multiline values, legacy data, malformed metadata and malformed JSON. Stored HTML is escaped, insert tags remain literal text, and no editable input/textarea is generated.
- jzm kernel boot + actual registered input-field callback + Contao Twig namespace loader: pass. German legend resolves correctly.
- Real SubmissionModel/SubmissionArchiveModel records in connection-local temporary copies of the schema verify section placement after regular fields and before publication, plus omission on empty records. No existing submission records were changed.
- Only the nullable additionalDataMeta column was added to jzm for future test submissions. The migration dry-run also listed unrelated pre-existing schema changes; these were not applied.
- Firefox browser validation completed on 2026-11-17 using Playwright MCP 0.0.85 and its matching Firefox 159 build in a separate persistent test profile, after manual backend login.
- Live backend screenshots at 1280 × 960 and 390 × 844 verify the section after regular fields and before status. Existing populated and empty test submissions verify legacy fallback labels and omission of the empty section.
- A temporary synthetic submission verifies submission-time field/option captions, multiple selections, repeated rows with column captions, upload filename snapshots, multiline text, decoded entities, literal HTML/insert tags, empty answers, booleans, zero and wrapping of long references. DOM inspection confirms zero editable controls, script elements or download links in the section; the browser console reports no errors.
- The synthetic submission was deleted after verification (one matching row removed, zero fixture rows remaining). Existing submissions were only read. Screenshots remain local because some contain existing test-record data; none were published with this report.
- Codex review findings concerning Contao 5.3 magic option access and []-suffixed select names were fixed and covered by real-widget regression assertions. Follow-up Codex review: no actionable findings.
- Existing Rector configuration failure is unrelated and remains documented in the PR.

Final review follow-up:
- Claude Opus 5.5 high findings on encoded text, deprecated activeRecord access and standard field permissions were validated. Real Contao Input + Twig regression tests reproduced the visible-entity bug; the fix decodes raw values before Twig escapes them, preserving encoded option keys for lookup.
- New callback uses getCurrentRecord(), supported since Contao 5.3, instead of activeRecord (deprecated since 5.0, removal in 6).
- Empty option captions do not masquerade as answers; upload fallback captions are normalized consistently.
- The display field explicitly keeps standard excluded-field permissions. README explains granting tl_submission::additionalDataView to non-admin editors. No user/group grants were changed.
- Final Codex review after these fixes: no actionable correctness issues. PHPStan both dependency combinations and ECS pass.
- jzm still reports JSON normalization alongside pre-existing core schema diffs in migration dry-run. No full migration run or unrelated drops were applied.
- CI run 44065448543 was checked after browser validation: both PHPStan/test matrix jobs and ECS pass; Rector still exits before analysis because the unchanged configuration references the missing DoctrineSetList::DOCTRINE_ORM_214 constant.

Native table styling follow-up (2026-11-17):
- Main and repeated-group tables now use Contao's tl_listing, with-border, with-padding and with-zebra styles instead of popup-detail table styling. These classes were verified in both Contao 5.3 and 5.7 sources; no stylesheet or dependency was added.
- Authenticated Firefox screenshots verify the native cell spacing, separators and alternating row colors at 1280 × 960 and 390 × 844, plus the actual Contao dark theme via media emulation. At 390 pixels, section clientWidth and scrollWidth both measure 378 pixels. DOM inspection confirms zero editable controls or scripts, and the browser console reports no errors.
- Both DDEV dependency suites were rerun with the final template: 20 tests / 74 assertions pass per suite. The temporary synthetic submission was removed afterwards (one matching row deleted, zero fixtures remaining); existing records were not saved.
