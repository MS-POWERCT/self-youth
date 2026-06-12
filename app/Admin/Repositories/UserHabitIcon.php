<?php

namespace App\Admin\Repositories;

use App\Models\UserHabitIcon as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class UserHabitIcon extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
