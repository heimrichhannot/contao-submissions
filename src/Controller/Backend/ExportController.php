<?php

namespace HeimrichHannot\Submissions\Controller\Backend;

use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\DC_Table;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '%contao.backend.route_prefix%/huh_submissions/archive/{archive}/export', name: self::class, defaults: [
    '_scope' => 'backend',
])]
class ExportController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Utils $utils,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(SubmissionArchiveModel $archive): Response
    {
        if (!$this->isGranted(
            ContaoCorePermissions::DC_PREFIX . SubmissionArchiveModel::getTable(),
            new ReadAction(SubmissionArchiveModel::getTable(), $archive->row())
        )
        ) {
            throw new AccessDeniedException('You are not allowed to access this submission archive.');
        }

        if (!$archive->allowExport) {
            throw new AccessDeniedException('Exporting submissions is not allowed for this archive.');
        }

        $fields = StringUtil::deserialize($archive->submissionFields, true);
        $fields[] = 'dateAdded';
        $fields = array_unique($fields);

        Controller::loadLanguageFile('tl_submission');

        $submissions = $this->prepareExportData($archive, $fields);

        $response = new StreamedResponse(function () use ($submissions, $fields) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $this->addRowToCsvHandle(
                $handle,
                array_map(
                    fn ($field) => $GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['label'][0] ??
                        $this->translator->trans('tl_submission.' . $field . '.0', [], 'contao_tl_submission'),
                    $fields
                )
            );

            // Data rows
            foreach ($submissions() as $row) {
                $this->addRowToCsvHandle($handle, $row);
            }
            fclose($handle);
        });

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="submissions_' . $archive->id . '.csv"');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }

    private function prepareExportData(SubmissionArchiveModel $archive, array $fields): \Closure
    {
        return function () use ($archive, $fields) {
            $result = $this->connection->executeQuery(
                'SELECT * FROM tl_submission WHERE pid = :pid',
                [
                    'pid' => $archive->id,
                ]
            );

            $fields = array_combine(array_flip($fields), $fields);

            $dc = new class($archive) extends DC_Table {
                /**
                 * @noinspection PhpMissingParentConstructorInspection
                 */
                public function __construct(
                    $archive,
                ) {
                    $this->intId = $archive->id;
                    $this->strTable = SubmissionModel::getTable();
                }
            };

            while ($row = $result->fetchAssociative()) {
                $return = [];
                foreach ($fields as $key) {
                    $return[$key] = $this->utils->formatter()->formatDcaFieldValue($dc, $key, $row[$key]);
                }
                yield $return;
            }
        };
    }

    private function addRowToCsvHandle($handle, array $fields): void
    {
        fputcsv(
            stream: $handle,
            fields: $fields,
            separator: ';',
            enclosure: '"',
            escape: '\\',
            eol: "\r\n",
        );
    }
}
