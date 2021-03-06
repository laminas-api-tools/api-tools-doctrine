<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Controller;

use Laminas\ApiTools\ContentNegotiation\ViewModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\Mvc\Controller\AbstractActionController;

class DoctrineAutodiscoveryController extends AbstractActionController
{
    /** @var DoctrineAutodiscoveryModel */
    protected $model;

    /**
     * Constructor
     */
    public function __construct(DoctrineAutodiscoveryModel $model)
    {
        $this->model = $model;
    }

    /** @return ViewModel */
    public function discoverAction()
    {
        $module  = $this->params()->fromRoute('name');
        $version = $this->params()->fromRoute('version');
        $adapter = $this->params()->fromRoute('object_manager_alias');
        $data    = $this->model->fetchFields($module, $version, $adapter);

        return new ViewModel(['payload' => $data]);
    }
}
