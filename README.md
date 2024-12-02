# Submissions
A generic module to store and handle submissions in Contao. You can use it with all of your modules to simplify submission handling.

## Features

- Directly store form generator submissions.
- Submissions organized in archives using a dedicated DCA.
- Highly customizable: define new fields on the data container.
- Auto-creates palettes for your fields.
- Ships with an optional automated double opt-in process.
- Notification Center 2.0 support ([terminal42/contao-notification_center](https://github.com/terminal42/contao-notification_center))

## Install

1. Install with composer or contao manager

```bash
composer require heimrichhannot/contao-submissions
```

2. Update database

## Usage

In the backend, you will find a new menu item called “Submissions”. Create a new archive with a title and select the
fields that should be contained in your submissions.

### Form generator
You can save your form generator submissions directly as a submission. Simply activate "Save as submission" and select
the submission archive. The names of the form fields must be the same as the field names of the saved entity.

Use `##form_attachment_*##` tokens in your notification center notifications to include attachments in your emails.

Use the built-in double opt-in process to verify submissions.
Create an opt-in challenge notification in notification center and select it on your form in the form generator.
You can also define a jump to page to which the user is redirected when the opt-in is successful.
If you want to bump a property at successful opt-in, set a boolean confirmation field (i.e. to set the field `publish` to true).

Use the following notification tokens in the opt-in notification:

| Token             | Description                                                                                                              |
|-------------------|--------------------------------------------------------------------------------------------------------------------------|
| `##optin_token##` | To be replaced with the opt-in token                                                                                     |
| `##optin_url##`   | To be replaced with the absolute opt-in url                                                                              |
| `##email##`       | Same as `##form_email##`, but guaranteed to be in a valid email address format. Intended use as recipient email address. |


## Development

### Fields on tl_submission

Take a look at [`dca/tl_submission.php`](https://github.com/heimrichhannot/contao-submissions/blob/master/dca/tl_submission.php#L94) for all available fields.

Adjust the dca to your needs.

Mark fields as `noSubmissionField` to make them unavailable for use as submission fields.

```php
$dca = &$GLOBALS['TL_DCA']['tl_submission'];

$dca['fields']['my_field']['eval']['noSubmissionField'] = true;
```

### Events

| Event                                              | Description                                                                     |
|----------------------------------------------------|---------------------------------------------------------------------------------|
| SubmissionsBeforeSendConfirmationNotificationEvent | Dispatched before success notification is sent. Requires enabled double opt-in. |
