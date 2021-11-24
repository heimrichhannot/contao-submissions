# Submissions
A generic module to store and handle submissions in Contao. You can use it with all of your modules to simplify submission handling. Works great with [heimrichhannot/frontendedit](https://github.com/heimrichhannot/contao-frontendedit),
[heimrichhannot/formhybrid_list](https://github.com/heimrichhannot/contao-formhybrid_list) and
[heimrichhannot/formhybrid](https://github.com/heimrichhannot/contao-formhybrid).

![alt Archive](docs/screenshot.png)

*Archive configuration*

![alt Archive](docs/screenshot2.png)

*List view with opportunity to export and resend confirmation*

![alt Archive](docs/screenshot3.png)

*Submission view (every field of this form can be changed, of course)*

## Features

- a new submissions entity (organized in archives)
- opportunity to specify a parent entity for each archive (e.g. an event)
- submissions are highly customizable by defining new fields in your dca (palette is created with no code at all)
- every archive can specify its own submission field list
- rich interfaces (e.g. SubmissionModel)
- handling for notification center messages ([terminal42/contao-notification_center](https://github.com/terminal42/contao-notification_center))
- easily export submissions as CSV and Excel file (using [heimrichhannot/contao-exporter](https://github.com/heimrichhannot/contao-exporter))
- optional cleaner support for periodically removing unpublished (aka inactive) submissions (using TL_CRON or your server's cron, using [heimrichhannot/contao-entity_cleaner](https://github.com/heimrichhannot/contao-entity_cleaner))
- specify a member (frontend) or a user (backend) to be the author of the submission
- Form generator support including opt-in process (contao 4.7+ only)

## Installation and usage

### Install

1. Install with composer or contao manager

    composer require heimrichhannot/contao-submissions

2. Update database

### Usage

You will find a new backend menu entry named "Submissions". Create a new archive with a title and select the fields, 
your submissions should contain.

#### Form generator
You can store your form generator submissions directly as submission. Just active 
"store as submission" and select the submission archive. Form field names must be 
the same as the fields names of the submission entity.

If you on contao 4.7 or higher, you can also set up an opt-in process for your submission.
Create an opt-in notification in notification center and select it in the form configuration.
You can also choose a jump to page to which the user is redirected when the opt-in-url 
is called and the opt-in was successful. If you want to check a property on successful
opt-ins, you can set the confirmation field property (e.g. set the publish field to true).

### Formhybrid

To use this bundle with formhybrid, we recommend to install 
[Submissions Creator](https://github.com/heimrichhannot/contao-submissions_creator).

## Further information

### Fields

#### tl_submission:

Name | Description
---- | -----------
authorType | Specifies whether a frontend member or a backend user is athor of the submission
author | Specifies the author
type | Specified the type of the submission
gender | Specifies a gender
academicTitle | Specifies an academicTitle
additionalTitle | Specifies an additionalTitle
firstname | Specifies a firstname
lastname | Specifies a lastname
company | Specifies a company
dateOfBirth | Specifies a dateOfBirth
street | Specifies a street
street2 | Specifies a street2
postal | Specifies a postal
city | Specifies a city
country | Specifies a country
email | Specifies an email
phone | Specifies a phone
fax | Specifies a fax
subject | Specifies a subject
notes | Specifies notes
message | Specifies a message
agreement | Specifies whether some constraint is agreed
privacy | Specifies whether some privacy constraint is agreed
captcha | Specifies a captcha (for use in frontend modules)
startDate | Specifies a startDate
stopDate | Specifies a stopDate
startDatime | Specifies a startDatime
stopDatime | Specifies a stopDatime
billingGender | Specifies a billingGender
billingFirstname | Specifies a billingFirstname
billingLastname | Specifies a billingLastname
billingCompany | Specifies a billingCompany
billingStreet | Specifies a billingStreet
billingPostal | Specifies a billingPostal
billingCity | Specifies a billingCity
attachments | Specifies an attachment
published | Determines whether the submission is published (aka inactive)
formHybridBlob | Can be used in combination with [heimrichhannot/formhybrid](https://github.com/heimrichhannot/contao-formhybrid) to temporarily save submission data to a blob before really saving it to database.

##### Add custom fields

After adding new fields, run

```\HeimrichHannot\Submissions\Backend\SubmissionBackend::addFieldsToPalette();```

in your dca in order to add the new fields to the default palette.

#### tl_submission_archive

Name | Description
---- | -----------
parentTable | Specifies the parent table (if necessary)
parentField | Specifies the parent table's label field
pid | Stores the id of the parent entity
title | Specifies the title of the archive
submissionFields | Specifies the fields visible in the forms of submissions of the current archive
titlePattern | Specifies a pattern for the archive's submission's label (e.g. "%title% %someotherfield%")
nc_submission | Specifies a notification being sent when submitting a submission (sorry for the poor expression ^^). Could be used for informing some customer that a submission had been made.
nc_confirmation | Specifies a notification being sent to the author of the submission. Can be resent in the list view of the archive via backend.

### Hooks

Name | Arguments | Description
---- | --------- | -----------
preGenerateSubmissionTokens | $objSubmission, $objSubmissionArchive, $arrFields | Triggered just before the token generation for notifications is started. Could be used for changing the field list.