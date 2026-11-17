# Claude CSV review and triage

Claude CLI 2.1.325 reviewed the complete CSV-export diff, tests, requirements and relevant surrounding source using `claude-opus-5-5`, effort `high`, with no tools or MCP servers. The user authorized the review and transmission to Anthropic. The successful call took 112396 ms; the returned model usage confirms claude-opus-5-5.

Reviewed immutable patch SHA-256: `b518da24e3ce6f9b1d7dfce0c075c146f0848e6b9c943a4ed34a5554fa36fac9`, against base `5265566bd08aeafdde6727762e231fd20c2e9167`. This identifies the initial candidate; the fixes below were applied afterwards and were not sent for a second Claude review. Local input, diff and raw response are in `/tmp/submissions-csv-review-input.txt`, `/tmp/submissions-csv-review.diff` and `/tmp/submissions-csv-review.json`.

All three findings were validated:

1. **Medium — legacy fallback overrides an available stored caption.** Records without a caption fixed the header to a humanized name even when a later record had a real saved label. The regression test failed with `Topic` and `Late Caption` instead of the saved captions. Discovery now distinguishes saved labels from fallbacks, promotes the first nonempty saved caption while preserving column order, then resolves caption collisions. Tests cover missing metadata, empty saved captions and subsequent renamed captions.
2. **Low — empty lists differ from empty scalar answers.** Reproduced `''` versus `—` in plain-text export. Empty lists now use `—`, matching the backend and empty scalar answers; absent fields still produce empty cells. Regression failed before the change and passes afterwards.
3. **Low — changelog omits visible formula protection.** The protection prefixes formula-leading values with an apostrophe, including existing DCA columns. Added a user-facing Security entry and categorized the new CSV columns as Added.

Claude also noted the deliberate rejection of malformed additional JSON before streaming. No change: silently omitting corrupted values would produce an incomplete export. The normal storage path writes only valid JSON objects or NULL. Friendly error presentation remains outside this patch.

Verification after fixes: DDEV jzm, Contao 5.3/DBAL3 and 5.7/DBAL4, full PHPUnit suites (25 tests / 108 assertions each), PHPStan, Rector and ECS. Real jzm kernel/controller and Utils formatter tested with connection-local temporary data; existing submission records unchanged.
