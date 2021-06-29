# Changelog

All notable changes to this project will be documented in this file.

## [1.16.0] - 2021-06-29

- added Polish translations

## [1.15.1] - 2021-03-09

- fixed issue with multifileupload integration

## [1.15.0] - 2020-12-14

- increased title length to 255
- added streetNumber field

## [1.14.0] - 2020-10-08

- possibility to apply the palette logic once again

## [1.13.0] - 2020-09-29

- added pid translation

## [1.12.0] - 2020-09-25

- added more ics tokens (support for notification center plus's ics generation)

## [1.11.0] - 2020-09-24

- added ics tokens (support for notification center plus's ics generation)

## [1.10.0] - 2020-08-11

- added firstname to salutation generation

## [1.9.0] - 2020-08-10

- added diverse gender

## [1.8.0] - 2020-06-03

- added possibility to override

## [1.7.0] - 2020-03-19

- added oncreate_callback in `SubmissionModel::create` and `tl_submission`
- added `submissionLanguage` to `tl_submission`

## [1.6.2] - 2020-02-14

- fixed `attachmentExtensions` sql

## [1.6.1] - 2020-01-16

- fixed contao 4 compatibilty for tl_submission_archive and multifileupload

## [1.6.0] - 2019-11-06

- commented currently dysfunctional send_confirmation operation (todo)

## [1.5.8] - 2019-10-28

### Fixed

- prevented checkPermission from being called in frontend

## [1.5.7] - 2019-08-07

### Changed

- added localization

## [1.5.6] - 2019-05-29

### Changed

- export action order

## [1.5.5] - 2019-05-24

### Added

- billingAcademicTitle

### Fixed

- billing fields are not usable for substitute fields

## [1.5.4] - 2019-03-14

### Changed

- updated polish translations

## [1.5.3] - 2019-03-06

### Added

- some polish translations

## [1.5.2] - 2019-01-29

### Fixed

- moved gender translation to tl_submission (from haste) and added cs, pl, en and ru translations for male and female

## [1.5.1] - 2018-12-11

### Fixed

- attachments fields not added when using multifileupload bundle

## [1.5.0] - 2018-12-07

### Added

- fields: language, languages

## [1.4.9] - 2018-10-30

### Added

- Italian translation (thanks @MicioMax)

## [1.4.8] - 2018-06-07

### Added

- support export for module and bundle mode in tl_submission

## [1.4.7] - 2018-06-07

### Fixed

- issue with ptable and deletion of submission archives

## [1.4.6] - 2018-03-08

### Fixed

- `tl_submission_archive.submissionFields` style glitch under contao 4

## [1.4.5] - 2018-02-15

### Fixed

- toggleVisibility

## [1.4.4] - 2018-02-01

### Added

- some english translations

## [1.4.3] - 2018-01-30

### Fixed

- salutation for academicTitle

### Added

- position field

## [1.4.2] - 2018-01-24

### Added

- some english translations

### Changed

licence LGPL-3.0+ is now LGPL-3.0-or-later

## [1.4.1] - 2017-11-03

### Added

- support for special fields in title pattern

## [1.4.0] - 2017-11-02

### Added

- startDate, stopDate, startDatime, stopDatime, addDifferentBillingData

### Fixed

- palette handling -> now generated automatically (see README.md)

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

- add all available fields to default palette, otherwise fields wont be rendered in frontend, due
  to `formhybridForcePaletteRelation`

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
