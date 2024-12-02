<?php

namespace HeimrichHannot\Submissions\Manager;

use Contao\FilesModel;

class SimpleTokensManager
{
    public function generateAttachmentTokens(?array $files): array
    {
        if (empty($files))
        {
            return [];
        }

        $tokens = [];

        foreach ($files as $fieldName => $fileData)
        {
            if ($fileModel = FilesModel::findByUuid($fileData['uuid']))
            {
                $tokens['attachment_'.$fieldName] = $fileModel->path;
            }
        }

        return $tokens;
    }
}