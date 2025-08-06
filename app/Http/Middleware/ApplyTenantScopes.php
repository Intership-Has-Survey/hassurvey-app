<?php

namespace App\Http\Middleware;

use App\Models\DaftarAlat;
use App\Models\Project;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApplyTenantScopes
{
    public function handle(Request $request, Closure $next)
    {
        DaftarAlat::addGlobalScope(
            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
        );

        // Project::addGlobalScope(
        //     fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
        // );

        return $next($request);
    }
}
