<?php

namespace App\Admin\Repositories;

use App\Models\FarmTask as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmTask extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
