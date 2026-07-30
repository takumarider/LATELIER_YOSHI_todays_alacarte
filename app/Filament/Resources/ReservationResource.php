<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_name')
                    ->label('お名前')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('メールアドレス')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('電話番号')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('reservation_date')
                    ->label('予約日')
                    ->required(),
                Forms\Components\TextInput::make('guest_count')
                    ->label('人数')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Select::make('status')
                    ->label('ステータス')
                    ->options([
                        'pending' => '未確認',
                        'confirmed' => '確定',
                        'cancelled' => 'キャンセル',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('備考')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('お名前')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('メール')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reservation_date')
                    ->label('予約日')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('人数')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('ステータス')
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageReservations::route('/'),
        ];
    }
}
