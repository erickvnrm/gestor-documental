<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Areas;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Tables\Actions\ActionGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administración de Usuarios';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $breadcrumb = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('last_name')
                ->label('Apellido')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Correo Electrónico')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('document')
                ->label('Documento')
                ->required()
                ->maxLength(255),
            Select::make('area_id')
                ->label('Área')
                ->options(Areas::all()->pluck('name_area', 'id'))
                ->required()
                ->searchable()
                ->placeholder('Selecciona un área'),
            Select::make('role')
                ->label('Rol')
                ->options(User::ROLES)
                ->default(User::ROLE_USER)
                ->required()
                ->afterStateHydrated(fn ($state, callable $set) => $set('role', $state)),
            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->autocomplete('new-password')
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                ->afterStateHydrated(fn ($state, callable $set) => $set('password', '')),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->getStateUsing(function ($record) {
                        return $record->name . ' ' . $record->last_name;
                    })
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name_area')
                    ->label('Área')
                    ->wrap(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(function ($state) {
                        return User::ROLES[$state] ?? $state;
                    }),
                Tables\Columns\SelectColumn::make('state')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo')
                    ->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('última actualización')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('id')
                    ->label('Filtrar por nombre de usuario')
                    ->options(User::all()->pluck('name', 'id'))
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])->tooltip('Acciones'),
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
            'index' => Pages\ListUsers::route('/usuarios'),
            'create' => Pages\CreateUser::route('/crear'),
            'edit' => Pages\EditUser::route('/{record}/editar'),
        ];
    }

    public static function getPluralLabel(): string
    {
        return 'Usuarios';
    }

    public static function getModelLabel(): string
    {
        return 'Usuario';
    }

}
