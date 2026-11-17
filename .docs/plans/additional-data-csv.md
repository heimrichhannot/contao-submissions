# Additional form data in CSV exports

Approved scope: append one column per additional top-level form field, with snapshotted field and option labels. Lists and repeated groups stay in one readable multiline cell. Preserve the existing DCA columns, archive access checks, UTF-8 BOM and semicolon format. Test in DDEV jzm.

Implementation plan (inline):
- [x] Add a failing integration test exercising the real streamed CSV response and database queries with connection-local temporary tables, plus plain-text formatting tests.
- [x] Reuse the backend renderer's existing value tree for plain-text export, including selected options, uploads, dates and repeated rows.
- [x] Collect the union of additional field names for the archive before writing the header; stream rows in the same column order. Use the earliest stored label and distinguish duplicate captions by field name. Missing fields produce empty cells. Do not export the metadata column. Bound the streamed query to the last discovered record ID so concurrent new submissions cannot introduce columns absent from the header.
- [x] Update README and user-facing changelog; run PHPUnit, PHPStan, Rector and ECS in jzm with Contao 5.3 and 5.7. Review the resulting diff.

Review focus: fields appearing only in later records, same captions for different fields, changed labels, malformed/absent legacy metadata, multiline and CSV-special characters, stable field association, archive isolation and rejected export access.

Working directory: current checkout, matching the user's established preference for testing the branch here. The subsequent user request authorizes Claude review, fixes and a new PR against v2. No release requested.
