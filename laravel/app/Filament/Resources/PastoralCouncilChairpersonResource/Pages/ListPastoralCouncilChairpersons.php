<?php

namespace App\Filament\Resources\PastoralCouncilChairpersonResource\Pages;

use App\Filament\Resources\PastoralCouncilChairpersonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPastoralCouncilChairpersons extends ListRecords
{
    protected static string $resource = PastoralCouncilChairpersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
