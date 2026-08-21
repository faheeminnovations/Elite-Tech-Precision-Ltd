<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function withoutContract()
    {
        return $this->model->where('status', 'nocontract')->get();
    }

    public function search(string $term)
    {
        return $this->model
            ->where('name', 'like', "%{$term}%")
            ->orWhere('job_ref', 'like', "%{$term}%")
            ->get();
    }
}
