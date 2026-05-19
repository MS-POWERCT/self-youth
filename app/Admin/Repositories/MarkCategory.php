<?php

namespace App\Admin\Repositories;

use App\Models\MarkCategory as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MarkCategory extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
