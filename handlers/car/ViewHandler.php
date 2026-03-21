<?php

namespace app\handlers\car;

use app\exceptions\handlers\CarNotFound;
use app\exceptions\repository\ModelNotFound;

class ViewHandler extends BaseHandler
{
    public function handle(int $id)
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFound $exception) {
            throw new CarNotFound($exception->getMessage());
        }
    }
}