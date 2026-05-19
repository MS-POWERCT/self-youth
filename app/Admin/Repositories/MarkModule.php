<?php

namespace App\Admin\Repositories;

use App\Models\MarkModule as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MarkModule extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
