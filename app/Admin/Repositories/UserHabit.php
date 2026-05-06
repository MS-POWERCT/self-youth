<?php

namespace App\Admin\Repositories;

use App\Models\UserHabit as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class UserHabit extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
