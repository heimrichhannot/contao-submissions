<?php

namespace HeimrichHannot\Submissions\NotificationType;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;

class OptInChallengeNotificationType implements NotificationTypeInterface
{
    public const NAME = 'huh_submissions_challenge_optin';

    public const TOKEN_OPT_IN_TOKEN = 'optin_token';
    public const TOKEN_OPT_IN_URL = 'optin_url';

    public function __construct(private readonly TokenDefinitionFactoryInterface $factory) {}

    public function getName(): string
    {
        return static::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(AnythingTokenDefinition::class, static::TOKEN_OPT_IN_TOKEN, 'huhsub.optin_token'),
            $this->factory->create(AnythingTokenDefinition::class, static::TOKEN_OPT_IN_URL, 'huhsub.optin_url'),
            $this->factory->create(EmailTokenDefinition::class, 'email', 'form.form_email'),
            $this->factory->create(AnythingTokenDefinition::class, 'form_*', 'form.form_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'formconfig_*', 'form.formconfig_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'formlabel_*', 'form.formlabel_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'attachment_*', 'form.attachment_*'),
            $this->factory->create(TextTokenDefinition::class, 'raw_data', 'form.raw_data'),
            $this->factory->create(TextTokenDefinition::class, 'raw_data_filled', 'form.raw_data_filled'),
        ];
    }
}