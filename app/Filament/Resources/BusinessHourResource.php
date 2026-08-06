<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessHourResource\Pages;
use App\Models\BusinessHour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessHourResource extends Resource
{
    protected static ?string $model = BusinessHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->label('曜日')
                    ->options([
                        'monday' => '月曜日',
                        'tuesday' => '火曜日',
                        'wednesday' => '水曜日',
                        'thursday' => '木曜日',
                        'friday' => '金曜日',
                        'saturday' => '土曜日',
                        'sunday' => '日曜日',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('open_time')
                    ->label('開店時間')
                    ->placeholder('09:00')
                    ->maxLength(255)
                    ->rules(fn (Forms\Get $get): array => [
                        'nullable',
                        'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                        function ($attribute, $value, $fail) use ($get): void {
                            $isClosed = filter_var($get('is_closed'), FILTER_VALIDATE_BOOLEAN);

                            if (! $isClosed && blank($value)) {
                                $fail('営業日の場合は開店時間を入力してください。');
                            }
                        },
                        function ($attribute, $value, $fail) use ($get): void {
                            $isClosed = filter_var($get('is_closed'), FILTER_VALIDATE_BOOLEAN);
                            $closeTime = $get('close_time');

                            if ($isClosed || blank($value) || blank($closeTime)) {
                                return;
                            }

                            $openMinutes = (int) substr($value, 0, 2) * 60 + (int) substr($value, 3, 2);
                            $closeMinutes = (int) substr($closeTime, 0, 2) * 60 + (int) substr($closeTime, 3, 2);

                            if ($closeMinutes <= $openMinutes) {
                                $fail('閉店時間は開店時間より後に設定してください。');
                            }
                        },
                    ]),
                Forms\Components\TextInput::make('close_time')
                    ->label('閉店時間')
                    ->placeholder('20:00')
                    ->maxLength(255)
                    ->rules(fn (Forms\Get $get): array => [
                        'nullable',
                        'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                        function ($attribute, $value, $fail) use ($get): void {
                            $isClosed = filter_var($get('is_closed'), FILTER_VALIDATE_BOOLEAN);
                            $openTime = $get('open_time');

                            if ($isClosed || blank($value) || blank($openTime)) {
                                return;
                            }

                            $openMinutes = (int) substr($openTime, 0, 2) * 60 + (int) substr($openTime, 3, 2);
                            $closeMinutes = (int) substr($value, 0, 2) * 60 + (int) substr($value, 3, 2);

                            if ($closeMinutes <= $openMinutes) {
                                $fail('閉店時間は開店時間より後に設定してください。');
                            }
                        },
                    ]),
                Forms\Components\Toggle::make('is_closed')
                    ->label('休業日')
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
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('曜日')
                    ->searchable(),
                Tables\Columns\TextColumn::make('open_time')
                    ->label('開店時間')
                    ->searchable(),
                Tables\Columns\TextColumn::make('close_time')
                    ->label('閉店時間')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('休業')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('曜日')
                    ->options([
                        'monday' => '月曜日',
                        'tuesday' => '火曜日',
                        'wednesday' => '水曜日',
                        'thursday' => '木曜日',
                        'friday' => '金曜日',
                        'saturday' => '土曜日',
                        'sunday' => '日曜日',
                    ]),
                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label('休業日')
                    ->placeholder('すべて')
                    ->trueLabel('休業')
                    ->falseLabel('営業'),
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
            'index' => Pages\ManageBusinessHours::route('/'),
        ];
    }
}
