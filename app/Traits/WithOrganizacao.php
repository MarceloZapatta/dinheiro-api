<?php

namespace App\Traits;

use App\Scopes\OrganizacaoScope;

trait WithOrganizacao {
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new OrganizacaoScope);
    }
}