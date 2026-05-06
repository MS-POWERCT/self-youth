<?php

namespace App\Admin\Repositories;

use App\Models\UserHabitConfig as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class UserHabitConfig extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
