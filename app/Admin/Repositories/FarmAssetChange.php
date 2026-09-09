<?php

namespace App\Admin\Repositories;

use App\Models\FarmAssetChange as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmAssetChange extends EloquentRepository
{
    protected $eloquentClass = Model::class;
}
