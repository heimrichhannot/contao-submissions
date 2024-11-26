<?php

namespace HeimrichHannot\Submissions\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\OptIn\OptIn;
use Contao\FilesModel;
use Contao\FormModel;
use Contao\PageModel;
use Contao\StringUtil;
use HeimrichHannot\Submissions\Event\SubmissionsBeforeSendConfirmationNotificationEvent;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\Submissions\Util\Tokens;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\NotificationCenterBundle\EventListener\ProcessFormDataListener;

class OptInController extends AbstractController
{
    public function __construct(
        private readonly OptIn $optIn,
        private readonly ProcessFormDataListener $processFormDataListener,
        private readonly Utils $utils
    ) {}

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['kernel'] = '?'.KernelInterface::class;

        return $services;
    }

    /**
     * @Route("/_huh_submissions/opt-in/{formId}/{tokenIdentifier}", name="huh_submissions_opt_in")
     */
    public function confirm(int $formId, string $tokenIdentifier, Request $request): Response
    {
        $this->initializeContaoFramework();

        $form = FormModel::findByPk($formId);

        if (!$form) {
            throw $this->createNotFoundException();
        }

        $optInToken = $this->optIn->find($tokenIdentifier);

        if (!$optInToken) {
            throw $this->abort($form);
        }

        $submission = SubmissionModel::findOneByOptInToken($optInToken);

        if (!$submission) {
            throw $this->abort($form);
        }

        $subCache = StringUtil::deserialize($submission->huhSub_optInCache, true);

        if ($form->id !== $subCache['form']) {
            throw $this->createAccessDeniedException();
        }

        if ($optInToken->isConfirmed()) {
            throw $this->abort($form, 'Token already confirmed');
        }

        if (!$optInToken->isValid()) {
            throw new BadRequestHttpException('Token is expired or no longer valid.');
        }

        $optInToken->confirm();

        if ($form->huhSub_optInField) {
            $submission->{$form->huhSub_optInField} = "1";
        }

        $submission->huhSub_optInCache = \serialize(['form' => $form->id]);
        $submission->save();

        $this->sendNotification($form, $submission, $subCache);

        $jumpTo = $form->getRelated('huhSub_optInJumpTo');

        if (!$jumpTo instanceof PageModel) {
            $jumpToPageId = $request->query->get('jtf');  // jump to fallback

            if ($jumpToPageId) {
                $jumpTo = PageModel::findByPk($jumpToPageId);
            }

            if (!$jumpTo instanceof PageModel) {
                $jumpTo = $form->getRelated('jumpTo');
            }
        }

        if (!$jumpTo instanceof PageModel) {
            return new Response('Opt-In successful', Response::HTTP_OK);
        }

        return $this->redirect($jumpTo->getFrontendUrl());
    }

    protected function sendNotification($form, $submission, $subCache): void
    {
        $subData = $submission->row();

        $files = $subCache['files'] ?? [];
        $files = \is_array($files) ? $files : StringUtil::deserialize($files, true);

        Tokens::addAttachmentTokens($subData, $files);

        try
        {
            $event = new SubmissionsBeforeSendConfirmationNotificationEvent($submission, $subCache, $form, $subData);

            $this->container->get('event_dispatcher')->dispatch($event, $event::class);

            $subData = $event->getSubmissionData();
        }
        catch (\Exception $e)
        {
            if ($this->container->get('kernel')?->isDebug())
            {
                throw $e;
            }

            $this->utils->container()->log($e->getMessage(), __METHOD__, TL_ERROR);
        }

        $this->processFormDataListener->__invoke($subData, $form->row(), $files, $subCache['labels'] ?? [], $form);
    }

    protected function abort(FormModel $form, string $message = 'Not Found'): \RuntimeException
    {
        $jumpToPageId = $form->huhSub_optInTokenInvalidJumpTo ?: $form->huhSub_optInJumpTo ?: $form->jumpTo;
        if (!$jumpToPageId) {
            return $this->createNotFoundException($message);
        }

        if (!$jumpToPage = PageModel::findByPk($jumpToPageId)) {
            return $this->createNotFoundException($message);
        }

        if (!$jumpToPage->published) {
            return $this->createNotFoundException($message);
        }

        $url = $jumpToPage->getAbsoluteUrl();

        return new RedirectResponseException($url);
    }
}