<?php
/**
 * Created by IntelliJ IDEA.
 * User: ack7
 * Date: 2020-07-19
 * Time: 14:32
 */
namespace Clef;

use Clef\Pdo7 as Pdo;

class Logger
{
    public static function action_log_write($link,$table, $mode=null, $menu=null , $target_key=null, $title=null , $data=null,$result='success') {

        if($link===null){
            $db = new Pdo();
            $link = $db->link;

        }

        $params = array(
        'act_ip'            =>  null
        ,'act_session_id'   =>  null
        ,'act_mode'         =>  null
        ,'act_menu'         =>  null
        ,'act_staff'        =>  null
        ,'act_target_key'   =>  null
        ,'act_title'        =>  null
        ,'act_data'         =>  null
        ,'act_result'       =>  null


        );
        $params['act_ip']           = $_SERVER['REMOTE_ADDR'];
        $params['act_session_id']   = @session_id();
        $params['act_menu']         = $menu;
        $params['act_mode']         = $mode;
        $params['act_target_key']   = $target_key;
        $params['act_staff']        = $_SESSION['seq'];
        $params['act_title']        = $title;
        $params['act_data']         = $data;
        $params['act_result']       = $result;


        $sql = '';
        $cols_str = ' (';
        $vals_str = ' VALUES (';
        foreach ($params as $col=>$param) {
            $cols_str .= "{$col},";
            $vals_str .= ":{$col},";
        };
        //$cols_str = substr($cols_str, 0, -1);
        //$vals_str = substr($vals_str, 0, -1);
        $cols_str .= ' registered_dt)';
        $vals_str .= ' now() )';
        $sql = "INSERT INTO {$table} {$cols_str}{$vals_str}";
       // var_dump($sql);
        $stmt = $link->prepare($sql);
        $result = null;
        try {

            $bind_type = '';
            foreach ($params as $col=>$param) {
                if(is_array($param)) {
                    $param = implode("||",$param);
                }
                if($param == '') {
                    $param = null;
                    $bind_type = \PDO::PARAM_NULL;
                } else {
                    if(is_int($param))        { $bind_type = \PDO::PARAM_INT; }
                    elseif(is_bool($param))   { $bind_type = \PDO::PARAM_BOOL; }
                    elseif($param===null)   { $bind_type = \PDO::PARAM_NULL; }
                    elseif(is_string($param)) { $bind_type = \PDO::PARAM_STR; }
                    else { $bind_type = FALSE;}
                }
                // var_dump($param .'/'. $bind_type);
                if(!$stmt->bindValue(":{$col}", $param,$bind_type)){
                    //echo "mapping_error";
                    throw new BindException();
                }

            }
            $return = '';
            $result = $stmt->execute();



        } catch (\PDOException $exception) {
            //echo $exception->getMessage();

        }  catch (\Exception $exception) {
          return ;

        }
        finally {

            $stmt = null;

        }

    }

    public static function write($table, $page_type) :bool
    {

        $db = new Pdo();
        $link = $db->link;

        $params = array(
        'log_ip'           =>  null
        , 'PAGE_TYPE' => null
        , 'log_referer'     =>  null
        , 'log_url'          =>  null
        , 'log_session_id'   =>  null
        , 'log_browser'      =>  null
        , 'log_keyword'      =>  null
        , 'log_year' => date('Y')
        , 'log_day' => date('d')
        , 'log_month' => date('m')
        , 'log_hour' => date('h')
        , 'log_unixtime' => time()
        , 'log_date' => date('Y-m-d')
        );

        $ipAddress = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        $userAgent = "";

        if (isset($_SERVER['HTTP_X_CUSTOM_USER_AGENT'])) {
            // 사용자 정의 User-Agent 값
            $userAgent = $_SERVER['HTTP_X_CUSTOM_USER_AGENT'];
        } else if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $userAgent = '';
        }

        $params['log_ip'] = $ipAddress;
        $params['PAGE_TYPE'] = $page_type;
        $params['log_referrer'] = $_SERVER['HTTP_REFERER'] ?? null;
        $params['log_url'] = $_SERVER['PHP_SELF'];
        $params['log_session_id'] = @session_id();
        $params['log_browser'] = $userAgent;
        $params['log_keyword'] =  $_SERVER['QUERY_STRING'];

        $sql = '';

        $cols_str = ' (';
        $vals_str = ' VALUES (';
        foreach ($params as $col=>$param) {
            $cols_str .= "{$col},";
            $vals_str .= ":{$col},";
        }
        $cols_str = substr($cols_str, 0, -1);
        $vals_str = substr($vals_str, 0, -1);
        $cols_str .= ')';
        $vals_str .= ')';
        $sql = "INSERT INTO {$table}{$cols_str}{$vals_str}";
        $stmt = $link->prepare($sql);
        $result = null;
        try {

            $bind_type = '';
            foreach ($params as $col=>$param) {
                if(is_array($param)) {
                    $param = implode("||",$param);
                }
                if($param === '') {
                    $param = null;
                    $bind_type = \PDO::PARAM_NULL;
                } else if(is_int($param)) {
                    $bind_type = \PDO::PARAM_INT;
                } elseif(is_bool($param))   {
                    $bind_type = \PDO::PARAM_BOOL;
                } elseif(is_null($param))   {
                    $bind_type = \PDO::PARAM_NULL;
                } elseif(is_string($param)) {
                    $bind_type = \PDO::PARAM_STR;
                } else {
                    $bind_type = FALSE;
                }

                if(!$stmt->bindValue(":{$col}", $param,$bind_type)){
                    throw new BindException();
                }

            }
            $return = '';
            $result = $stmt->execute();



        } catch (\PDOException $exception) {
            //echo $exception->getMessage();

        }  catch (\Exception $exception) {
            return true;

        }
        finally {
            $stmt = null;
        }
        return true;
    }


}
