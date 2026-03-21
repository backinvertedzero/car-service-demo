<?php

namespace app\handlers\car;

use app\exceptions\handlers\CarNotFound;
use app\exceptions\repository\ModelNotFound;
use app\repositories\CarRepository;

class ViewHandler
{
    public function __construct(private readonly CarRepository $repository)
    {
    }

    public function handle(int $id)
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFound $exception) {
            throw new CarNotFound($exception->getMessage());
        }
    }
}