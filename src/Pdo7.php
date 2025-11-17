<?php
/**
 * Created by IntelliJ IDEA.
 * User: ack7
 * Date: 2019-10-30
 * Time: 12:59
 */

namespace Clef;

use clef\BindException;
use PDO;
use PDOException;
use Clef\ClefResult as ClefResult;

class Pdo7
{
    private $user = 'piknic2023';
    private $pw = '';
    private $db = 'piknic2023';
    private $host = '127.0.0.1';
    private $port = '';
    public $link = null;
    private $error = null;

    public function __construct() {
        try {
            if ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1' || $_SERVER['SERVER_ADDR'] === '192.168.0.5') {
                $this->port = 'port=3307;';
            }

            // MySQL PDO 객체 생성
            // mysql을 다른 DB로 변경하면 다른 DB도 사용 가능
            $this->link = new PDO("mysql:host={$this->host};dbname={$this->db};{$this->port}", $this->user, $this->pw, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

            // 에러 출력
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(\PDOException $e) {
            echo $e->getMessage();

            header($_SERVER['SERVER_PROTOCOL']. '디비에러', true, 500);
        }
    }

    public function connect():bool {
        if ($this->link===null) {
            try {
                // MySQL PDO 객체 생성
                // mysql을 다른 DB로 변경하면 다른 DB도 사용 가능
                $this->link = new PDO("mysql:host={$this->host};dbname={$this->db};{$this->port}", $this->user, $this->pw, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

                // 에러 출력
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return true;
            } catch(\PDOException $e) {
                return false;
            }
        } else {
            return true;
        }
    }

    public function __destruct() {
        $this->link = null;
    }

    /**
     * name : count
     * comment : 결과 세트에서 리턴될 때 컬럼 0부터 시작하여 컬럼 번호로 인덱스가 지정된 배열을 리턴 : FETCH_NUM
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function count($sql, $params=null, $name_sql=null): ClefResult {
        return $this->query($sql, $params, 'COUNT',$name_sql);
    }

    /**
     * name : select
     * comment : 열 이름으로 인덱싱된 배열을 지정 : FETCH_ASSOC
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function select($sql, $params=null, $name_sql=null): ClefResult {
        return $this->query($sql, $params,'SELECT', $name_sql);
    }

    /**
     * name : get
     * comment : 열 이름으로 인덱싱된 배열을 지정 : FETCH_ASSOC
     *         , 해당기능은 하나의 행만 가져옴
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function get($sql, $params=null, $name_sql=null): ClefResult {
        return $this->query($sql, $params, 'ONE', $name_sql);
    }

    /**
     * name : is_exist
     * comment : 열 이름으로 인덱싱된 배열을 지정 : FETCH_ASSOC
     *         , 해당기능은 쿼리가 존재하는지 확인 후 쿼리값을 반환 없으면 값을 반환하지 않음
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function is_exist($sql, $params=null, $name_sql=null): ClefResult {
        return $this->query($sql, $params,'EXIST', $name_sql);
    }

    /**
     * name : total
     * comment : 결과 세트에서 리턴될 때 컬럼 0부터 시작하여 컬럼 번호로 인덱스가 지정된 배열을 리턴 : FETCH_NUM
     *         , 해당기능 하나의 행만 가져옴
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function total($sql, $name_sql=null): ClefResult {
        return $this->query($sql, null, 'TOTAL', $name_sql);
    }

    /**
     * name : procedure
     * comment : 열 이름으로 인덱싱된 배열을 지정 : FETCH_ASSOC
     *         , 해당기능은 프로시저를 실행시킴
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function procedure($sql, $params=null, $name_sql=null): ClefResult {
        return $this->query($sql, null, 'PROCEDURE', $name_sql);
    }

    /**
     * name : query
     * comment : 실제 쿼리가 실행되는 부분
     *           $sql : 쿼리 내용 ex) $sql = "SELECT * FROM TEST WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $optional : 조건에 해당하는 'COUNT'/ 'SELECT'/ 'ONE'/ 'EXIST'/ 'TOTAL'등 조건을 확인하는 값
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function query($sql, $params=null, $optional='SELECT', $name_sql=null): ClefResult {
        $clefResult = new ClefResult();
        $st = null;
        $result_set = null;

        try {
            $st = $this->link->prepare($sql);
            if(!$params||$params===''||is_null($params)){
                $result = $st->execute();
            } else {
                $result = $st->execute($params);
             //   var_dump($params);
            }
            if($result) {
                if($optional==='COUNT') {
                    $result_set = $st->fetchAll(PDO::FETCH_NUM);
                    $clefResult->setCount(count($result_set));
                    $clefResult->setResultSet($result_set);
                } else if($optional==='SELECT') {
                    $result_set = $st->fetchAll(PDO::FETCH_ASSOC);
                    $clefResult->setCount(count($result_set));
                    $clefResult->setResultSet($result_set);
                } else if($optional==='ONE') {
                    $result_set = $st->fetch(PDO::FETCH_ASSOC);
                    $clefResult->setCount(1);
                    $clefResult->setResultSet($result_set);
                } else if($optional==='EXIST') {
                    $result_set = $st->fetchAll(PDO::FETCH_ASSOC);
                    if($result_set===false){
                        $clefResult->setCount(0);
                    } else {
                        $clefResult->setCount(count($result_set));
                    }
                    $clefResult->setResultSet($result_set);
                } else if($optional==='TOTAL') {
                    $result_set = $st->fetch(PDO::FETCH_NUM);
                    $clefResult->setCount(1);
                    $clefResult->setResultSet($result_set);
                    $clefResult->setTotal($result_set[0]);
                } else if ($optional === 'PROCEDURE') {
                    $clefResult->setCount(1);  // 프로시저가 성공적으로 실행되었으므로 count를 1로 설정
                    $clefResult->setResultSet(null);  // 결과 셋은 없으므로 null로 설정
                } else {
                    $result_set = $st->fetchAll(PDO::FETCH_ASSOC);
                    $clefResult->setCount(count($result_set));
                    $clefResult->setResultSet($result_set);
                }
                
                gfn_query_log($name_sql, $sql, $params);
            } else {
                $clefResult->setCount(0);
                $clefResult->setResultSet(null);
            }

            $clefResult->setResult($result);
        } catch (PDOException $e) {
            $clefResult->setCount(0);
            $clefResult->setErrCode($e->getCode());
            $clefResult->setErrMsg($e->getMessage());

            gfn_sql_log(print_r($clefResult, true));
        } finally {
            return $clefResult;
        }
    }

    /**
     * name : update
     * comment : 데이터 수정
     *           $table : 쿼리에 실행될 필요한 테이블이름
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $pkParam : WHERE절에 해당하는 해당 테이블의 PK값
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function update($table,$params,$pkParam,$name_sql=null): ClefResult {
        $sql = "UPDATE {$table} SET ";
        $str = '';
        $i = 0;
        $values = array();

        foreach ($params as $key => $val) {
            if ($i > 0) {
                $str .= ",";
            }

            $str .= "{$key} = :{$key}";
            $i++;
            $values[$key] = $val;
        }

        $i = 0;
        $pk_str = ' WHERE ';
      
        foreach ($pkParam as $key => $val) {
            if ($i > 0) {
                $pk_str .= " AND ";
            }

            $pk_str .= "{$key} = :{$key}";
            $i++;
            $values[$key] = $val;
        }

        $sql = $sql. $str. $pk_str;
       
        $clefResult = new ClefResult();

        try {
            $st = $this->link->prepare($sql);
            $result = $st->execute($values);

            if ($result) {
                $clefResult->setCount($st->rowCount());
                $clefResult->setLastId($this->link->lastInsertId());
            } else {
                $clefResult->setCount(0);
            }

            $clefResult->setResult($result);

            gfn_query_log2($name_sql, $sql, $params, $pkParam);
        } catch (PDOException $e) {
            $clefResult->setCount(0);
            $clefResult->setErrCode($e->getCode());
            $clefResult->setErrMsg($e->getMessage());

            gfn_sql_log($clefResult);
        }

        return $clefResult;
    }

    /**
     * name : delete
     * comment : 데이터를 삭제
     *           $sql : 쿼리 내용 ex) $sql = "DELETE FROM TABLE WHERE ID = :ID"
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function delete($sql,$params,$name_sql=null): ClefResult {
        $clefResult = new ClefResult();

        try {
            $st = $this->link->prepare($sql);
            $result = $st->execute($params);

            if ($result) {
                $clefResult->setCount($st->rowCount());

                if (count($params) === 1) {
                    $clefResult->setLastId($params[':pk']);
                }
            } else {
                $clefResult->setCount(0);

                if (count($params) === 1) {
                    $clefResult->setLastId($params[':pk']);
                }
            }

            $clefResult->setResult($result);

            gfn_query_log($name_sql, $sql, $params);
        } catch (PDOException $e) {
            $clefResult->setCount(0);

            if (count($params) === 1) {
                $clefResult->setLastId($params[':pk']);
            }

            $clefResult->setErrCode($e->getCode());
            $clefResult->setErrMsg($e->getMessage());

            gfn_sql_log(print_r($clefResult, true));
        }

        return $clefResult;
    }

    /**
     * name : insert
     * comment : 데이터 추가
     *           $table : 쿼리에 실행될 필요한 테이블이름
     *           $params : 해당 쿼리의 파라미터 값 ex) $arrValue[':ID'] = $ID;
     *           $name_sql : 해당 쿼리의 이름 ex) "테스트 쿼리" // 로그에 필요
     */
    public function insert($table,$params,$name_sql=null): ClefResult {
        $sql = "INSERT {$table} SET ";
        $str = '';
        $i = 0;
        $values = array();

        foreach ($params as $key => $val) {
            if ($i > 0) {
                $str .= ",";
            }

            $str .= "{$key} = :{$key}";
            $i++;
            $values[$key] = $val;
        }

        $sql = $sql. $str;
      
        $clefResult = new ClefResult();

        try {
            $st = $this->link->prepare($sql);
            $result = $st->execute($values);

            if ($result) {
                $clefResult->setCount($st->rowCount());
                $clefResult->setLastId($this->link->lastInsertId());
            } else {
                $clefResult->setCount(0);
                $clefResult->setLastId('null');
            }

            $clefResult->setResult($result);

            gfn_query_log2($name_sql, $sql, $params);
        } catch (PDOException $e) {
            $clefResult->setCount(0);
            $clefResult->setErrCode($e->getCode());
            $clefResult->setErrMsg($e->getMessage());

            gfn_sql_log(print_r($clefResult, true));
        }

        return $clefResult;
    }

    /**
     * name : getPdoErrorMessage
     * comment : 디비에러시 사용됨
     *           $e : 디비에러내역 
     */
    public function getPdoErrorMessage(PDOException $e):string {
        $msg = '디비처리중 에러가 발생했습니다';
        
        if ($e->getCode() === '2A000') {
            $msg = '디비문법에러';
        }
        
        return $msg;
    }
}
