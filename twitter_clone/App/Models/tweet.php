<?php
    namespace App\Models;
    use MF\Model\Model;

    class Tweet extends Model{
        private $id;
        private $idUsuairo;
        private $tweet;
        private $data;

        public function __get($attr){
            return $this->$attr;
        }
        public function __set($attr, $value){
            $this->$attr = $value;
        }

        public function salvar(){
            $query = 'INSERT INTO tweets(id_usuario, tweet) VALUES (:idUsuairo, :tweet)';
            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':idUsuairo', $this->__get('idUsuairo'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));
            $stmt->execute();

            return $this;
        }
        public function getAll(){
            $query = '  SELECT t.id, t.id_usuario, t.tweet, DATE_FORMAT(t.data, "%d/%m/%Y %H:%i") AS data, u.nome
                        FROM tweets AS t
                        LEFT JOIN usuarios AS u on (t.id_usuario = u.id)
                        WHERE t.id_usuario = :idUsuario OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :idUsuario)
                        ORDER BY t.data DESC';
            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':idUsuario', $this->__get('idUsuario'));

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        public function removerTweet($idTweet){
            $query = 'DELETE FROM tweets WHERE id = :idTweet';
            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':idTweet', $idTweet);
            $stmt->execute();

            return $this;
        }


    }
?>