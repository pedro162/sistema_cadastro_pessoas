<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use App\Models\Telefone;
use App\Models\Logradouro;
use App\Models\PessoaLogradouro;
use \App\Models\Usuario;
use \Core\Utilitarios\Utils;
use \Core\Utilitarios\LoadEnderecoApi;
use Core\Utilitarios\Sessoes;
use \Exception;

class LogradouroController extends BaseController
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

            if((! isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            $idPessoa = (int) $request['get']['cd'];

            $pessoa = new Pessoa();
            $dados = $pessoa->findForId($idPessoa);
            if($dados == false){

                throw new Exception("Registro não encontrado\n");
                
            }

            $this->view->dados = $dados;
            $this->render('logradouro/index', true, 'layoutAdmin');

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
            header('Location:/pessoa/painel?cd='.$idPessoa);
        }	
    }


    public function salvar($request)
    {
        
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            
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

            $idUsuario = $usuario[0]->getIdUsuario();
            $idPessoa = $dados['pessoa'];

            $pessoa = new Pessoa();
            $pessoaLoaded = $pessoa->findForId((int)$idPessoa)[0];

            if($pessoaLoaded == false){

            	throw new Exception("Registro não encontrado\n");
            	
            }

            $logradouro = new Logradouro();

            $logradouro->setCep($dados['cep']);
            $logradouro->setCidade($dados['cidade']);
			$logradouro->setEstado($dados['estado']);
			$logradouro->setBairro($dados['bairro']);
			$logradouro->setEndereco($dados['endereco']);
			$logradouro->setComplemento($dados['complemento']);
			$logradouro->setNumero((int)$dados['numero']);
			$logradouro->setTipo($dados['tipo']);
			$logradouro->setIdUsuario($idUsuario);
			$logradouro->setDtRegistro(date('Y-m-d H:i:s'));

			$errors = $logradouro->getErrors();


			if(strlen($errors) > 0){

				throw new Exception($errors);
				
			}

			$result = $logradouro->save();
			if($result == false){
				throw new Exception('Erro ao salvar registro!');
			}

			$lastIdLogradouro = $logradouro->maxId();

            $logPessoa = new PessoaLogradouro();

            $logPessoa->setIdPessoa($idPessoa);
            $logPessoa->setIdLogradouro($lastIdLogradouro);
            $logPessoa->setStatus('abilitado');
            $logPessoa->setIdUsuario($idUsuario);
            $logPessoa->setDtRegistro(date('Y-m-d H:i:s'));

            $errorsLogPess = $logPessoa->getErrors();

            /**
             * Verifica se os dados informados pelo usuario sao válidos
             */
            
            if(strlen($errorsLogPess) > 0){

                throw new Exception($errorsLogPess);
            }


            /**
             * Salva as informaçoes no banco, caso esteja tudo ok
             */

            $resultLogPess = $logPessoa->save();

            if($resultLogPess == false){
                throw new Exception("Erro ao salvar dados do endereço");
                
            }
           
            
            Transaction::close();

            /**
             * Limpa os dados do fromulario da sessao
             */
            Sessoes::clearForm();

            /**
             * Exibe uma mensagem de sucesso
             */
            Sessoes::sendMessage(['msg', 'success', 'Dados cadastrados com sucesso!']);

            /**
             * Redireciona o candidato para o proximo formulario
             */
            header('Location:/logradouro/index?cd='.$idPessoa);


        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/logradouro/index?cd='.$idPessoa);

        }


    }

    public function editar($request)
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

            if((! isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            if((! isset($request['get']['ps'])) || ($request['get']['ps'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            $idLogradouro = (int) $request['get']['cd'];
            $idPessoa = (int) $request['get']['ps'];

            $logradPessoa = new PessoaLogradouro();

            $dados = $logradPessoa->select(
            	['*'],
            	[
            		['key'=>'idPessoa','val' => $idPessoa,'comparator'=>'=','operator'=>'and'],
            		['key'=>'idLogradouro','val'=> $idLogradouro,'comparator'=>'='],
            	],
            	null, 1, null, true, false

            );
            if($dados == false){

                throw new Exception("Registro não encontrado\n");
                
            }

            
            $this->view->dados = $dados;

            $this->render('logradouro/editar', true, 'layoutAdmin');

            Sessoes::clearMessage();

            Transaction::close();

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/logradouro/index?cd='.$idPessoa);
        }
    }

    public function atualizar($request)
    {

    	try {
            
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');
            
            if((! isset($request['post'])) || (count($request['post']) == 0)){
                throw new Exception('Requisição inválida');
            }

            $dados = $request['post'];

            $idLogradouro = (int) $dados['logradouro'];
            $idPessoa = (int) $dados['pessoa'];

            $logradPessoa = new PessoaLogradouro();

            $logPessLoaded = $logradPessoa->select(
            	['*'],
            	[
            		['key'=>'idPessoa','val' => $idPessoa,'comparator'=>'=','operator'=>'and'],
            		['key'=>'idLogradouro','val'=> $idLogradouro,'comparator'=>'=', 'operator'=>'and'],
            		['key'=>'status','val'=> 'abilitado','comparator'=>'=']
            	],
            	null, 1, null, true, false

            );
            
            if($logPessLoaded == false){

                throw new Exception("Registro não encontrado aqui\n");
                
            }


            $logradouro = $logPessLoaded[0]->logradouro()[0];

            $logradouro->setCidade($dados['cidade']);
			$logradouro->setEstado($dados['estado']);
			$logradouro->setBairro($dados['bairro']);
			$logradouro->setEndereco($dados['endereco']);
			$logradouro->setComplemento($dados['complemento']);
			$logradouro->setNumero((int)$dados['numero']);
			$logradouro->setTipo($dados['tipo']);

			$errors = $logradouro->getErrors();


			if(strlen($errors) > 0){

				throw new Exception($errors);
				
			}

			$result = $logradouro->modify();
			if($result == false){
				throw new Exception('Nenhuma informação foi alterada!');
			}

            $sucess = ['msg', 'success','Registro atualizado com sucesso!'];
            Sessoes::sendMessage($sucess);

            Transaction::close();

            /**
             * Redireciona o candidato para o proximo formulario
             */
            header('Location:/logradouro/index?cd='.$idPessoa);

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/logradouro/editar?cd='.$idLogradouro.'&ps='.$idPessoa);
        }

    	
    }

    public function adicionar()
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');


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

    public function info($request)
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

            if((! isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            if((! isset($request['get']['ps'])) || ($request['get']['ps'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            $idLogradouro = (int) $request['get']['cd'];
            $idPessoa = (int) $request['get']['ps'];

            $logradPessoa = new PessoaLogradouro();

            $logPessLoaded = $logradPessoa->select(
            	['*'],
            	[
            		['key'=>'idPessoa','val' => $idPessoa,'comparator'=>'=','operator'=>'and'],
            		['key'=>'idLogradouro','val'=> $idLogradouro,'comparator'=>'=', 'operator'=>'and'],
            		['key'=>'status','val'=> 'abilitado','comparator'=>'=']
            	],
            	null, 1, null, true, false

            );
            
            if($logPessLoaded == false){

                throw new Exception("Registro não encontrado aqui\n");
                
            }

                       
            $this->view->dados = $logPessLoaded;

            $this->render('logradouro/info', true, 'layoutAdmin');

            Transaction::close();

            Sessoes::clearMessage();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/logradouro/index?cd='.$idPessoa);
        }

    }

    
    public function deletar($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            if((! isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            if((! isset($request['get']['ps'])) || ($request['get']['ps'] <= 0)){
                throw new Exception('Requisição inválida');
            }

            $idLogradouro = (int) $request['get']['cd'];
            $idPessoa = (int) $request['get']['ps'];

            $logradPessoa = new PessoaLogradouro();

            $dados = $logradPessoa->select(
            	['*'],
            	[
            		['key'=>'idPessoa','val' => $idPessoa,'comparator'=>'=','operator'=>'and'],
            		['key'=>'idLogradouro','val'=> $idLogradouro,'comparator'=>'='],
            	],
            	null, 1, null, true, false

            );


            if($dados == false){

                throw new Exception("Registro não encontrado\n");
                
            }

            $dados[0]->setStatus('desabilitado');

            $resultLogPess = $dados[0]->modify();

            $logradouro = $dados[0]->logradouro();
            $logradouro[0]->setStatus('desabilitado');

            $resultLog = $logradouro[0]->modify();

            Transaction::close();

            $success = ['msg', 'success','Registro deletado com sucesso!'];
            Sessoes::sendMessage($success);
            header('Location:/logradouro/index?cd='.(int) $idPessoa);

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){
            
            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/logradouro/index?cd='.(int) $idLogradouro.'&ps='.(int) $idPessoa);
        }
    }

    public function loadCep($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            
            Transaction::startTransaction('connection');

            $cepApi = new LoadEnderecoApi($request['post']['cep']);
            $resultCepApi = $cepApi->getEndereco();

            $this->view->result = json_encode($resultCepApi);
            $this->render('logradouro/ajax', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }


}