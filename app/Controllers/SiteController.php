<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Categoria;
use App\Models\Candidato;
use App\Models\Empresa;
use App\Models\Vaga;
use Core\Utilitarios\Sessoes;

class SiteController extends BaseController
{
    public function index()
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');

            Transaction::startTransaction('connection');

            $this->setMenu();
            $this->setFooter();

            $empresaStandard =
            [
                'empresa' => 'Emprsa standard', 'logo' => 'logo_standard.jpeg'
                
            ];

            $vagasStandard =
            [
                'cargo' => 'Cargo standard', 'empresa' => 'Empresa Standard',
                'publicacao' => '02/03/2020'
            ];

            $candidato = new Candidato();
            $result = $candidato->findForId(1);

            $empresa = new Empresa();
            $empresas = $empresa->select(['*'], [], null,0, 12, true, false);
            $totEmpresas = $empresa->countItens();

            $vaga = new Vaga();
            $vagas = $vaga->select(['*'], [], null,0, 4, true, false);
            $totVagas = $vaga->countItens();

            $data = new \DateTime();
            $data->sub(new \DateInterval('P2M'));

            $totNovas = $vaga->countItens(
                [
                    ['key'=>'dataCriacao', 'val' => $data->format('Y-m-d H:i:s'), 'comparator' => '>=']
                ]
            );




            $this->view->empresas = $empresas;
            $this->view->vagas = $vagas;
            $this->view->totVagas = $totVagas;
            $this->view->totEmpresas = $totEmpresas;
            $this->view->totNovas = $totNovas;

            $this->view->empresaStandard = $empresaStandard;
            $this->view->vagasStandard = $vagasStandard;
            $this->render('site/index', true);
            Transaction::close();

        } catch (\PDOException $e) {
            
            Transaction::rollback();

        }catch (Exception $e) {
            
            Transaction::rollback();
        }
        
    }
    
}
