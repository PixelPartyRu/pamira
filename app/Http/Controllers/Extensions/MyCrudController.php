<?php

namespace App\Http\Controllers\Extensions;

use Serverfireteam\Panel\CrudController;
class MyCrudController extends CrudController{
    //Путь к расширениям админки
    public static $ext_apth = 'App\Http\Controllers\AdminExt';
    public $edutUrl = null;
    public function __construct(\Lang $lang)
    {
       // $this->entity = $params['entity'];

        $route = \App::make('route');
        $this->lang = $lang;
        $this->route = $route;
        $routeParamters = $route::current()->parameters();
        if(isset($routeParamters['entity'])) {
        $this->setEntity($routeParamters['entity']);
        }

    }
        public function set_scripts($scripts) {
        $global_scripts = \Config::get('global_scripts.scripts');
        view()->share('global_scripts', $global_scripts);
        view()->share('scripts', $scripts);
    }

    public function viewWithMessage($message) {
        return \View::make('panelViews::edit', array('title'		 => $this->entity,
					                'edit' 		 => "",
							'helper_message' => $message));
        
    }
    
        public function returnView() {

        $configs = \Serverfireteam\Panel\Link::returnUrls();

        if (!isset($configs) || $configs == null) {
            throw new \Exception('NO URL is set yet !');
        } else if (!in_array($this->entity, $configs)) {
            throw new \Exception('This url is not set yet!');
        } else {
            $view_data = array(
                'grid' => $this->grid,
                'filter' => $this->filter,
                'title' => $this->entity,
                'current_entity' => $this->entity,
                'import_message' => (\Session::has('import_message')) ? \Session::get('import_message') : ''
            );



            return \View::make('panelViews::all', $view_data);
        }
    }
    
   public function returnEditView() {
        $configs = \Serverfireteam\Panel\Link::returnUrls();

        if (!isset($configs) || $configs == null) {
            throw new \Exception('NO URL is set yet !');
        } else if (!in_array($this->entity, $configs)) {
            throw new \Exception('This url is not set yet !');
        } else {
            return \View::make('panelViews::edit', array('title' => $this->entity,
                        'edit' => $this->edit,
                        'helper_message' => $this->helper_message));
        }
    }
    
    public function customEditView($view) {
        $configs = \Serverfireteam\Panel\Link::returnUrls();

        if (!isset($configs) || $configs == null) {
            throw new \Exception('NO URL is set yet !');
        } else if (!in_array($this->entity, $configs)) {
            throw new \Exception('This url is not set yet !');
        } else {
            return \View::make($view, array('title' => $this->entity,
                        'edit' => $this->edit,
                        'helper_message' => $this->helper_message));
        }
    }

}