<?php

namespace App\Repositories;

use App\Contracts\CustomerRepository;
use App\Enum\ValueDefault;
use App\Models\Customer;
use App\Models\CustomerPic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentCustomerRepository extends EloquentRepository implements CustomerRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'id';

    public function findPaging()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $id = request()->get('id');
        $name = request()->get('name');
        $status = request()->get('status');
        $taxCode = request()->get('tax_code');
        $createdBy = request()->get('created_by');
        $createdDate = request()->get('created_date');
        $address = request()->get('address');
        $orderBy = request()->get('order');
        $sort = request()->get('sort');

        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'desc';
        }

        if (!$sort) {
            $sort = $this->allowedSorts;
        }
        if (empty($search)) {
            $search = '';
        }
        $search =urldecode($search);
        $query = '(name ilike ' . "'%" . $search . "%'" . ' or CAST(id AS TEXT) like ' . "'%" . str_replace(['kh_'], '',urldecode(mb_strtolower($search))) . "%'" . ' or phone like ' . "'%" . $search . "%'" . ' or address ilike ' . "'%" . $search . "%')";

        if (!in_array($status, [ValueDefault::ACTIVE_STATUS, ValueDefault::INACTIVE_STATUS])) {
            $status = ValueDefault::ACTIVE_STATUS;
        }
        $query .= " and status = '" . $status . "'";

        if ($id) {
            $id = str_replace(['kh_'], '',urldecode(mb_strtolower($id)));
            $query .= ' and id = ' . $id;
        }
        if ($name) {
            $name = urldecode($name);
            $query .= ' and name ilike ' . "'%" . $name . "%'";
        }

        if ($taxCode) {
            $query .= ' and tax_code ilike ' . "'%" . $taxCode . "%'";
        }
        if ($address) {
            $address = urldecode($address);
            $query .= ' and address ilike ' . "'%" . $address . "%'";
        }
        if ($createdBy) {
            $createdBy = urldecode($createdBy);
            $query .= ' and created_by ilike ' . "'%" . $createdBy . "%'";
        }
        if ($createdDate) {
            $query .= " and created_date = '" . $createdDate . "'";
        }
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->with('pic')
            ->orderBy($sort, $orderBy)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll(array $objects = [])
    {
        $search = isset($objects['key']) ? $objects['key'] : '';
        $result = $this->model->query()->select()->with('pic');
        $result->where(function($query) use ($search) {
            $query->orWhere('name', 'LIKE', "%" . $search . "%");
            $query->orWhere('address', 'LIKE', "%" . $search . "%");
            $query->orWhere('phone', 'LIKE', "%" . $search . "%");
        });
        $results = $result
            ->where('status', '=', ValueDefault::ACTIVE_STATUS)
            ->orderByDesc($this->defaultSort)
            ->get();
        $results->append('full_info');
        return $results;
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
     */
    public function findById($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->with('pic')
            ->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createCustomer(array $objects): bool
    {

        $customer = new Customer($objects);
        $customerId = QueryBuilder::for($customer)
            ->insertGetId($customer->attributesToArray());
        if (isset($objects['pic'])) {
            $pic = [];
            foreach ($objects['pic'] as $customerPic) {
                $customerPic['customer_id'] = $customerId;
                $pic[] = (new CustomerPic($customerPic))->attributesToArray();
            }
            CustomerPic::query()->insert($pic);
        }
        return true;
    }

    /**
     * @param $id
     * @param array $objects
     * @return bool
     */
    public function updateCustomer($id, array $objects): bool
    {

        $customer = new Customer($objects);
        $customer->newQuery()->updateOrInsert(['id' => $id], $customer->attributesToArray());
        CustomerPic::query()->where('customer_id', '=', $id)->delete();
        if (isset($objects['pic'])) {
            $pic = [];
            foreach ($objects['pic'] as $customerPic) {
                $customerPic['customer_id'] = $id;
                $pic[] = (new CustomerPic($customerPic))->attributesToArray();
            }
            CustomerPic::query()->insert($pic);
        }
        return true;
    }

    /**
     * @param $id
     * @return int
     */
    public function deleteCustomer($id): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $ids
     * @return bool
     */
    public function updateCustomersStatus($ids): bool
    {
        $status = request()->get('status');
        if ($status) {
            $this->model->query()
                ->whereIn('id', $ids['ids'])
                ->update(['status' => $status]);
            return true;
        }
        return false;
    }
}
