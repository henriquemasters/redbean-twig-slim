<?php

namespace App\Controller;

use Slim\Container;

class BaseController {

    protected $view;
    protected $logger;
    protected $flash;
    protected $allroutes;
    protected $upload_dir;

    /**
     * 
     * @param Container $c
     */
    public function __construct(Container $c) {
        $this->view = $c->get('view');
        $this->logger = $c->get('logger');
        $this->flash = $c->get('flash');
        $this->allroutes = $c->get('allroutes');
        $this->upload_dir = $c->get('upload_dir');
    }

}
