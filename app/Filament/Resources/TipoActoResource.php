<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoActoResource\Pages;
use App\Models\TipoActo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TipoActoResource extends Resource
{
    protected static ?string $model = TipoActo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    protected static ?string $navigationLabel = 'Tipos de Actos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_tipo_acto')
                    ->label('Nombre del Tipo de Acto')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn (?Model $record) => $record)
                    ->validationAttribute('tipo de acto')
                    ->helperText("Introduce un nombre único para el tipo de acto."),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_tipo_acto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListTipoActos::route('/'),
            // 'create' => Pages\CreateTipoActo::route('/create'),
            // 'edit' => Pages\EditTipoActo::route('/{record}/edit'),
        ];
    }
}
