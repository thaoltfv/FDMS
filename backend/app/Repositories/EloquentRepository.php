<?php

namespace App\Repositories;

use App\Contracts\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

abstract class EloquentRepository implements BaseRepository
{
    protected Model $model;

    protected bool $withoutGlobalScopes = false;

    protected array $with = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $with = []): BaseRepository
    {
        $this->with = $with;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutGlobalScopes(): BaseRepository
    {
        $this->withoutGlobalScopes = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $data): Model
    {
        return tap($model)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilters(): LengthAwarePaginator
    {
        return $this->model->with($this->with)->paginate();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById(string $id): Model
    {
        if (!Uuid::isValid($id)) {
            throw (new ModelNotFoundException())->setModel(get_class($this->model));
        }

        if (!empty($this->with) || auth()->check()) {
            return $this->findOneBy(['id' => $id]);
        }

        return Cache::remember($id, now()->addHour(), function () use ($id) {
            return $this->findOneBy(['id' => $id]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria): Model
    {
        if (!$this->withoutGlobalScopes) {
            return $this->model->with($this->with)
                ->where($criteria)
                ->orderByDesc('created_at')
                ->firstOrFail();
        }

        return $this->model->with($this->with)
            ->withoutGlobalScopes()
            ->where($criteria)
            ->orderByDesc('created_at')
            ->firstOrFail();
    }

    /**
     * @param $result
     * @param $pageInput
     * @param null $limit
     * @return array
     */
    protected function responseByResult($result, $pageInput, $limit = null): array
    {
        $total = 0;
        $error = false;
        if ($result->totalHits() && is_array($result->totalHits())){
            $total = ($result->totalHits())['value'];
        }
        $response['data'] = $result;

        if (! empty($limit)) {
            [$pagination, $error] = $this->pagination($total, $pageInput, $limit);
            $response['current_page'] = $pagination['current_page'];
            $response['last_page'] = 1;
            $response['per_page'] = $pagination['per_page'];
            $response['total'] = $pagination['total'];
            $response['total_pages'] = $pagination['total_pages'];
        }

        $response['error'] = $error;

        return $response;
    }

    /**
     * @param $total
     * @param $pageInput
     * @param $limit
     * @return array
     */
    private function pagination($total, $pageInput, $limit): array
    {
        $pagination = [
            'count'       => $total,
            'current_page' => $pageInput+1,
            'links'       => [],
            'per_page'     => $limit,
            'total'       => $total,
            'total_pages'  => (int)($total / $limit) + 1,
        ];

        return [$pagination, false];
    }
}
