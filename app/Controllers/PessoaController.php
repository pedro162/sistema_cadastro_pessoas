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
use Core\Utilitarios\Sessoes;
use \Exception;

class PessoaController extends BaseController
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

            $pessoa = new Pessoa();
            $dados = $pessoa->select(['*'], [
                    [   
                        'key'=>'status',
                        'val'=>'abilitado',
                        'comparator'=>'='
                    ]
                ],[
                    [
                        'key'=>'nome',
                        'order'=>'asc'
                    ]
                ], 0, 10, true,false
            );

            $this->view->dados = $dados;
            $this->render('pessoa/index', true, 'layoutAdmin');
            Sessoes::clearMessage();
            Transaction::close();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            Sessoes::sendMessage($error);
            header('Location:/pessoa/index');
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

            $idUsuario = $curso = $usuario[0]->getIdUsuario();
            $dados = $request['post'];


            /**
             * Armazena os dados do formulario na sessao 
             * para previnir de possiveis erros
             */
            Sessoes::storangeForm($dados);
            

            /**
             * Dados da tabela de pessoa
             */
            $pessoa = new Pessoa();

            $pessoa->setSobrenome($dados['sobrenome'] ?? '');
            $pessoa->setCpfCnpj($dados['cpf_cnpj'] ?? '');
            //$pessoa->getRgIe($dados['rg'] ?? '');
            //$pessoa->setImg($dados['post'] ?? '');
            //$pessoa->setDtNascimento($dados['nascimento'] ?? '');
            $pessoa->setNome($dados['nome'] ?? '');
            $pessoa->setSobrenome($dados['sobrenome'] ?? '');


            if(! isset($dados['tipo'])){
                throw new Exception("Erro na requisição, recarrege a pagina\n");
                
            }

            /**
             * Verifica o tipo de cadastro CPF ou CNPJ
             */
            if($dados['tipo_pessoa'] == 'cpf'){
                
                $pessoa->setSexo($dados['sexo'] ?? '');

            }elseif($dados['tipo_pessoa'] == 'cnpj'){

                //$pessoa->setSexo('n');
                $pessoa->getRgIe($dados['ie'] ?? '');
                $pessoa->setTipo('cnpj');
            }


            if(isset($dados['email']) && (strlen($dados['email']) > 0)){

                $pessoa->setEmail($dados['email'] ?? '');
            }
            //$pessoa->setRgIe();
            //$pessoa->setDtNascimento(date('Y-m-d H:i:s'));
            $pessoa->setStatus('abilitado');
            $pessoa->setIdUsuario($idUsuario);
            $pessoa->setDtRegistro(date('Y-m-d H:i:s'));

            $errorsPessoa = $pessoa->getErrors();

            if(strlen($errorsPessoa) > 0){
                throw new Exception($errorsPessoa);
                
            }


            /**
             * Salva as informaçoes no banco, caso esteja tudo ok
             */
            $resultPessoa = $pessoa->save();

            if($resultPessoa == false){
                throw new Exception("Erro ao salvar dados da pessoa");
                
            }

            $lastIdPessoa = $pessoa->maxId();

            /**
             * Dados da tabela de telefone
             */

            $phones = ['phone_1', 'phone_2'];

            for ($i=0; !($i == count($phones)); $i++) { 
                
                if(isset($dados[$phones[$i]]) && (strlen($dados[$phones[$i]]) > 0)){

                    $telefone = new Telefone();

                    $telefone->setIdUsuario($idUsuario);
                    $telefone->setIdPessoa($lastIdPessoa);
                    $telefone->setNumero($dados[$phones[$i]] ?? '');
                    $telefone->setDtRegistro(date('Y-m-d H:i:s'));
                    $telefone->setStatus('abilitado');

                    $errorsTelefone = $telefone->getErrors();

                    if(strlen($errorsTelefone) > 0){
                        throw new Exception($errorsTelefone);
                        
                    }



                    /**
                     * Salva as informaçoes no banco, caso esteja tudo ok
                     */
                    $resultTelefone = $telefone->save();

                    if($resultTelefone == false){
                        throw new Exception("Erro ao salvar dados do telefone");
                        
                    }
                    
                }
            }

            $sentinela = false;
            for ($i=0; !($i == count($dados['cidade']) ); $i++) { 

                $cep         = $dados['cep'][$i];
                $cidade         = $dados['cidade'][$i];
                $estado         = $dados['estado'][$i];
                $bairro         = $dados['bairro'][$i];
                $endereco       = $dados['endereco'][$i];
                $complemento    = $dados['complemento'][$i];
                $numero         = $dados['numero'][$i];
                $tipo           = $dados['tipo'][$i];

                /**
                * Dados da tabela de Logradouro
                */

                if(
                    (strlen($cidade) > 0) && (strlen($cep) > 0) && (strlen($estado) > 0) && (strlen($bairro) > 0) &&
                    (strlen($endereco) > 0) && (strlen($complemento) > 0) && (strlen($numero) > 0)
                    && (strlen($tipo) > 0) 
                ){

                    $logradouro = new Logradouro();

                    $logradouro->setCidade($cidade);
                    $logradouro->setCep($cep);
                    $logradouro->setEstado($estado);
                    $logradouro->setBairro($bairro);
                    $logradouro->setEndereco($endereco);
                    $logradouro->setComplemento($complemento);
                    $logradouro->setNumero($numero);
                    $logradouro->setTipo($tipo);
                    $logradouro->setIdUsuario($idUsuario);
                    $logradouro->setDtRegistro(date('Y-m-d H:i:s'));
                    $logradouro->setStatus('abilitado');

                    $errorsLogradouro = $logradouro->getErrors();

                    

                    /**
                     * Verifica se os dados informados pelo usuario sao válidos
                     */
                    
                    if(strlen($errorsLogradouro) > 0){

                        throw new Exception($errorsLogradouro);
                    }


                    /**
                     * Salva as informaçoes no banco, caso esteja tudo ok
                     */

                    $resultLogradouro = $logradouro->save();

                    if($resultLogradouro == false){
                        throw new Exception("Erro ao salvar dados do endereço");
                        
                    }


                    $lastIdLogradouro = $logradouro->maxId();


                    $logPessoa = new PessoaLogradouro();

                    $logPessoa->setIdPessoa($lastIdPessoa);
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

                    $sentinela = true;

                }
                
            }

            if($sentinela ==false){
                throw new Exception("Preencha o formulário corretamente");
                
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
            header('Location:/pessoa/index');


        } catch (\PDOException $e) {
            echo $e->getMessage();
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/pessoa/adicionar');

        }


    }


    public function painel($request)
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

            $this->render('pessoa/painel', true, 'layoutAdmin');

            Sessoes::clearMessage();

            Transaction::close();

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage().'-'.$e->getLine()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/');
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

            $idPessoa = (int) $request['get']['cd'];

            $pessoa = new Pessoa();
            $dados = $pessoa->findForId($idPessoa);
            if($dados == false){

                throw new Exception("Registro não encontrado\n");
                
            }

            
            $this->view->dados = $dados;

            $this->render('pessoa/editar', true, 'layoutAdmin');

            Sessoes::clearMessage();

            Transaction::close();

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage().'-'.$e->getLine()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/pessoa/painel?cd='.$idPessoa);
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

            $idUsuario = $curso = $usuario[0]->getIdUsuario();
            $dados = $request['post'];

            $pessoa = new Pessoa();
            $pessoaLoaded = $pessoa->findForId((int) $dados['pessoa'] ?? 0);

            if($pessoaLoaded == false){
                throw new Exception("Registro não encontrado\n");
                
            }
            $pessoaLoaded = $pessoaLoaded[0];
            
            $pessoaLoaded->setSobrenome($dados['sobrenome'] ?? '');
            $pessoaLoaded->setCpfCnpj($dados['cpf_cnpj'] ?? '');
            //$pessoaLoaded->setRg($dados['rg'] ?? '');
            //$pessoaLoaded->setImg($dados['post'] ?? '');
            //$pessoaLoaded->setDtNascimento($dados['nascimento'] ?? '');
            $pessoaLoaded->setNome($dados['nome'] ?? '');
            $pessoaLoaded->setSobrenome($dados['sobrenome'] ?? '');
            $pessoaLoaded->setSexo($dados['sexo'] ?? '');

            if(isset($dados['email']) && (strlen($dados['email']) > 0)){

                $pessoaLoaded->setEmail($dados['email'] ?? '');
            }
            //$pessoaLoaded->setRgIe();
            //$pessoaLoaded->setDtNascimento(date('Y-m-d H:i:s'));
            $pessoaLoaded->setStatus('abilitado');
            $pessoaLoaded->setIdUsuario($idUsuario);
            $pessoaLoaded->setDtRegistro(date('Y-m-d H:i:s'));

            $errorsPessoa = $pessoaLoaded->getErrors();

            if(strlen($errorsPessoa) > 0){
                throw new Exception($errorsPessoa);
                
            }


            /**
             * Salva as informaçoes no banco, caso esteja tudo ok
             */
            $resultPessoa = $pessoaLoaded->modify();

            if($resultPessoa == false){
                throw new Exception("Erro ao atualizar os dados da pessoa");
                
            }
            
             
            Transaction::close();

            /**
             * Limpa os dados do fromulario da sessao
             */
            Sessoes::clearForm();

            /**
             * Exibe uma mensagem de sucesso
             */
            Sessoes::sendMessage(['msg', 'success', 'Dados ataualizados com sucesso!']);

            /**
             * Redireciona o candidato para o proximo formulario
             */
            header('Location:/pessoa/painel?cd='.(int) $dados['pessoa']);

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/pessoa/editar?cd='.(int) $dados['pessoa']);
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

            $this->setMenu('_adm/adminMenu');
            $this->setFooter('_adm/adminFooter');
            
            $this->render('pessoa/adicionar', true);

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
            header('Location:/');
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

            $idPessoa = (int) $request['get']['cd'];

            $pessoa = new Pessoa();
            $pessoaLoaded = $pessoa->findForId($idPessoa)[0];

            $pessoaLoaded->setStatus('desabilitado');

            $resultPessoa = $pessoaLoaded->modify();

            if($resultPessoa == false){
                throw new Exception("Erro ao deletar registro");
                
            }

            $lograPess = $pessoaLoaded->pessoaLogradouro();

            if($lograPess != false){
                for ($i=0; !($i == count($lograPess) ); $i++) { 

                    $lograPess[$i]->setStatus('desabilitado');

                    $resultLogPess = $lograPess[$i]->modify();
                    if($resultLogPess == false){

                        throw new Exception("Erro ao deletar registro");
                        
                    }

                    $logradouro = $lograPess[$i]->logradouro()[0];

                    $logradouro->setStatus('desabilitado');

                    $resultLogradouro = $logradouro->modify();
                    if($resultLogradouro == false){

                        throw new Exception("Erro ao deletar registro");
                        
                    }
                }
            }

            Transaction::close();

            $error = ['msg', 'success','Registro deletado com sucesso!'];
            Sessoes::sendMessage($error);
            header('Location:/pessoa/index');

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){
            
            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];
            Sessoes::sendMessage($error);

            /**
             * Redireciona o candidato de volta ao formulario
             */
            header('Location:/pessoa/painel?cd='.(int) $idPessoa);
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

            $idPessoa = (int) $request['get']['cd'];

            $pessoa = new Pessoa();
            $dados = $pessoa->findForId($idPessoa);
            
            if($dados == false){

                throw new Exception("Registro não encontrado\n");
                
            }

            
            $this->view->dados = $dados;

            $this->render('pessoa/info', true, 'layoutAdmin');

            Transaction::close();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e){

            Transaction::rollback();

            $error = ['msg', 'warning','<strong>Atenção: </strong>'.$e->getMessage()];

            Sessoes::sendMessage($error);
        }

    }

    



}