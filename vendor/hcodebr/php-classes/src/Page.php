<?php

namespace Hcode;

use Rain\Tpl;

class Page 
{

	// classe para organizar o template
	private $tpl;
	// dados para serem transportados pelo tpl
	private $options = [];
	// dados padrao, caso nao seja iniciado nenhum option;
	private $defaults = [
		"data"=>[]
	];

	// classe construtora.
	public function __construct($opts = array())
	{

		// faz um merge com os parametros default e opts, armazenando em options.
		$this->options = array_merge($this->defaults, $opts);

		// configuração do tpl
		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"] . "/views/",			// diretorio das views
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",		// diretorio das views cache
			"debug"         => false // set to false to improve the speed
	   );
		// configura o tpl com os dados setados em $config
		Tpl::configure( $config );
		// instancia o tpl
		$this->tpl = new Tpl();
		// associa os dados de options na tpl.
		$this->assignData($this->options["data"]);
		// desenha o header do html
		$this->tpl->draw("header");
	
	}
	

	// desenha, apos o header o body da pagina.
	public function setTpl($name,$data = array(), $returnHTML = false)
	{
		$this->assignData($data);
		return $this->tpl->draw($name,$returnHTML);
	}

	// função auxiliar para associar um dado no tpl;
	private function assignData($data = array()){
			foreach ($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}

	}

	// metodo destrutor, desenhando o footer no html ao fim;
	public function __destruct()
	{
		$this->tpl->draw("footer");
	}

}