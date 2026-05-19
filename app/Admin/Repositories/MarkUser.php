<?php

namespace App\Admin\Repositories;

use App\Models\MarkUser as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MarkUser extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
