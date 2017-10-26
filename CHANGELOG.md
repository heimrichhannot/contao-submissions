# Changelog
All notable changes to this project will be documented in this file.

## [1.3.13] - 2017-10-26

### Added
- message

## [1.3.12] - 2017-10-12

### Added
- frontend handling for gender (in frontend now is named "salutation" which is more common rather than gender)

## [1.3.11] - 2017-10-09

### Added
- `includeBlankOption` for `tl_submission.gender`

## [1.3.10] - 2017-07-26

### Added
- added field for billing company

## [1.3.9] - 2017-07-18

### Added
- added fields for billing address

## [1.3.8] - 2017-06-20

### Fixed
- translations

## [1.3.7] - 2017-05-09

### Fixed
- tl_submission.postal field was not separated with comma from street and field widget did not appear

## [1.3.6] - 2017-05-09

### Fixed
- php 7 support

## [1.3.5] - 2017-05-09

### Changed
- add all available fields to default palette, otherwise fields wont be rendered in frontend, due to `formhybridForcePaletteRelation`

## [1.3.4] - 2017-04-28

### Changed
- varchar lengths to reduce db size

## [1.3.3] - 2017-04-12
- created new tag

## [1.3.2] - 2017-04-06

### Changed
- added php7 support. fixed contao-core dependency

## [1.3.1] - 2017-03-28

### Fixed
- attachment max upload dimension size fix

## [1.3.0] - 2017-03-17

### Removed
- exporter dependency -> now a suggestion

## [1.2.5] - 2017-02-08

### Removed
- author & authorType functionality - included in haste_plus
