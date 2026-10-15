<?php

use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Tables\ActivitiesTable;

function configuredActivityFilter(string $name, array $state = []): BaseFilter
{
    config(['filament-logger.resources.enabled' => false]);

    $livewire = Mockery::mock(HasTable::class);
    $livewire->shouldReceive('getTableFilterState')
        ->with($name)
        ->andReturn($state);

    $table = ActivitiesTable::configure(Table::make($livewire));

    return $table->getFilter($name, withHidden: true);
}

it('handles missing old and new filter values without warnings', function (string $name) {
    $filter = configuredActivityFilter($name);
    $query = Mockery::mock(Builder::class);

    $query->shouldNotReceive('where');

    expect($filter->getIndicators())->toBe([])
        ->and($filter->apply($query, []))->toBe($query);
})->with([
    'old properties' => 'properties->old',
    'new properties' => 'properties->attributes',
]);

it('still applies old and new property filters when values are provided', function (string $name, string $key, string $column) {
    $filter = configuredActivityFilter($name, [$key => 'email']);
    $query = Mockery::mock(Builder::class);

    $query->shouldReceive('where')
        ->once()
        ->with($column, 'like', '%email%')
        ->andReturnSelf();

    expect($filter->getIndicators())->toHaveCount(1)
        ->and($filter->apply($query, [$key => 'email']))->toBe($query);
})->with([
    'old properties' => ['properties->old', 'old', 'properties->old'],
    'new properties' => ['properties->attributes', 'new', 'properties->attributes'],
]);

it('handles missing date filter values without warnings', function () {
    $filter = configuredActivityFilter('created_at');
    $query = Mockery::mock(Builder::class);

    $query->shouldReceive('when')
        ->once()
        ->with(null, Mockery::type(Closure::class))
        ->andReturnSelf();
    $query->shouldNotReceive('whereDate');

    expect($filter->apply($query, []))->toBe($query);
});

it('still applies the date filter when a value is provided', function () {
    $filter = configuredActivityFilter('created_at');
    $query = Mockery::mock(Builder::class);

    $query->shouldReceive('when')
        ->once()
        ->with('2026-10-15', Mockery::type(Closure::class))
        ->andReturnUsing(fn ($date, Closure $callback) => $callback($query, $date));
    $query->shouldReceive('whereDate')
        ->once()
        ->with('created_at', '2026-10-15')
        ->andReturnSelf();

    expect($filter->apply($query, ['logged_at' => '2026-10-15']))->toBe($query);
});
