<?php
use Luracast\Restler\RestException;
class MySQLDataProvider 
{
    private $db;
    function __construct()
    {
        try {
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
            // consider .env
            $this->db = new PDO(
                'mysql:host=ares.cjm1t15yslke.eu-central-1.rds.amazonaws.com;dbname=ares',
                'admin',
                'admin123',
                $options
            );
            //set fetch mode(cursor type)
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }
    function get($cin,$name){
        //PDO will report errors
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            // if cin exists select from cin else look for name
            $sql = $cin ? "SELECT * FROM companies WHERE cin = ${cin}" : "SELECT * FROM companies WHERE name LIKE '%{$name}%'";
            $result = $this->db->query($sql)->fetchAll();

            //return only companies that are not older than 1 month
            $result=array_filter($result, function($k,$v) {
                if (strtotime($k['edit_date'])  > strtotime('-30 days')){
                    return  $k ;
                }
            }, ARRAY_FILTER_USE_BOTH);

            //if there is nothing in database then get XML from ares website
            if(empty($result)){
                $fromAres= $cin ? "https://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?ico={$cin}" : "http://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?obch_jm={$name}";
                $xml = simplexml_load_file($fromAres);
                $array=[];
                //loop through xml childs
                foreach ($xml->children('are', TRUE)->children("dtt",TRUE)->V->S as $child){
                    $json = json_encode($child);
                    array_push($array ,json_decode($json,TRUE));
                }
                //create register of company in db
                foreach($array as $obj){
                   // $this->put($obj["ico"],$obj["ojm"], $obj["jmn"] );
                    $tmp['cin']=$obj["ico"];
                    $tmp['name']=$obj["ojm"];
                    $tmp['address']=$obj["jmn"];
                    array_push( $result ,$tmp );
                } ;
            };
            return $result;
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }
    function post($requestData){
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return($requestData);
        try {
            $sql = "INSERT INTO `companies` (`cin`, `name`, `address`) VALUES ('{$cin}', '{$name}', '{$address}')";
            $result = $this->db->query($sql)->fetch();

            return $result;
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }
}