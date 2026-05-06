<?php

namespace App\Admin\Repositories;

use App\Models\WalletAsset as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WalletAsset extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
