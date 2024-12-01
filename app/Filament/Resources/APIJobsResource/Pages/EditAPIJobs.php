<?php

namespace App\Filament\Resources\APIJobsResource\Pages;

use App\Filament\Resources\APIJobsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAPIJobs extends EditRecord
{
    protected static string $resource = APIJobsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
