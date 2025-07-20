<?php
namespace App\Filament\Resources;

use App\Filament\Exports\ActosExporter;
use App\Filament\Resources\ActosResource\Pages;
use App\Models\Actos;
use App\Models\EjeTematico;
use App\Models\TipoActo;
use App\Http\Controllers\ActosController;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ActosResource extends Resource
{
    protected static ?string $model = Actos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Administración de Actos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id())
                    ->required(),
                Forms\Components\Select::make('tipo_acto_id')
                    ->label('Tipo de Acto')
                    ->options(TipoActo::all()->pluck('nombre_tipo_acto', 'id'))
                    ->required()
                    ->reactive()
                    ->disabled(fn($record) => $record !== null)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $year = now()->year;
                            $controller = new ActosController();
                            $result = $controller->getNextNumberSimulado($state, $year);
                            $set('number', $result);
                        }
                    }),                
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->disabled()
                    ->required(),
                Forms\Components\Select::make('eje_tematico_id')
                    ->relationship('ejeTematico', 'name_eje_tematico')
                    ->label('Eje Temático')
                    ->options(EjeTematico::all()->pluck('name_eje_tematico', 'id'))
                    ->createOptionForm(fn(Form $form) => EjeTematicoResource::form($form))
                    ->editOptionForm(fn(Form $form) => EjeTematicoResource::form($form))
                    ->searchable()
                    ->required()
                    ->default(fn ($record) => $record ? $record->eje_tematico_id : null)
                    ->reactive(),
                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->placeholder('Escribe aquí el título...')
                    ->maxLength(1000)
                    ->reactive(),
                Forms\Components\Radio::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'acto' => 'Subir un Acto',
                        'oficio' => 'Subir un Oficio',
                    ])
                    ->descriptions([
                        'acto' => 'Selecciona esta opción para subir un acto.',
                        'oficio' => 'Selecciona esta opción para subir un oficio y anular el acto.',
                    ])
                    ->reactive()
                    ->visible(fn ($get, $record) => $get('enable_file_upload') || $record && $record->tipo_documento)
                    ->required(),
                Forms\Components\Toggle::make('enable_file_upload')
                    ->label('Habilitar Carga de Archivos')
                    ->default(false)
                    ->hidden(fn ($record) => $record && $record->tipo_documento != null)
                    ->reactive(),
                Forms\Components\FileUpload::make('archivo_url')
                    ->label('Cargar Archivo')
                    ->disk('public')
                    ->maxSize(512000)
                    ->visible(fn ($get, $record) => $get('enable_file_upload') || $record && $record->tipo_documento)
                    ->required(),
                Forms\Components\Textarea::make('observacion')
                    ->label('Observación')
                    ->required(fn ($get) => $get('tipo_documento') === 'oficio')
                    ->visible(fn ($get) => $get('tipo_documento') === 'oficio')
                    ->placeholder('Justifica por qué se anula este acto'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Nombre')
                    ->getStateUsing(function ($record) {
                        return wordwrap($record->usuario->name . ' ' . $record->usuario->last_name, 8, "\n", true);
                    })
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipoActo.nombre_tipo_acto')
                    ->label('Tipo Acto'),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('number', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('formatted_title')
                    ->label('Acto')
                    ->wrap(),
                Tables\Columns\TextColumn::make('observacion')
                    ->label('Observacion')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->wrap()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updatedByUser.name')
                    ->label('Último actualización')
                    ->getStateUsing(function ($record) {
                        if ($record->updatedByUser) {
                            return $record->updatedByUser->name . ' ' . $record->updatedByUser->last_name;
                        }
                        return '';
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->label('Estado')
                    ->icon(fn (string $state): string => match ($state) {
                        'pendiente' => 'heroicon-o-clock',
                        'aprobado' => 'heroicon-o-check-circle',
                        'anulado' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado' => 'success',
                        'anulado' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state) => ucfirst($state))
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_acto_id')
                    ->label('Tipo de Acto')
                    ->options(TipoActo::all()->pluck('nombre_tipo_acto', 'id')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Creado a partir de'),
                        DatePicker::make('created_until')
                            ->label('Creado hasta'),
                    ])->columnSpan(2)->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                    Tables\Filters\TrashedFilter::make(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                RestoreAction::make(),
                ViewAction::make(),
                EditAction::make()->visible(fn (Actos $record) => !$record->trashed()),
                Action::make('download')
                ->label('Descargar')
                ->icon('heroicon-m-document-arrow-down')
                ->url(fn (Actos $record) => 
                    $record->archivo_url && Storage::disk('public')->exists($record->archivo_url)
                        ? route('actos.download', $record)
                        : null
                )
                ->disabled(fn (Actos $record) => 
                    !$record->archivo_url || !Storage::disk('public')->exists($record->archivo_url)
                ),            
                ])->tooltip('Acciones'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ActosExporter::class)
                    ->label('Exportar')
                    ->color('warning')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => Auth::user()->hasRole('admin')),
                ]),
                ExportBulkAction::make()->exporter(ActosExporter::class)
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
            'index' => Pages\ListActos::route('/'),
            'create' => Pages\CreateActos::route('/create'),
            'edit' => Pages\EditActos::route('/{record}/edit'),
        ];
    }
}

