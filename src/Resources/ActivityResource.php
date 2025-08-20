<?php

namespace Jacobtims\FilamentLogger\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Pages\ListActivities;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Pages\ViewActivity;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Schemas\ActivityInfolist;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Tables\ActivitiesTable;
use Spatie\Activitylog\ActivitylogServiceProvider;

class ActivityResource extends Resource
{
    protected static ?string $label = 'Activity Log';

    protected static ?string $slug = 'activity-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getCluster(): ?string
    {
        return config('filament-logger.resources.cluster');
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'view' => ViewActivity::route('/{record}'),
        ];
    }

    public static function getModel(): string
    {
        return ActivitylogServiceProvider::determineActivityModel();
    }

    public static function getLabel(): string
    {
        return __('filament-logger::filament-logger.resource.label.log');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-logger::filament-logger.resource.label.logs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-logger.resources.navigation_group', 'Settings'));
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-logger::filament-logger.nav.log.label');
    }

    public static function getNavigationIcon(): string
    {
        return __('filament-logger::filament-logger.nav.log.icon');
    }

    public static function isScopedToTenant(): bool
    {
        return config('filament-logger.scoped_to_tenant', true);
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-logger.navigation_sort', null);
    }
}
