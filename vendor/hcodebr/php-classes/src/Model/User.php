<?php


namespace Hcode\Model;


use Hcode\DB\Sql;
use Hcode\Model;


class User extends  Model
{
    const SESSION = "User";


    public static function listAll():array{
        $sql = new Sql();
        return $sql->select("SELECT * from tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    public function save(){
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
            array(
                ":desperson"=>$this->getdesperson(),
                ":deslogin"=>$this->getdeslogin(),
                ":despassword"=>$this->getdespassword(),
                ":desemail"=>$this->getdesemail(),
                ":nrphone"=>$this->getnrphone(),
                ":inadmin"=>$this->getinadmin()
            )
        );

        $this->setData($results[0]);
    }

    public function update(){
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
            array(
                ":iduser"       =>$this->getiduser(),
                ":desperson"    =>$this->getdesperson(),
                ":deslogin"     =>$this->getdeslogin(),
                ":despassword"  =>$this->getdespassword(),
                ":desemail"     =>$this->getdesemail(),
                ":nrphone"      =>$this->getnrphone(),
                ":inadmin"      =>$this->getinadmin()
            )
        );

        $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_users_delete(:iduser)",
            array( ":iduser" => $this->getiduser()));
    }

    public function get($iduser){
        $sql = new Sql();

        $result = $sql->select(
            "SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) WHERE a.iduser = :iduser",
            array(
                ":iduser"=>(int)$iduser
            )
        );

        $this->setData($result[0]);
    }

    /**
     * Verifica os dados de login e senha passados pelos parametros
     * @param $login
     *  valor do login
     * @param $password
     *  valor do password
     * @return User
     *  retorna os dados do usuario capturados no banco
     * @throws \Exception
     *  caso o usuário ou senha estejam errados
     */
    public static function login($login, $password)
    {
        // instancia a classe Sql;
        $sql = new Sql();
        // query de consulta no banco de dados: retorna um resultado caso o login seja encontrado
        $results = $sql->select(
            "SELECT * FROM tb_users WHERE deslogin = :LOGIN",
            array(":LOGIN" => $login)
        );

        // se não houver dados em $results, o usuário não foi encontrado no banco
        if (count($results) === 0){
            throw new \Exception("Usuario não encontrado");
        }

        // guarda o primeiro valor de $results em $data
        $data = $results[0];

        // verifica se a senha armazenada em $password bate com a senha (criptografada) armazenada no banco
        if (password_verify($password, $data["despassword"])){
            // cria um usuário
            $user = new User();

            // seta os dados do usuário armazenados em $data;
            $user->setData($data);

            // cria um parâmetro de sessão com os valores do usuário;
            $_SESSION[User::SESSION] = $user->getValues();

            //retorna o usuário;
            return $user;
        } else {
            throw new \Exception("Senha inválida");
        }

    }

    /**
     * Verifica se o usuário está ativo ou logado na sessão e se ele tem acesso à página, dependendo de seus privilégios
     * @param bool $inadmin
     *  true se a verificação exige usuário admin ou false, caso contrário.
     * @return bool
     *  true se o usuário estiver logado na sessão e se ele atender aos requisitos de login.
     */
    public static function verifyLogin($inadmin = true)
    {
        return ! (
            ! isset($_SESSION[User::SESSION]) ||                    // parâmetro da sessão não foi definida?
            ! $_SESSION[User::SESSION] ||                           // parâmetro da sessão não é vazia?
            ! (int)$_SESSION[User::SESSION]['iduser'] > 0 ||        // parâmetro iduser não é vazio?
            (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin  // usuário é usuário admin?
        );
    }

    public static function logout()
    {
        $_SESSION[User::SESSION] = null;
    }
}
