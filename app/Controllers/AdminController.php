<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Candidato;
use \App\Models\Usuario;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class AdminController extends BaseController
{
    
	public function index($request)
    {
    	try {
            
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            

            $this->setMenu('_adm/adminMenu');
            $this->setFooter('_adm/adminFooter');


            $this->view->registro = $usuario;
            $this->render('admin/index', true, 'layoutAdmin');
            
            Transaction::close();
            Sessoes::clearMessage();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            $this->view->result = json_encode($error);
            $this->render('candidato/ajax', false);
            Sessoes::sendMessage($error);
        }	
    }


    public function recibo()
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            $this->setMenu('_adm/adminMenu');
            $this->setFooter('_adm/adminFooter');


            

            setlocale('LC_TIME', 'portuguese');
            date_default_timezone_set('America/Sao_Paulo');

            $data  = strftime('%d de %B de %Y', strtotime(date('Y-m-d')));
            $this->view->data = $data;
            $this->render('admin/recibo/index', true, 'layoutAdmin');

            Sessoes::clearMessage();
            Transaction::close();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/');
        }   
    }


    



}