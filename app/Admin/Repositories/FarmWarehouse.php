<?php

namespace App\Admin\Repositories;

use App\Models\FarmWarehouse as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmWarehouse extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
