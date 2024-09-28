<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserOnlineWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNot('last_activity', null)
                    ->orderBy('last_activity', 'desc'),
            )
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('last_activity')
                    ->since()
            ])
            ->poll('60s');
    }
}
