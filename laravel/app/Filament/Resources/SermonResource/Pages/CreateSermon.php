<?php

namespace App\Filament\Resources\SermonResource\Pages;

use App\Filament\Resources\SermonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSermon extends CreateRecord
{
    protected static string $resource = SermonResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return SermonResource::prepareFormData($data);
    }
}
