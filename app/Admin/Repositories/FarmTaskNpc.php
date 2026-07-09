<?php

namespace App\Admin\Repositories;

use App\Models\FarmTaskNpc as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmTaskNpc extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
