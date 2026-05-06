<?php

namespace App\Admin\Repositories;

use App\Models\WalletDeposit as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WalletDeposit extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
