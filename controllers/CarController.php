<?php

namespace app\controllers;

use app\exceptions\handlers\CarNotFound;
use app\forms\CreateCarForm;
use app\handlers\car\SaveHandler;
use app\handlers\car\ViewHandler;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use Yii;
use Exception;

class CarController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly SaveHandler $saveHandler,
        private readonly ViewHandler $viewHandler,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionCreate()
    {
        $form = new CreateCarForm();
        $form->attributes = Yii::$app->request->post();
        if (!$form->validate()) {
            throw new BadRequestHttpException(json_encode($form->errors));
        }
        try {
            $carDto = $form->makeCarDto();
            $result = $this->saveHandler->handle($carDto);
            if ($result->isNew()) {
                Yii::$app->response->statusCode = 201;
            } else {
                Yii::$app->response->statusCode = 200;
            }
            return $result->getData();
        } catch (Exception $exception) {
            return [
                'success' => false,
                'error' => $exception->getMessage(),
            ];
        }
    }

    public function actionView($id)
    {
        try {
            $car = $this->viewHandler->handle($id);
            return $car;
        } catch (CarNotFound $exception) {
            Yii::$app->response->statusCode = 404;
            return [
                'success' => false,
                'error' => 'Car not found',
            ];
        }  catch (Exception $exception) {
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'error' => $exception->getMessage(),
            ];
        }
    }

    public function actionList()
    {
        //
    }
}