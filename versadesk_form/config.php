<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Database{
    private $host;
    private $user;
    private $pass;
    private $db;
    public $mysqli;
  
    public function __construct() {
      $this->db_connect();
      mysqli_select_db($this->db_connect(),"");
    }
  
    private function db_connect(){
      $this->host = 'localhost';
      $this->user = 'root';
      $this->pass = 'versaroot';
      $this->db = 'custom_versa_product';
  
      $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);
      return $this->mysqli;
    }
  
    
    public function get_rows($fields, $id = NULL, $tablename = NULL)  
    {  
        $cn = !empty($id) ? " WHERE $id " : " ";  
        $fields = !empty($fields) ? $fields : " * ";  
        $sql = "SELECT $fields FROM $tablename $cn";  

        $results = $this -> query_executed($sql);  

        $rows = $this -> get_fetch_data($results);  
        return $rows;  
    } 
    public function query_executed($sql)  
        {  
          // $ddd = "SELECT  *  FROM crm_products  WHERE id=5";
            $c = mysqli_query($this->db_connect(),$sql);  
            //echo $sql;
            return $c;  
        } 
    public function get_fetch_data($r)  
        {  
            $array = array();  
            $rows = mysqli_num_rows($r); 
            while ($rows = mysqli_fetch_array($r,MYSQLI_ASSOC))  
            {  
                $array[] = $rows;  
            }  
            return $array;  
        }   
  }
  
  
    $obj = new Database();  
    $recresults = $obj-> get_rows (implode(",",array("id","Product_Name","Product_ID")), '' , "crm_products") ;
    $maparray = array();
    foreach($recresults as $rec)
    {
        $maparray[$rec['id']] = $rec['Product_Name'];
        
    }
   
    
    
  
?>
