<?php

namespace App\Admin\Repositories;

use App\Models\Llconfig as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Llconfig extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
