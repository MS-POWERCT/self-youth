<?php

namespace App\Admin\Repositories;

use App\Models\HabitCheckLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HabitCheckLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
