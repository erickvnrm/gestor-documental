<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EjeTematicoResource\Pages;
use App\Models\EjeTematico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EjeTematicoResource extends Resource
{
    protected static ?string $model = EjeTematico::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    protected static ?string $navigationLabel = 'Ejes Tematicos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_eje_tematico')
                    ->label('Eje Tematico')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn (?Model $record) => $record)
                    ->validationAttribute('eje tematico')
                    ->helperText("Introduce un nombre único para el eje tematico."),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_eje_tematico')
                    ->label('Eje Tematico')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEjeTematicos::route('/'),
            // 'create' => Pages\CreateEjeTematico::route('/create'),
            // 'edit' => Pages\EditEjeTematico::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): string
    {
        return 'Ejes Tematicos';
    }

    public static function getModelLabel(): string
    {
        return 'Eje Tematico';
    }
}
