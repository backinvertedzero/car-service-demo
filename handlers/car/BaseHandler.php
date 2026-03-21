<?php

namespace app\handlers\car;

use app\repositories\CarRepository;

class BaseHandler
{
    public function __construct(protected CarRepository $repository)
    {
    }
}