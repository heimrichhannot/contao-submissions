<?php

namespace HeimrichHannot\Submissions\Manager;

use Codefog\HasteBundle\FileUploadNormalizer;
use Contao\Form;
use Contao\StringUtil;
use Terminal42\NotificationCenterBundle\BulkyItem\FileItem;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\BulkyItemsStamp;

class NotificationManager
{
    public function __construct(
        private readonly FileUploadNormalizer $fileUploadNormalizer,
        private readonly NotificationCenter $notificationCenter,
    ) {}

    public function send(
        array      $submittedData,
        array      $formData,
        array|null $files,
        array      $labels,
        Form       $form,
        string     $optInUrl
    ): void {
        $notificationId = $formData['huhSub_optInNotification'] ?? 0;
        if (!\is_numeric($notificationId) || $notificationId <= 0) {
            return;
        }
        $notificationId = (int) $notificationId;

        $tokens = [];
        $rawData = [];
        $rawDataFilled = [];
        $bulkyItemVouchers = [];
        $files = !\is_array($files) ? [] : $files; // In Contao 4.13, $files can be null

        $submissibleFields = \array_filter(
            $GLOBALS['TL_DCA']['tl_submission']['fields'] ?? [],
            static fn ($field) => !\filter_var(
                $field['eval']['noSubmissionField'] ?? false,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            )
        );

        foreach ($submittedData as $k => $v)
        {
            if (!\array_key_exists($k, $submissibleFields)) {
                continue;
            }

            // Skip the tokens that are not implodeable
            if (\is_array($v)) {
                foreach ($v as $vv) {
                    if (!\is_scalar($vv)) {
                        continue 2;
                    }
                }
            }

            $label = isset($labels[$k]) && \is_string($labels[$k]) ? StringUtil::decodeEntities($labels[$k]) : ucfirst($k);

            $tokens['formlabel_'.$k] = $label;
            $tokens['form_'.$k] = $v;

            $rawData[] = $label.': '.(\is_array($v) ? implode(', ', $v) : $v);

            if (\is_array($v) || ('' !== (string) $v)) {
                $rawDataFilled[] = $label.': '.(\is_array($v) ? implode(', ', $v) : $v);
            }
        }

        if ($email = $tokens['form_email'] ?? null) {
            $tokens['email'] = $email;
        }

        $tokens['opt_in_url'] = $optInUrl;

        foreach ($formData as $k => $v) {
            $tokens['formconfig_'.$k] = \is_string($v) ? StringUtil::decodeEntities($v) : $v;
        }

        $tokens['raw_data'] = implode("\n", $rawData);
        $tokens['raw_data_filled'] = implode("\n", $rawDataFilled);

        foreach ($this->fileUploadNormalizer->normalize($files) as $k => $files) {
            $vouchers = [];

            foreach ($files as $file) {
                $fileItem = \is_resource($file['stream']) ?
                    FileItem::fromStream($file['stream'], $file['name'], $file['type'], $file['size']) :
                    FileItem::fromPath($file['tmp_name'], $file['name'], $file['type'], $file['size']);

                $vouchers[] = $this->notificationCenter->getBulkyGoodsStorage()->store($fileItem);
            }

            $tokens['form_'.$k] = implode(',', $vouchers);
            $bulkyItemVouchers = array_merge($bulkyItemVouchers, $vouchers);
        }

        // Make sure we don't pass any objects as tokens
        $tokens = array_filter($tokens, static fn ($v) => !\is_object($v));

        $stamps = $this->notificationCenter->createBasicStampsForNotification(
            $notificationId,
            $tokens,
        );

        if (0 !== \count($bulkyItemVouchers)) {
            $stamps = $stamps->with(new BulkyItemsStamp($bulkyItemVouchers));
        }

        $this->notificationCenter->sendNotificationWithStamps($notificationId, $stamps);
    }
}