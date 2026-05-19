<?php

namespace App\Admin\Repositories;

use App\Models\MarkItem as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MarkItem extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
