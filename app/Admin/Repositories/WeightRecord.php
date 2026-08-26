<?php

namespace App\Admin\Repositories;

use App\Models\WeightRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WeightRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
