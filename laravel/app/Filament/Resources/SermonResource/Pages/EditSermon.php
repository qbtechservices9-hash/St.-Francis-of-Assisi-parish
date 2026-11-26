<?php

namespace App\Filament\Resources\SermonResource\Pages;

use App\Filament\Resources\SermonResource;
use Filament\Resources\Pages\EditRecord;

class EditSermon extends EditRecord
{
    protected static string $resource = SermonResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return SermonResource::prepareFormData($data);
    }
}
