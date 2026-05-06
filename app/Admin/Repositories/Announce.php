<?php

namespace App\Admin\Repositories;

use App\Models\Announce as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Announce extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
