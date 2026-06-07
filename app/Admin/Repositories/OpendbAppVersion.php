<?php

namespace App\Admin\Repositories;

use App\Models\OpendbAppVersion as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class OpendbAppVersion extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
