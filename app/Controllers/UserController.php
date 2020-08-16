<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Candidato;
use \Core\Database\Transaction;
use Core\Utilitarios\Sessoes;
use \Exception;

/**
 * 
 */
class UserController extends BaseController
{
	
	public function index()
    {
        try {
        	
        	Sessoes::sessionInit();//inicia a sessao
			
            Transaction::startTransaction('connection');
            
            $this->setMenu();
            $this->setFooter();

            $this->render('site/login/index', true);

            Transaction::close();

            Sessoes::sessionEnde();
            

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e) {
            
            Transaction::rollback();
        }
    }
	
	public function logar($request)
    {
    	try {
    		
			Transaction::startTransaction('connection');

			if((!isset($request['post']['usuario'])) || (!isset($request['post']['senha']))) {
                throw new Exception("Dados inálidos\n");
            }


            $login = $request['post']['usuario'];
            $senha = $request['post']['senha'];
            
            $usuario = new Usuario();
            $result = $usuario->findLogin($login, $senha);

            if( $result == false){
            	throw new Exception('Usuario ou senha inválido');
            }

        	Transaction::close();

            Sessoes::usuarioInit($result);
            Sessoes::clearMessage();

            switch ($result[0]->getTipoUsuario()) {
            	case 'candidato':
            		header('Location:/candidato/index');
            		break;

            	case 'empresa':
            		header('Location:/empresa/index');
            		break;
            	
            	case 'admin':
            		header('Location:/');
            		break;
            	default:
            		throw new Exception('Usuario inválido'.PHP_EOL);
            		
            		break;
            }

		}
		 catch (\PDOException $e) {

			Transaction::rollback();

		} catch (Exception $e) {
			
			Transaction::rollback();

			$error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
			Sessoes::sendMessage($error);
			
        	header('Location:/usuario/index');
		}
    }

    public function logout()
    {
        /**
         * Encerra a cessao
         */
        Sessoes::sessionEnde();

        /**
         * Limpa as mensagens existenstes
         */
        Sessoes::clearMessage();
        header('Location:/usuario/index');

    }

    public function adicionar()
    {
        try {

            Transaction::startTransaction('connection');

            $this->setMenu();
            $this->setFooter();
            
            $this->render('usuario/adicionar', true);

            Sessoes::clearMessage();
            
            Transaction::close();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            Sessoes::sendMessage($error);
        }
    }


    public function salvar($request)
    {
        try {
            
            Transaction::startTransaction('connection');

            if((! isset($request['post'])) || (count($request['post']) == 0)){
                throw new Exception('Requisição inválida');
            }

            $dados = $request['post'];

            /**
             * Armazena os dados do formulario na sessao 
             * para previnir de possiveis erros
             */
            Sessoes::storangeForm($dados);

            $usuario = new Usuario();

            $usuario->setLogin($dados['email']);
            $usuario->setSenha($dados['senha']);
            $usuario->setTipoUsuario('candidato');
            $usuario->setDtRegistro(date('Y-m-d H:i:s'));
            
            


            /*
            
            phone_1
            phone_2*/

            /**
             * Verifica se os dados informados pelo usuario sao válidos
             */
            $errosUsuario = $usuario->getErrors();

            if((strlen($errosUsuario) > 0) || (strlen($errosUsuario) > 0)){

                throw new Exception($errosUsuario);
            }


            /**
             * Salva as informaçoes no banco, caso esteja tudo ok
             */

            $resultUser = $usuario->save();

            if($resultUser == false){
                throw new Exception("Erro ao salvar registros");
                
            }

           
            Transaction::close();

            /**
             * Limpa os dados do fromulario da sessao
             */
            Sessoes::clearForm();
            Sessoes::sendMessage(['msg', 'success', 'Dados cadastrados com sucesso!']);

            /**
             * Redireciona o candidato para o proximo formulario
             */
            //header('Location:/curso/adicionar');


        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/candidato/adicionar');

        }


    }



    
}