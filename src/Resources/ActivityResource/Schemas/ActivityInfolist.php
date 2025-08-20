<?php

namespace Z3d0X\FilamentLogger\Resources\ActivityResource\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['md' => 4])
            ->components([
                Group::make()
                    ->columnSpan(['md' => 3])
                    ->schema([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextEntry::make('causer.name')
                                    ->label(__('filament-logger::filament-logger.resource.label.user')),

                                TextEntry::make('subject_type')
                                    ->label(__('filament-logger::filament-logger.resource.label.subject'))
                                    ->formatStateUsing(function (?Model $record, ?string $state) {
                                        /** @var Activity&ActivityModel $record */
                                        return $state ? Str::of($state)->afterLast('\\')->headline().' # '.$record->subject_id : '-';
                                    }),

                                TextEntry::make('description')
                                    ->label(__('filament-logger::filament-logger.resource.label.description'))
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['md' => 1])
                    ->schema([
                        Section::make()->schema([
                            TextEntry::make('log_name')
                                ->label(__('filament-logger::filament-logger.resource.label.type'))
                                ->formatStateUsing(function (?Model $record): string {
                                    /** @var Activity&ActivityModel $record */
                                    return $record->log_name ? ucwords($record->log_name) : '-';
                                }),

                            TextEntry::make('event')
                                ->label(__('filament-logger::filament-logger.resource.label.event'))
                                ->formatStateUsing(function (?Model $record): string {
                                    /** @phpstan-ignore-next-line */
                                    return $record?->event ? ucwords($record?->event) : '-';
                                }),

                            TextEntry::make('created_at')
                                ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                                ->formatStateUsing(function (?Model $record): string {
                                    /** @var Activity&ActivityModel $record */
                                    return $record->created_at ? "{$record->created_at->format(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                                }),
                        ]),
                    ]),

                Section::make()
                    ->columns()
                    ->columnSpan(['md' => 4])
                    ->visible(fn ($record) => $record->properties?->count() > 0)
                    ->schema(function (?Model $record) {
                        /** @var Activity&ActivityModel $record */
                        $properties = $record->properties->except(['attributes', 'old']);

                        $schema = [];

                        if ($properties->count()) {
                            $schema[] = KeyValueEntry::make('properties')
                                ->label(__('filament-logger::filament-logger.resource.label.properties'))
                                ->columnSpanFull();
                        }

                        if ($old = $record->properties->get('old')) {
                            $schema[] = KeyValueEntry::make('old')
                                ->label(__('filament-logger::filament-logger.resource.label.old'))
                                ->formatStateUsing(fn () => $old);
                        }

                        if ($attributes = $record->properties->get('attributes')) {
                            $schema[] = KeyValueEntry::make('attributes')
                                ->label(__('filament-logger::filament-logger.resource.label.new'))
                                ->formatStateUsing(fn () => $attributes);
                        }

                        return $schema;
                    }),
            ]);
    }
}
