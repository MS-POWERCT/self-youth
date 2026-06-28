<?php

namespace App\Admin\Repositories;

use App\Models\FarmDeliveryRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class FarmDeliveryRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
