<?php

namespace App\Admin\Repositories;

use App\Models\HabitValueLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HabitValueLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
