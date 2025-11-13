<?php
class UsersController extends AppController {

    var $name = 'Users';
    function beforeFilter(){

        $this->Auth->allow(
            array(
                'add'
            )
        );
    }
    function add() {
        if ($this->Session->read('Auth.User.menu_conf_usuarios')) {
            if ($this->request->is('post')) {
                //                echo var_dump($this->request->data['User']);
                $foto = "";
                if (isset($this->request->data['User']['fotografia']['name'])){
                    $foto = $this->request->data['User']['fotografia'];
                }
                $this->request->data['User']['fotografia'] =  "";
                //                $this->request->data['User']['password']= $this->Auth->password($this->request->data['User']['password']);
                $this->request->data['User']['created']=date('Y-m-d H:i:s');
                $this->request->data['User']['password']=$this->Auth->password("Palatti.".date("Y"));

                $this->loadModel('Grupo');
                $grupo = $this->Grupo->read(null,$this->request->data['User']['grupo_id']);

                $this->request->data['User']['menu_pendientes'] = $grupo['Grupo']['menu_pendientes'];
                $this->request->data['User']['menu_i_clientes'] = $grupo['Grupo']['menu_i_clientes'];
                $this->request->data['User']['menu_i_id'] = $grupo['Grupo']['menu_i_id'];
                $this->request->data['User']['menu_operacion'] = $grupo['Grupo']['menu_operacion'];
                $this->request->data['User']['menu_certificacion'] = $grupo['Grupo']['menu_certificacion'];
                $this->request->data['User']['menu_documental'] = $grupo['Grupo']['menu_documental'];
                $this->request->data['User']['menu_conf_password'] = $grupo['Grupo']['menu_conf_password'];
                $this->request->data['User']['menu_conf_cat'] = $grupo['Grupo']['menu_conf_cat'];
                $this->request->data['User']['menu_conf_emp'] = $grupo['Grupo']['menu_conf_emp'];
                $this->request->data['User']['menu_conf_usuarios'] = $grupo['Grupo']['menu_conf_usuarios'];
                $this->request->data['User']['menu_conf_pts'] = $grupo['Grupo']['menu_conf_pts'];
                $this->request->data['User']['menu_mensajes'] = $grupo['Grupo']['menu_mensajes'];
                $this->request->data['User']['menu_rh'] = $grupo['Grupo']['menu_rh'];
                $this->request->data['User']['link_c1'] = $grupo['Grupo']['link_c1'];
                $this->request->data['User']['link_c2'] = $grupo['Grupo']['link_c2'];
                $this->request->data['User']['link_c3'] = $grupo['Grupo']['link_c3'];
                $this->request->data['User']['link_c4'] = $grupo['Grupo']['link_c4'];
                $this->request->data['User']['link_t_1'] = $grupo['Grupo']['link_t_1'];
                $this->request->data['User']['link_t_2'] = $grupo['Grupo']['link_t_2'];
                $this->request->data['User']['link_t_3'] = $grupo['Grupo']['link_t_3'];
                $this->request->data['User']['link_t_4'] = $grupo['Grupo']['link_t_4'];
                $this->request->data['User']['link_t_5'] = $grupo['Grupo']['link_t_5'];
                $this->request->data['User']['link_t_6'] = $grupo['Grupo']['link_t_6'];
                $this->request->data['User']['link_t_7'] = $grupo['Grupo']['link_t_7'];
                $this->request->data['User']['link_t_8'] = $grupo['Grupo']['link_t_8'];
                $this->request->data['User']['link_t_9'] = $grupo['Grupo']['link_t_9'];
                $this->request->data['User']['link_t_10'] = $grupo['Grupo']['link_t_10'];
                $this->request->data['User']['link_inv_1'] = $grupo['Grupo']['link_inv_1'];
                $this->request->data['User']['link_inv_2'] = $grupo['Grupo']['link_inv_2'];
                $this->request->data['User']['link_inv_3'] = $grupo['Grupo']['link_inv_3'];
                $this->request->data['User']['link_inv_4'] = $grupo['Grupo']['link_inv_4'];
                $this->request->data['User']['link_inv_5'] = $grupo['Grupo']['link_inv_5'];
                $this->request->data['User']['link_rec_emb_1'] = $grupo['Grupo']['link_rec_emb_1'];
                $this->request->data['User']['link_rec_emb_2'] = $grupo['Grupo']['link_rec_emb_2'];
                $this->request->data['User']['link_rec_emb_3'] = $grupo['Grupo']['link_rec_emb_3'];
                $this->request->data['User']['link_rec_emb_4'] = $grupo['Grupo']['link_rec_emb_4'];
                $this->request->data['User']['link_rec_emb_5'] = $grupo['Grupo']['link_rec_emb_5'];
                $this->request->data['User']['link_rec_emb_6'] = $grupo['Grupo']['link_rec_emb_6'];
                $this->request->data['User']['link_prod_1'] = $grupo['Grupo']['link_prod_1'];
                $this->request->data['User']['link_prod_2'] = $grupo['Grupo']['link_prod_2'];
                $this->request->data['User']['link_prod_3'] = $grupo['Grupo']['link_prod_3'];
                $this->request->data['User']['link_prod_4'] = $grupo['Grupo']['link_prod_4'];
                $this->request->data['User']['link_prod_5'] = $grupo['Grupo']['link_prod_5'];
                $this->request->data['User']['link_prod_6'] = $grupo['Grupo']['link_prod_6'];
                $this->request->data['User']['link_prod_7'] = $grupo['Grupo']['link_prod_7'];
                $this->request->data['User']['link_prod_8'] = $grupo['Grupo']['link_prod_8'];

                $this->User->create();
                if($this->User->save($this->request->data)){
                    $id = $this->User->getInsertID();
                    if ($foto['name']!=""){
                        $unitario = $foto;
                        $filename = getcwd()."/files/palatti/usuarios/".$unitario['name'];
                        move_uploaded_file($unitario['tmp_name'],$filename);
                        $ruta = "/files/palatti/usuarios/".$unitario['name'];
                        $this->request->data['User']['fotografia'] = $ruta;
                        $this->request->data['User']['id'] = $id;
                        $this->User->save($this->request->data);
                    }

                    $this->Session->setFlash(__('El Usuario Se guardo exitosamente', true), 'default' ,array('class'=>'mensaje_exito'));
                    $this->redirect(array('action' => 'index','controller'=>'users'));
                }else{
                    debug($this->User->invalidFields());
                    return false;
                }
            }
            else{
                $this->loadModel('Grupo');
                $this->set('grupos',$this->Grupo->find('list'));
                $this->set('users',$this->User->find('list',array('order'=>'nombre ASC','conditions'=>array('User.status'=>1))));
                $this->set('puestos', $this->puestos );
            }
        }else{
            $this->Session->setFlash(__('Sección restringida', true), 'default' ,array('class'=>'mensaje_error'));
            return $this->redirect(array('action'=>'index','controller'=>'tickets'));
        }
    }
    function password($id = null) {
        if ($this->request->is(array('post', 'put'))) {
            $user = array(
                'id'=>$id,
                'password'=>$this->Auth->password($this->request->data['User']['password'])
            );
            $this->User->create();

            if($this->User->save($user)){
                $this->Session->setFlash(__('La contraseña ha cambiado exitosamente', true), 'default' ,array('class'=>'mensaje_exito'));
                $this->redirect(array('action' => 'index','controller'=>'tickets'));
            }
        }else{
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }


    }

    public function login() {
		$this->layout = 'login';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                    return $this->redirect(array('controller'=>'jugadors','action'=>'index'));
            }else{
                $this->Session->setFlash(__('Usuario y/o password incorrecto.', true), 'default' ,array('class'=>'mensaje_error'));
            }

        }
    }
    public function logout() {
        $this->Session->setFlash('Fin de la sesión', 'default' ,array('class'=>'mensaje_exito'));
        return $this->redirect($this->Auth->logout());
    }

}
?>
