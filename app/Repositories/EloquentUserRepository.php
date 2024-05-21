<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Enum\ValueDefault;
use App\Models\Appraiser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentUserRepository extends EloquentRepository implements UserRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';

    public function findPaging()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $branch_id = (int)request()->get('branch_id');
        $role = request()->get('role');
        if (empty($search)) {
            $search = '';
        }
        $query = 'deleted_at is null and ( name ilike ' . "'%" . $search . "%'" . ' or address ilike ' . "'%" . $search . "%'" . ' or email ilike ' . "'%" . $search . "%')";
        if ($branch_id > 0) {
            $query = $query . ' and branch_id = ' . $branch_id;
        }

        if (!empty($role)) {
            $users = User::role($role)->get();
            $userIds = [];
            foreach ($users as $user) {
                array_push($userIds, $user->id);
            }
        }

        $result = QueryBuilder::for($this->model)
            ->with('branch')
            ->whereRaw($query)
            ->where('email', '<>', ValueDefault::ROOT_ADMIN_DEFAULT)
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
        foreach ($result as $key => $value) {
            $result[$key]['role'] = $value->getRoleNames();
            $appraiser = Appraiser::where('user_id', $result[$key]->id)->first();
            if ($appraiser) {
                $result[$key]['is_legal_representative'] = $appraiser->is_legal_representative;
                $result[$key]['appraiser_number'] = $appraiser->appraiser_number;
            }
        }
        return $result;
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()->select()->orderByDesc($this->defaultSort)->get();
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
     */
    public function getUser($id)
    {
        $user = $this->model->query()
            ->with([
                'branch',
                'appraiser:id,branch_id,appraiser_number,appraise_position_id,name,user_id',
                'appraiser.appraisePosition:id,acronym,description',
                'appraiser.appraiserBranch:id,acronym,address,name'
            ])
            ->where('id', '=', $id)
            ->orderByDesc($this->allowedSorts)
            ->whereNull('deleted_at')
            ->first();
        if (isset($user)) {
            $user['role'] = $user->getRoleNames();
        } else {
            $user = null;
        }
        return $user;
    }

    /**
     * @param $email
     * @return Builder|Model|object|null
     */
    public function checkExitUserByEmail($email)
    {
        $user = $this->model->query()
            //->where('email', $email)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->first();
        if (isset($user)) {
            $user['role'] = $user->getRoleNames();
        } else {
            $user = null;
        }

        return $user;
    }

    /**
     * @param $email
     * @return Builder|Model|object|null
     */
    public function findUserByEmail($email)
    {
        return $this->model->query()
            //->where('email', $email)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        return parent::update($model, $data);
    }

    /**
     * @param array $objects
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function createUser(array $objects)
    {
        $array = [
            'id' => Uuid::uuid4()->toString(),
            'name' => $objects['name'] ?? "",
            'email' => mb_strtolower($objects['email']) ?? "",
            'phone' => $objects['phone'] ?? "",
            'branch_id' => $objects['appraiser']['branch_id'] ?? null,
            'address' => $objects['address'] ?? "",
            'appraisers_number' => $objects['appraisers_number'] ?? "",
            'mailing_address' => mb_strtolower($objects['mailing_address']) ?? "",
            'customer_group_id' => $objects['customer_group_id'] ?? null,
        ];
        $id = $this->model->query()->insertGetId($array);
        if (isset($objects['appraiser'])) {
            Appraiser::insert([
                'name' => $objects['name'] ?? "",
                'appraiser_number' => $objects['appraiser']['appraiser_number'] ?? "",
                'appraise_position_id' => $objects['appraiser']['appraise_position_id'] ?? null,
                'branch_id' => $objects['appraiser']['branch_id'] ?? null,
                'user_id' => $id,
            ]);
        }
        $user = $this->model->query()->find($id);
        $getRole = Role::query()->where('role_name', '=', $objects['role'])->first();
        $user->assignRole($getRole->id);
        return $user;
    }

    /**
     * @param $id
     * @param array $objects
     * @param $roleUpdate
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function updateUser($id, array $objects, $roleUpdate)
    {
        $array = [
            'name' => $objects['name'] ?? "",
            'phone' => $objects['phone'] ?? "",
            'branch_id' => $objects['appraiser']['branch_id'] ?? null,
            'address' => $objects['address'] ?? "",
            'appraisers_number' => $objects['appraisers_number'] ?? "",
            'image' => $objects['image'] ?? "",
            'mailing_address' => $objects['mailing_address'] ?? "",
            'customer_group_id' => $objects['customer_group_id'] ?? null,
        ];

        $this->model->query()
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($array);
        $user = $this->model->query()->find($id);
        $user->getRoleNames();
        $roles = $user['roles'];

        if (!($roles[0]['role_name'] == $objects['role'])) {
            $getRole = Role::query()->where('role_name', '=', $objects['role'])->first();
            $user->syncRoles($getRole->id);
        }
        // $user->syncRoles($objects['role']);
        if (isset($objects['appraiser'])) {
            Appraiser::query()->where('user_id', $id)
                ->update([
                    'name' => $objects['name'] ?? "",
                    'branch_id' => $objects['appraiser']['branch_id'] ?? null,
                    'appraise_position_id' => $objects['appraiser']['appraise_position_id'] ?? null,
                    'appraiser_number' => $objects['appraiser']['appraiser_number'] ?? "",
                ]);
        }
        return $user;
    }

    /**
     * @param $id
     * @return int
     */
    public function deleteUser($id): int
    {
        if (Appraiser::where('user_id', $id)->exists())
            Appraiser::where('user_id', $id)->delete();

        return $this->model->query()
            ->where('id', $id)
            ->where('email', '<>', ValueDefault::ROOT_ADMIN_DEFAULT)
            ->update(['deleted_at' => Carbon::now()]);
    }

    public function validateToken($token)
    {
        return $this->model->query()
            ->where('token', '=', $token)
            ->first();
    }

    /**
     * @param $id
     * @return int
     */
    public function deactiveUser($id): int
    {
        // if(Appraiser::where('user_id',$id)->exists())
        //     Appraiser::where('user_id',$id)->delete();

        return $this->model->query()
            ->where('id', $id)
            ->update(['status_user' => 'deactive']);
    }

    /**
     * @param $id
     * @return int
     */
    public function activeUser($id): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['status_user' => 'active']);
    }

    /**
     * @param $id
     * @return int
     */
    public function isntLegalUser($id): int
    {
        return Appraiser::where('user_id', $id)
            ->update(['is_legal_representative' => 0]);
    }

    /**
     * @param $id
     * @return int
     */
    public function isLegalUser($id): int
    {
        return Appraiser::where('user_id', $id)
            ->update(['is_legal_representative' => 1]);
    }


    /**
     * @param $id
     * @return int
     */
    public function resetUserPasswordNew($id): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['is_reset_password' => 1]);
    }
}
