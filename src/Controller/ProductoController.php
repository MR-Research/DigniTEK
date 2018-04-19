<?php
// src/Controller/ArticlesController.php

namespace App\Controller;
use App\Controller\AppController;
use Cake\Event\Event;

class ProductoController extends AppController
{
    public function initialize(){
        parent::initialize();
    }    

    public function index()
    {
        $this->set('activo', 'navProductos');
    }

    public function view($idproducto = null)
    {
        //$this->set('titulo', 'Nombre de la comunidad');
        $this->set('activo', 'navProductos');
        $producto = $this->Producto->get($idproducto);
        $this->set('producto', $producto);
    }    

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['listaproductos']);
    }          

    public function listaproductos(){
        $producto = $this->Producto->find('all');
        foreach ($producto as $row){
            $row->imagen = array('enlace' => $row->imagen, 'id' => $row->idproducto);
            $row->detalle = array('enlace' => 'producto/view/' . $row->idproducto);
        }
        $responseResult = json_encode($producto);
        $this->response->type('json');
        $this->response->body($responseResult);
        return $this->response;        
    }    

    public function add()
    {
        //$this->set('titulo', 'Nombre de la comunidad');
        $this->set('activo', 'navProductos');
        $producto = $this->Producto->newEntity(); 
        if ($this->request->is(['post', 'put'])) {  
            if (!empty($this->request->getData())) {        
                if(!empty($this->request->getData()['image_path'][0]['name']))
                {
                    $file = $this->request->getData()['image_path'][0]; //put the data into a var for easy use                
                    $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension                    
                    $producto->imagen = "portada." . $ext;
                }                
                
                $producto->usuario_comunidad_idcomunidad = "1";
                $producto->usuario_idusuario = "1";            
                $this->Producto->patchEntity($producto, $this->request->getData());
                if ($this->Producto->save($producto)) {
                    //guardar imagen de portada
                    if(!empty($this->request->getData()['image_path'][0]['name']))
                    {
                        $file = $this->request->getData()['image_path'][0]; //put the data into a var for easy use
                        $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                        $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //set allowed extensions
                        if(in_array($ext, $arr_ext))
                        {
                            $dir = WWW_ROOT . 'uploads/' . 'files/' . 'producto/' . $producto->idproducto . '/' . DS; //<!-- app/webroot/img/
                            mkdir($dir, 0755, true);
                            array_map('unlink', glob($dir . "portada.*"));                        
                            if(!move_uploaded_file($file['tmp_name'], $dir . "portada." . $ext)) 
                            {
                                $this->Flash->error(__('Image could not be saved. Please, try again.'));
                            } else {                            
                                $this->Flash->error(__('Archivo subido'));
                                //return $this->redirect(['action' => 'view/' . $producto->idproducto]);
                            }
    
                        } else {
                            $this->Flash->error(__('Extension no valida'));
                            //return $this->redirect(['action' => 'view/' . $producto->idproducto]);                  
                        }
                    }

                    $this->Flash->success(__('Producto creado.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('Error al crear producto.'));
            }            
        } else {            
            $this->set('producto', $producto); 
        }

    }    

    public function edit($idproducto = null)
    {
        $this->set('activo', 'navProductos');
        $producto = $this->Producto->get($idproducto);

        if ($this->request->is(['post', 'put'])) {
            if (!empty($this->request->getData())) {
                if(!empty($this->request->getData()['image_path'][0]['name']))
                {
                    $file = $this->request->getData()['image_path'][0]; //put the data into a var for easy use                
                    $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension                    
                    $producto->imagen = "portada." . $ext;
                }

                $this->Producto->patchEntity($producto, $this->request->getData());
                if ($this->Producto->save($producto)) {
                    //guardar imagen de portada
                    if(!empty($this->request->getData()['image_path'][0]['name']))
                    {
                        $file = $this->request->getData()['image_path'][0]; //put the data into a var for easy use
                        $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                        $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //set allowed extensions
                        if(in_array($ext, $arr_ext))
                        {
                            $dir = WWW_ROOT . 'uploads/' . 'files/' . 'producto/' . $producto->idproducto . '/' . DS; //<!-- app/webroot/img/
                            mkdir($dir, 0755, true);
                            array_map('unlink', glob($dir . "portada.*"));                        
                            if(!move_uploaded_file($file['tmp_name'], $dir . "portada." . $ext)) 
                            {
                                $this->Flash->error(__('Image could not be saved. Please, try again.'));
                            } else {                            
                                $this->Flash->error(__('Archivo subido'));
                                //return $this->redirect(['action' => 'view/' . $producto->idproducto]);
                            }
    
                        } else {
                            $this->Flash->error(__('Extension no valida'));
                            //return $this->redirect(['action' => 'view/' . $producto->idproducto]);                  
                        }
                    }

                    $this->Flash->success(__('Producto actualizado.'));
                    return $this->redirect(['action' => 'view/' . $producto->idproducto]);
                }
            } else {
                $this->Flash->error(__('Error al actualizar producto.'));
            }
        }               

        $this->set('producto', $producto);        
    } 

    public function isAuthorized($user) {
        //auth check
        //return boolean
        return true;
    }     

}