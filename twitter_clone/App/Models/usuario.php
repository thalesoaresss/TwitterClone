<?php
namespace App\Models;
use MF\Model\Model;

class Usuario extends Model {
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($attr){
        return $this->$attr;
    }
    public function __set($attr, $value){
        $this->$attr = $value;
    }
    //salvar 
    public function salvar(){
        $query = "INSERT INTO usuarios(nome, email, senha)VALUE(:nome, :email, :senha)";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
    }

    public function validarRegistro(){
        $valido = true;
        
        if(strlen($this->__get('nome'))<3){
            $valido = false;
        }
        if(strlen($this->__get('email'))<3){
            $valido = false;
        }
        if(strlen($this->__get('senha'))<8){
            $valido = false;
        }
        return $valido;
    }

    public function getUsuarioPorEmail(){
        $query = 'SELECT nome, email FROM usuarios WHERE email=:email';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getUsuarioPorNome(){
        $query = 'SELECT nome, email FROM usuarios WHERE nome=:nome';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function autenticar(){
        $query = 'SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($usuario['id'] != '' && $usuario['nome'] != ''){
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']); 
        }
        return $this;
    }
    public function getAll(){
        $query = "  SELECT u.id, u.nome, u.email, (select count(*) from usuarios_seguidores as us where us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id) AS seguindo_sn
                    FROM usuarios AS u
                    WHERE u.nome LIKE :nome AND u.id != :id_usuario";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':nome', "%".$this->__get('nome')."%");
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario($id_usuario_seguindo){
        $query = "INSERT INTO usuarios_seguidores (id_usuario, id_usuario_seguindo) VALUES (:id_usuario, :id_usuario_seguindo)";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return true;
    }
    public function deixarSeguirUsuario($id_usuario_seguindo){
        $query = "  DELETE FROM usuarios_seguidores 
                    WHERE id_usuario = :id_usuario AND id_usuario_seguindo= :id_usuario_seguindo";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return true;
    }
    public function getInfoUsuario(){
        $query = 'SELECT nome FROM usuarios
                  WHERE id = :id_usuario';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getTotalTweets(){
        $query = 'SELECT COUNT(*) AS totalTweets FROM tweets
                  WHERE id_usuario = :id_usuario';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getTotalSeguindo(){
        $query = 'SELECT COUNT(*) AS totalSeguindo FROM usuarios_seguidores
                  WHERE id_usuario = :id_usuario';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getTotalSeguidores(){
        $query = 'SELECT COUNT(*) AS totalSeguidores FROM usuarios_seguidores
                  WHERE id_usuario_seguindo = :id_usuario';
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

?>