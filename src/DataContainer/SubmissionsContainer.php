<?php

namespace HeimrichHannot\Submissions\DataContainer;

class SubmissionsContainer
{
    public static function getDefaultAttachmentSubFolderPattern(): string
    {
        return '[dateAdded::date::Y]/[dateAdded::date::m]/[dateAdded::date::d]/[id]';
    }
}