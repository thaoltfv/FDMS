<?php

use App\Contracts\RoleRepository;
use App\Contracts\UserRepository;
use App\Enum\RoleDefault;
use App\Models\LoginHistory;
use App\Models\Role;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\Seeder;


class UsersTableSeeder extends Seeder
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/user.csv'));
            $sheets->map(function ($value) {
                return $this->userRepository->createUser(
                        [
                            'id' => Uuid::uuid4()->toString(),
                            'email' => $value['email'],
                            'name' => $value['name'],
                            'phone' => $value['phone'],
                            'branch_id' => $value['branch_id'],
                            'mailing_address' => $value['mailing_address'],
                            'role'=> RoleDefault::USER['role'],
                        ]
                    );
            });
        });
    }
}
