<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('商品名')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('価格')
                    ->required()
                    ->integer()
                    ->prefix('¥')
                    ->minValue(100)
                    ->step(100)
                    ->rules([
                        fn ($attribute, $value, $fail) => $value !== null && $value % 100 !== 0
                            ? $fail('価格は100円単位で入力してください。')
                            : null,
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('商品説明')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('画像')
                    ->image()
                    ->directory('products'),
                Forms\Components\Toggle::make('is_active')
                    ->label('公開する')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('商品名')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('価格')
                    ->money('JPY', divideBy: 1)
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('画像')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(url('/images/default-product.png')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('公開')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('公開状態')
                    ->placeholder('すべて')
                    ->trueLabel('公開中')
                    ->falseLabel('非公開'),
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
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
