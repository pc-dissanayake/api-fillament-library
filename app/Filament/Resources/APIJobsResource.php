<?php

namespace App\Filament\Resources;

use App\Filament\Resources\APIJobsResource\Pages;
use App\Filament\Resources\APIJobsResource\RelationManagers;
use App\Models\APIJobs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class APIJobsResource extends Resource
{
    protected static ?string $model = APIJobs::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->maxLength(65535),
            Forms\Components\TextInput::make('url')
                ->url()
                ->maxLength(255),
            Forms\Components\Toggle::make('active')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('public_uuid'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('url'),
                Tables\Columns\BooleanColumn::make('active'),
                Tables\Columns\BooleanColumn::make('locked'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TableStructureRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAPIJobs::route('/'),
            'create' => Pages\CreateAPIJobs::route('/create'),
            'edit' => Pages\EditAPIJobs::route('/{record}/edit'),
        ];
    }
}
