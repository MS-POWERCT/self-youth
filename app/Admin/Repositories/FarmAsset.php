<?php

namespace App\Admin\Repositories;

use App\Models\FarmAsset as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmAsset extends EloquentRepository
{
    protected $eloquentClass = Model::class;
}
