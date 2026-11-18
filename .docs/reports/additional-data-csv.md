# CSV export of additional form data

Additional top-level fields now append separate columns to the existing archive CSV. Columns are discovered across the archive in record ID order, keyed by the original field name. The earliest nonempty stored caption determines the header, even if older records have no caption; changed option captions are resolved independently for each submission. Duplicate captions are distinguished with the field name. Legacy records without metadata use humanized names and raw values. Missing fields are empty cells.

The existing backend value tree is reused for plain-text cells: selected options, upload filenames, localized dates/booleans, serialized multiple values and repeated groups retain their readable representation without HTML or insert-tag evaluation. Malformed additional JSON aborts export before streaming, rather than silently omitting data. The internal metadata object is never exported as a separate column.

CSV keeps its UTF-8 BOM, semicolon delimiter and CRLF record separator. Standard quote doubling (empty proprietary escape character) preserves quotes, line breaks and trailing backslashes on CSV round trips. Formula-leading strings in headers and data are prefixed with an apostrophe for spreadsheet use. Archive authorization and export enablement checks precede queries.

Verification in DDEV project jzm:

- Regression test first failed because only the two DCA columns were present; a second test-first check reproduced formula-leading values being emitted without protection.
- Concurrent submissions arriving between header discovery and streaming initially added a row without its new field. The streamed query is now bounded by the last discovered record ID; the regression passes.
- Contao 5.7 / DBAL4: 25 tests, 108 assertions; Contao 5.3 / DBAL3: 25 tests, 108 assertions. PHPStan, Rector and ECS pass for both installations.
- The real jzm kernel resolves the actual controller service with the real Utils DCA formatter. A connection-local temporary table verifies email/date formatting alongside saved option captions, multiple selections and repeated groups in the streamed CSV. No existing records are modified; the temporary table is removed in finally.
- Independent read-only review of the initial implementation found no actionable findings. The subsequent concurrent-insert bound was self-reviewed and regression tested.
- git diff --check passes.

Preexisting observation, outside this change: the real jzm CSV's dateAdded header falls back to the untranslated key tl_submission.dateAdded.0, while its date value is formatted correctly.

Claude Opus 5.5 high follow-up: three findings validated and fixed; see [review triage](claude-csv-review.md). Empty submitted lists now use the same dash as empty scalars; missing fields remain empty CSV cells.
