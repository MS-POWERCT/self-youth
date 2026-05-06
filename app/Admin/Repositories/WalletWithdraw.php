<?php

namespace App\Admin\Repositories;

use App\Models\WalletWithdraw as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WalletWithdraw extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
