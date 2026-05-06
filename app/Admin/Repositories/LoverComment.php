<?php

namespace App\Admin\Repositories;

use App\Models\LoverComment as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class LoverComment extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
