<?php

namespace Ingenius\Banners\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Ingenius\Banners\Models\Banner;

class PaginateBannersAction
{
    // Implementation of banner pagination action
    public function handle(array $filters = []): LengthAwarePaginator {

        $query = Banner::query();

        return table_handler_paginate($filters, $query);
    }
}