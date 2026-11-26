<?php

namespace App\Filament\Resources\SocietyResource\Pages;

use App\Filament\Resources\SocietyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocieties extends ListRecords
{
    protected static string $resource = SocietyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
