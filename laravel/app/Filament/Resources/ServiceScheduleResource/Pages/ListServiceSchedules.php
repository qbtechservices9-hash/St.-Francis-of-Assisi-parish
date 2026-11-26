<?php

namespace App\Filament\Resources\ServiceScheduleResource\Pages;

use App\Filament\Resources\ServiceScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceSchedules extends ListRecords
{
    protected static string $resource = ServiceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
