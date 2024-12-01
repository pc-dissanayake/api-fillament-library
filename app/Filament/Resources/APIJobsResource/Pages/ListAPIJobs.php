<?php

namespace App\Filament\Resources\APIJobsResource\Pages;

use App\Filament\Resources\APIJobsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAPIJobs extends ListRecords
{
    protected static string $resource = APIJobsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
