<?php

    include_once '../lib/conf/connection.php';
    class MasterModel extends Connection{
        public function insert($sql){
            $result = pg_query($this->getConnection(),$sql);

            return $result;
        }
        public function select($sql){
            $result = pg_query($this->getConnection(),$sql);

            return $result;
        }
        public function update($sql){
            $result = pg_query($this->getConnection(),$sql);

            return $result;
        }
        public function delete($sql){
            $result = pg_query($this->getConnection(),$sql);

            return $result;
        }
        public function findOne($table,$fields,$condition){
            $sql = "SELECT $fields FROM $table WHERE $condition";
            $result = pg_query($this->getConnection(),$sql);
            if(pg_num_rows($result)>0){
                return $result;
            }else{
                return "No se encontro ningun registro";
            }
        }
        public function autoincrement($table,$field){
            $sql= "SELECT MAX($field) FROM $table";
            $result = pg_query($this->getConnection(),$sql);
            $max_id = pg_fetch_array($result);
            return $max_id[0]+1;
        }
    }



?>