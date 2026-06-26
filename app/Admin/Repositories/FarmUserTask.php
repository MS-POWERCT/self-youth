<?php

namespace App\Admin\Repositories;

use App\Models\FarmUserTask as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmUserTask extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
