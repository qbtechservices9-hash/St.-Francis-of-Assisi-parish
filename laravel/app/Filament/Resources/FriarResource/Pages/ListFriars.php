<?php

namespace App\Filament\Resources\FriarResource\Pages;

use App\Filament\Resources\FriarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFriars extends ListRecords
{
    protected static string $resource = FriarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
