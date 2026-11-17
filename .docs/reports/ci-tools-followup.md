# CI tooling follow-up — 2026-11-17

## Changes

- `AdditionalDataRenderer` and `SubmissionDataManager` are explicitly marked `@internal`.
- Reproduced the Rector failure: Contao's aggregate `UP_TO_CONTAO_53` imports the removed `DoctrineSetList::DOCTRINE_ORM_214` constant. Replaced that aggregate with the Contao 5.0, 5.1 and 5.3 rule sets; Composer-based Doctrine, Symfony, PHPUnit and Twig sets select rules for installed dependencies.
- Replaced the old `ClassMethod` path of `AddVoidReturnTypeWhereNoReturnRector` with the actual `ClassLike` path. Rector's dev requirement is now `^2.8`, where that path is present; verified locally with Rector 2.8.5. PHP target remains compatible with the package minimum, PHP 8.2. Explicit nullable parameter types remain enabled.
- Rector, ECS and PHPStan permanently include tests, PHP translations and their PHP configuration files. PHPStan loads the existing embedded ECS autoloader to discover its fixer classes, rather than ignoring missing symbols.
- Applied Rector's class-import corrections and ECS formatting to the PR's tests and tooling configuration. Runtime behavior was not changed.

## Verification

All 16 PR-relevant PHP files (diff against `v2`, plus the modified tooling files) were checked explicitly with Rector, ECS and PHPStan. The regular CI commands were also run with their final configured paths in DDEV `jzm`, PHP 8.4.27:

| Check | Contao 5.3 / DBAL 3 | Contao 5.7 / DBAL 4 |
| --- | --- | --- |
| Rector dry run | Pass | Pass |
| ECS | Pass | Pass |
| PHPStan | Pass | Pass |
| PHPUnit with real DB integration | 22 tests / 85 assertions | 22 tests / 85 assertions |

No baseline or warning/deprecation suppression was added. Run ECS sequentially when using both dependency copies in one container: ECS uses a shared temporary changed-files cache, so simultaneous configuration-cache invalidation caused a transient collision; both sequential reruns passed. GitHub's ECS job has its own runner.

The older reports describe the Rector failure as it existed at those review dates; this follow-up resolves it.
