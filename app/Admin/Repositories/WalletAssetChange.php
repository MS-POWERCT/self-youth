<?php

namespace App\Admin\Repositories;

use App\Models\WalletAssetChange as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WalletAssetChange extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
