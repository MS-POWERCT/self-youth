<?php

namespace App\Admin\Repositories;

use App\Models\HabitStat as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HabitStat extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
