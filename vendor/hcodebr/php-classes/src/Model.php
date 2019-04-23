<?php


namespace Hcode;

abstract class Model
{
    // todas as subclasses de Model armazenarão seus valores (chave = campo do banco de dados, valor = valor do campo;
    private $values = [];

    /**
     * @param $name
     *  nome do método a ser chamado
     * @param $args
     *  argumentos do método a ser chamado
     * @return mixed
     *  caso o método seja do tipo get, ele irá retornar o valor do campo correspondente. Caso seja do tipo set, ele irá
     * inserir a chave e o valor do campo em $values
     */
    public function __call($name, $args)
    {
        // armazena as 3 primeiras letras do método
        $method = substr($name,0,3);
        // armazena o nome do campo do método chamado
        $fieldName = substr($name,3,strlen($name));
        switch ($method){
            case "get":
                // simula um método get, retornando o valor do seu campo
                return $this->values[$fieldName];

            case "set":
                // simula um método set, armazenando o valor da sua chave e campo;
                $this->values[$fieldName] = $args[0];
        }
    }

    /**
     * Simula o método set para todos os campos do banco;
     * @param array $data
     *  Os dados retornados após a consulta realizada no banco.
     */
    public function setData($data = array()){
        foreach ($data as $key => $value) {
            $this->{"set" . $key}($value);
        }
    }

    /**
     * @return array
     *  Os valores armazenados em $values
     */
    public function getValues():array {
        return $this->values;
    }
}
