<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                $nom=$this->request->data['nom'];
                $prenom=$this->request->data['prenom'];
                $tel=$this->request->data['tel'];
                $adresse=$this->request->data['adresse'];
                $photo=$this->request->data['photo'];

                $id = $this
                    ->Users
                    ->find()
                    ->select(['id'])
                    ->where(['mail =' => $this->request->data['mail']])->first();

                return $this->redirect([
                        'controller'=>'patients',
                        'action' => 'add',
                        '?'=>[
                            'nom'=>$nom,
                            'id'=>$id['id'],
                            'prenom'=>$prenom,
                            'tel'=>$tel,
                            'adresse'=>$adresse,
                            'photo'=>$photo
                        ]
                    ]);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {

            $user = $this->Auth->identify();

            if ($user) {
                $this->Auth->setUser($user);
                $id= $this
                    ->Users
                    ->find()
                    ->select(['id'])
                    ->where(['mail =' => $this->request->data['mail']])->first();
                $type= $this
                    ->Users
                    ->find()
                    ->select(['type'])
                    ->where(['mail =' => $this->request->data['mail']])->first();
                debug($type['type']);
                if($type['type'] == 'patient'){
                    return $this->redirect($this->Auth->redirectUrl(['controller'=>'patients','action'=>'acceuil','?'=>['id'=>$id['id']]]));
                }elseif ($type['type'] == 'assistante') {
                   return $this->redirect($this->Auth->redirectUrl(['controller'=>'assistants','action'=>'index']));
                }elseif ($type['type'] == 'medecin') {
                   return $this->redirect($this->Auth->redirectUrl(['controller'=>'medecins','action'=>'index']));
                }
            }
            $this->Flash->error('Votre username ou mot de passe est incorrect.');
        return $this->redirect($this->referer());

    }
   }
