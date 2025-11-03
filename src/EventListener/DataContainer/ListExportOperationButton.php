<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use HeimrichHannot\Submissions\Controller\Backend\ExportController;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ListExportOperationButton
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[AsCallback(table: 'tl_submission', target: 'list.global_operations.export.button')]
    public function __invoke(?string $href, string $label, string $title, string $class, string $attributes, string $table, array $root): string
    {
        $do = $this->requestStack->getCurrentRequest()?->get('do');
        $table = $this->requestStack->getCurrentRequest()?->get('table');
        $act = $this->requestStack->getCurrentRequest()?->get('act');

        if ('huh_submissions' !== $do || 'tl_submission' !== $table || null !== $act) {
            return '';
        }

        $id = $this->requestStack->getCurrentRequest()?->get('id');
        if (null === $id || !($archive = SubmissionArchiveModel::findByPk($id))) {
            return '';
        }

        if (!$archive->allowExport) {
            return '';
        }

        $href = $this->urlGenerator->generate(ExportController::class, [
            'archive' => $archive->id,
        ]);

        return sprintf(
            '<a href="%s" class="%s" title="%s"%s>%s</a>',
            $href,
            $class,
            $title,
            $attributes,
            $label
        );
    }
}
