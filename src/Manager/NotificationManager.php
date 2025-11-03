<?php

namespace HeimrichHannot\Submissions\Manager;

use Codefog\HasteBundle\FileUploadNormalizer;
use Contao\Form;
use Contao\StringUtil;
use HeimrichHannot\Submissions\NotificationType\OptInChallengeNotificationType;
use Terminal42\NotificationCenterBundle\BulkyItem\FileItem;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\BulkyItemsStamp;

readonly class NotificationManager
{
    public function __construct(
        private DcaManager           $dcaManager,
        private FileUploadNormalizer $fileUploadNormalizer,
        private NotificationCenter   $notificationCenter,
    ) {}

    public function filterSubmittedData(array $submittedData): array
    {
        $submissibleFields = $this->dcaManager->getSubmissibleFields('tl_submission');

        return \array_filter(
            $submittedData,
            static fn ($k) => \in_array($k, $submissibleFields, true),
            \ARRAY_FILTER_USE_KEY
        );
    }

    public function generateTokens(array $submittedData): array
    {
        $submissibleFields = $this->dcaManager->getSubmissibleFields('tl_submission');
        $tokens = [];
        $rawData = [];
        $rawDataFilled = [];

        foreach ($submittedData as $k => $v)
        {
            if (!\in_array($k, $submissibleFields, true)) {
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

            $label = isset($labels[$k]) && \is_string($labels[$k]) ? StringUtil::decodeEntities($labels[$k]) : ucfirst((string) $k);

            $tokens['formlabel_'.$k] = $label;
            $tokens['form_'.$k] = $v;

            $rawData[] = $label.': '.(\is_array($v) ? \implode(', ', $v) : $v);

            if (\is_array($v) || ('' !== (string) $v)) {
                $rawDataFilled[] = $label.': '.(\is_array($v) ? \implode(', ', $v) : $v);
            }
        }

        return [$tokens, $rawData, $rawDataFilled];
    }

    public function send(
        array      $submittedData,
        array      $formData,
        array|null $files,
        array      $labels,
        Form       $form,
        string     $optInToken,
        string     $optInUrl
    ): void {
        $notificationId = $formData['huhSub_optInNotification'] ?? 0;
        if (!\is_numeric($notificationId) || $notificationId < 1) {
            return;
        }
        $notificationId = (int) $notificationId;

        $bulkyItemVouchers = [];
        $files = !\is_array($files) ? [] : $files; // In Contao 4.13, $files can be null

        [$tokens, $rawData, $rawDataFilled] = $this->generateTokens($submittedData);

        if ($email = $tokens['form_email'] ?? null) {
            $tokens['email'] = $email;
        }

        $tokens[OptInChallengeNotificationType::TOKEN_OPT_IN_TOKEN] = $optInToken;
        $tokens[OptInChallengeNotificationType::TOKEN_OPT_IN_URL] = $optInUrl;

        foreach ($formData as $k => $v) {
            $tokens['formconfig_'.$k] = \is_string($v) ? StringUtil::decodeEntities($v) : $v;
        }

        $tokens['raw_data'] = \implode("\n", $rawData);
        $tokens['raw_data_filled'] = \implode("\n", $rawDataFilled);

        foreach ($this->fileUploadNormalizer->normalize($files) as $k => $fileDefinitions)
        {
            $vouchers = [];

            foreach ($fileDefinitions as $arrFile)
            {
                $fileItem = \is_resource($arrFile['stream']) ?
                    FileItem::fromStream($arrFile['stream'], $arrFile['name'], $arrFile['type'], $arrFile['size']) :
                    FileItem::fromPath($arrFile['tmp_name'], $arrFile['name'], $arrFile['type'], $arrFile['size']);

                $voucher = $this->notificationCenter->getBulkyGoodsStorage()->store($fileItem);

                $vouchers[] = $voucher;
                $bulkyItemVouchers[] = $voucher;
            }

            $tokens['form_'.$k] = \implode(',', $vouchers);
        }

        // Make sure we don't pass any objects as tokens
        $tokens = \array_filter($tokens, static fn ($v) => !\is_object($v));

        $stamps = $this->notificationCenter->createBasicStampsForNotification(
            $notificationId,
            $tokens,
        );

        if (\count($bulkyItemVouchers) > 0) {
            $stamps = $stamps->with(new BulkyItemsStamp($bulkyItemVouchers));
        }

        $this->notificationCenter->sendNotificationWithStamps($notificationId, $stamps);
    }
}