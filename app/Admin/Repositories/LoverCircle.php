<?php

namespace App\Admin\Repositories;

use App\Models\LoverCircle as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class LoverCircle extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
