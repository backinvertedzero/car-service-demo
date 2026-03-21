<?php

namespace app\handlers\car;

class ListHandler extends BaseHandler
{
    public function handler(int $page): array
    {
        return $this->repository->findAll($page);
    }
}