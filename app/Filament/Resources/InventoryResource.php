<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('商品')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('在庫数')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->rules([
                        fn ($attribute, $value, $fail) => $value !== null && $value < 0
                            ? $fail('在庫数は0以上で入力してください。')
                            : null,
                        fn ($attribute, $value, $fail) => request()->input('status') === 'sold_out' && (int) $value !== 0
                            ? $fail('売り切れの場合は在庫数を0にしてください。')
                            : null,
                        fn ($attribute, $value, $fail) => request()->input('status') === 'in_stock' && (int) $value <= 0
                            ? $fail('在庫ありの場合は0より大きい在庫数を設定してください。')
                            : null,
                    ]),
                Forms\Components\Select::make('status')
                    ->label('状態')
                    ->options([
                        'in_stock' => '在庫あり',
                        'sold_out' => '売り切れ',
                        'limited' => '残りわずか',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('商品')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('在庫数')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状態')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_stock' => '在庫あり',
                        'sold_out' => '売り切れ',
                        'limited' => '残りわずか',
                        default => $state,
                    })
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状態')
                    ->options([
                        'in_stock' => '在庫あり',
                        'sold_out' => '売り切れ',
                        'limited' => '残りわずか',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInventories::route('/'),
        ];
    }
}
