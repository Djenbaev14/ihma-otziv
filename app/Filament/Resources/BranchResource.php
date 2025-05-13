<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'fas-building';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        FileUpload::make('logo')
                                ->label('Логотип')
                                ->image()
                                ->disk('public') 
                                ->directory('logos')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->columnSpan(12),
                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(12),
                    ])->columnSpan(12)->columns(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать')
                    ->modal()
                    ->modalHeading('Создать')
                    ->modalSubmitActionLabel('Сохранить')
                    ->modalWidth(MaxWidth::Medium)
                    ->modalAlignment('end')
                    ->slideOver()
                    ->action(function (array $data) {
                        // Filialni yaratish
                        $branch = Branch::create([
                            'name' => $data['name'],
                            'logo' => $data['logo'],
                        ]);


                        Notification::make()
                            ->title('Филиал табыслы жаратылды!')
                            ->success()
                            ->send();
                    })
            ])
            ->columns([
                ImageColumn::make('logo')
                    ->label('Лого')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->sortable()    
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Статус')
                    ->boolean(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Url')
                    ->url(fn ($record) => env('FRONTEND_URL')."/feedback/$record->slug")
                    ->color('primary')
                    ->openUrlInNewTab()
                    ->copyable(fn ($record) => env('FRONTEND_URL')."/feedback/$record->slug")
                    ->copyMessage('URL nusxalandi!')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn ($record) => env('FRONTEND_URL')."/feedback/$record->slug")
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Время создания')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id','desc')
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
            //
        ];
    }
    public static function getNavigationLabel(): string
    {
        return 'Филиалы'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Филиалы'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Филиалы'; // Rus tilidagi ko'plik shakli
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            // 'create' => Pages\CreateBranch::route('/create'),
            // 'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
