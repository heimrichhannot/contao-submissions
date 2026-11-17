# Claude Opus follow-up review — 2026-11-17

Reviewer: Claude Opus 5.5, CLI effort `high`, read-only tools.
Reviewed range: `cbc69c198918ec71022d2ff28b568e89fab9b4a6..44066a565f23965d9acf427f5781e3a831be0046`.
The CLI completed successfully and reported `claude-opus-5-5` as the actual model.
The fixes below were subsequently implemented and verified by Codex; Claude's original review predates these fixes.

## Findings and disposition

1. **High — duplicated fieldset rows lost before grouping. Valid, fixed.** The inspiredminds storage hook removes duplicate keys at priority 100, before the form-type storage event at priority 17. The integration now captures processed duplicate values at priority 101 and consumes that snapshot when grouping. Raw request data is not restored. A regression test invokes the actual inspiredminds hook against a connection-local temporary table; it first failed because the second row was absent and now preserves both rows. The live jzm kernel confirms the compiled hook order: capture, inspiredminds, form-type event, submission storage.
2. **Medium — nested zebra stripes overridden by an even outer row. Valid, fixed.** Reproduced in Firefox with the participant table in the second outer row: both inner row colors were identical. Nested cells now receive their own row colors, overriding the native descendant selectors while preserving native headers, borders and spacing. Alternating colors were verified after the fix.
3. **Low — list items inherit flex layout and lose spacing/hierarchy. Valid, fixed.** Firefox confirmed `display:flex`, zero indentation and zero gap between label and value. Lists now restore list-item layout, markers and indentation. After the fix: 17.5px indentation and a visible label/value gap, including nested lists.
4. **Low — date/time values displayed as raw timestamps. Valid, fixed.** New metadata includes the widget's `rgxp`; date/time/datetime timestamps render through real Contao date formatting with configured display formats. Regression tests first failed for missing metadata and raw timestamp output, then passed. Values and the additionalData wire format remain unchanged; old records without the metadata retain their fallback behavior.
5. **Optional — generated notification token values also stored as additional data. No change.** The approved behavior stores all values without a DCA field, including values added programmatically. Filtering to widget names would drop these intentionally supported values. Narrowing that contract requires a separate requirement.

## Verification after fixes

- DDEV jzm, PHP 8.4: Contao 5.3/DBAL3 and Contao 5.7/DBAL4 each pass **22 tests / 85 assertions** with actual database integration enabled.
- PHPStan passes in both dependency combinations. ECS passes.
- Firefox verified the synthetic fixture's even-row nested table, labeled/nested lists and formatted dates against the real backend at 1280px width in light/dark themes. Read-only DOM checks confirmed no editable controls or scripts. The attempted 390px rerun hit a Playwright resize timeout and left the viewport at 1280px, so the final fixes have no fresh mobile screenshot. The temporary fixture was removed afterwards.
- Existing submissions were only read. No unrelated schema migration was run.
- The known unrelated Rector configuration error remains outside this review's implementation scope.
