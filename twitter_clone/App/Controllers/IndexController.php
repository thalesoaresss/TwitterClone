<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}
	public function inscreverse(){
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => '',
		);
		$this->view->emailUser = false;
		$this->view->nameUser = false;
		$this->view->minSenha = true;
		$this->view->erroCadastro = false;
		$this->render('inscreverse');
	}
	public function registrar(){
		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));

		if($usuario->validarRegistro() && count($usuario->getUsuarioPorEmail()) == 0 && count($usuario->getUsuarioPorNome()) == 0){
			$usuario->salvar(); 
			$this->render('cadastro');
			
		}else {
			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha'],
			);
			$this->view->erroCadastro = true;
			if(count($usuario->getUsuarioPorEmail()) != 0){
				$this->view->emailUser = true;
			} else if(count($usuario->getUsuarioPorNome()) != 0){
				$this->view->nameUser = true;
			}
			$this->render('inscreverse');
		}
	}

}
?>