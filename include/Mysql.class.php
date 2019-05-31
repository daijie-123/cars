<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：北京市普艾斯科技有限公司
 项目：simcms_锐车1.0
 电话：010-58480317
 Q  Q: 228971357
 网址：http://www.simcms.net
 simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/
class Mysql
{
    //mysql连接符
    public $conn_id = '';
    //sql运行结果
    public $query_result;
    //是否开启错误提示
    protected $db_error = false;
    //表前缀f
    public $tb_prefix = '';
    //操作数据库名
    public $db_name = '';

    //构造函数，连接数据库
    function __construct($db_config, $pconnect=0)
    {
        if(isset($db_config['DB_ERROR'])) $this->db_error = $db_config['DB_ERROR'];
        if(isset($db_config['TB_PREFIX'])) $this->tb_prefix = $db_config['TB_PREFIX'];
        $this->db_name = $db_config['DB_NAME'];
        
        $connect_type = $pconnect ? 'mysql_pconnect' : 'mysql_connect';
        $this->connect($db_config,$connect_type);
        $this->select_db($this->db_name);
        $this->db_charset($db_config["DB_CHARSET"]);
    }
    
    //连接服务器
    function connect($db_config, $connect_type='mysql_connect')
    {
        $this->conn_id = @$connect_type($db_config["DB_HOST"].':'.$db_config['DB_PORT'], $db_config["DB_USER"], $db_config["DB_PASS"]);
        if (!$this->conn_id) $this->halt("Access denied for user '{$db_config['DB_USER']}'@'{$db_config['DB_HOST']}' (using password: ".(empty($db_config['DB_PASS']) ? 'NO' : 'YES').")");
    }
    
    //选择数据库
    function select_db($db_name)
    {
        if (!@mysql_select_db($db_name, $this->conn_id)) $this->halt($this->get_error());
    }
    
    //设置数据库字符集
    function db_charset($db_charset)
    {
        @mysql_unbuffered_query("SET NAMES '$db_charset'");
    }

    //执行sql
    function query($sql,$query_type = 'mysql_query')
    {
        $this->query_result = @$query_type($sql,$this->conn_id);
        if (!$this->query_result) $this->halt($this->get_error($sql));
        return $this->query_result;
    }

    //无缓存执行sql
    function query_unbuffered($sql)
    {
        return $this->query($sql,'mysql_unbuffered_query');
    }

    //获得结果集中的一行（以数据库字段为下标）
    function fetch_assoc($query_result = '')
    {
        if ($query_result == '') $query_result = $this->query_result;
        return @mysql_fetch_assoc($query_result);
    }

    //取得影响的记录行数
    function affected_rows()
    {
        return @mysql_affected_rows($this->conn_id);
    }

    //取得结果集中的行数
    function num_rows($query_result = '')
    {
        if ($query_result == '') $query_result = $this->query_result;
        return @mysql_num_rows($query_result);
    }

    //获得错误
    function get_error($msg = '')
    {
        $error = '';
        $error .= '错误编码：'.@mysql_errno($this->conn_id)."\r\n";
        $error .= '错误信息：'.@mysql_error($this->conn_id)."\r\n";
        $error .= $msg ? '错误语句：'.$msg : '';
        return $error;
    }

    //上次插入的ID
    function insert_id()
    {
        return @mysql_insert_id($this->conn_id);
    }
    
    //下次要插入的ID
    function next_id($tbname)
    {
        $sql = "show table status where name='{$this->tb_prefix}$tbname'";
        $rs = $this->row_query_one($sql);
        return $rs['Auto_increment'];
    }

    //释放缓存
    function free_result($query_result = '')
    {
        if ($query_result == '') $query_result = $this->query_result;
        @mysql_free_result($query_result);
    }

    //关闭连接
    function close_db()
    {
        return @mysql_close($this->conn_id);
    }
    
    //获得mysql版本
    function get_server_info()
    {
        $rs= @mysql_query("SELECT VERSION();",$this->conn_id);
		 $row = @mysql_fetch_array($rs);
        $mysql_version = $row[0];
        @mysql_free_result($rs);
        $mysql_versions = explode(".",trim($mysql_version));
        $mysql_version = number_format($mysql_versions[0].".".$mysql_versions[1],2);
        return $mysql_version;
    }

    //获得数据库中所有表名
    function show_tables($database = '')
    {
        if ($database == '') $database = $this->db_name;
        $rs = $this->row_query("SHOW TABLES IN $database");
        $tables = array();
        foreach ($rs as $v)
        {
            $tables[] = $v["Tables_in_$database"];
        }
        return $tables;
    }
    
    //获取表字段信息
    function desc_table($tbname, $only_filed = 0)
    {
        $rs = $this->row_query("DESC {$this->tb_prefix}{$tbname}");
        if(!$only_filed) return $rs; 
        $fileds = array();
        foreach ($rs as $v)
        {
            $fileds[] = $v['Field'];
        }
        return $fileds;
    }
    
    //操作表(优化->OPTIMIZE,修复->REPAIR,检查->CHECK,分析->ANALYZE,强制更新->FLUSH)
    function operation_table($tbname, $do='')
    {
        if (is_array($tbname))
        {
            $str_tbname = '';
            foreach ($tbname as $v)
            {
                $str_tbname .= '`'.$v.'`,';
            }
            $str_tbname = substr($str_tbname,0,-1);
        }
        else $str_tbname = '`'.$tbname.'`';
        return $this->query("$do TABLE $str_tbname");
    }

    //输出错误信息
    function halt($message = '')
    {
        if ($this->db_error)
        {
            $message = "MySQL错语信息\r\n".$message;
            echo "<html><head><title>MySQL 错误</title>";
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<style type=\"text/css\"><!--.error { font: 11px tahoma, verdana, arial, sans-serif, simsun; }--></style></head>\r\n";
            echo "<body>\r\n";
            echo "<blockquote>\r\n";
            echo "<textarea class=\"error\" rows=\"15\" cols=\"80\" wrap=\"on\" >" . htmlspecialchars($message) . "</textarea>\r\n";
            echo "</blockquote>\r\n</body></html>";
        }
        exit;
    }

    function __destruct()
    {
        $this->close_db();
    }

    //查询sql
    function sql_select($tbname, $where='', $fields='*', $limit=0, $order_by='', $group_by='')
    {
        $sql = "SELECT $fields FROM `".$this->tb_prefix.$tbname.'`'.($where ? ' WHERE '.$where : '').($group_by ? " GROUP BY $group_by" : '').($order_by ? " ORDER BY $order_by" : '').($limit ? ' LIMIT '.$limit : '');
        return $sql;
    }

    //插入一条数据sql
    function sql_insert($tbname, $row, $replace=0)
    {
        $sql_field = $sql_value = '';
        foreach ($row as $key=>$value)
        {
            $sql_field .= '`'.$key.'`,';
            $sql_value .= "'".$value."',";
        }
        return ($replace ? 'REPLACE' : 'INSERT').' INTO `'.$this->tb_prefix.$tbname.'`('.substr($sql_field, 0, -1).') VALUES ('.substr($sql_value, 0, -1).')';
    }
    
    //插入一组数据sql
    function sql_arr_insert($tbname, $arr, $replace=0)
    {
        $sql_field = $sql_value = '';
        $arr_value = array();
        foreach ($arr as $key=>$val)
        {
            $sql_field .= '`'.$key.'`,';
            $num  = count($val);
            for ($i = 0; $i < $num; $i++)
            {
                $arr_value[$i][] = $val[$i];
            }
        }
        foreach ($arr_value as $arr)
        {
            $str_value = '';
            foreach ($arr as $v)
            {
                $str_value .= "'".$v."',";
            }
            $sql_value .= '('.substr($str_value,0,-1).'),';
        }
        $sql = ($replace ? 'REPLACE' : 'INSERT').' INTO `'.$this->tb_prefix.$tbname.'`('.substr($sql_field, 0, -1).') VALUES '.substr($sql_value, 0, -1);
        return $sql;
    }

    //更新数据sql
    function sql_update($tbname, $row, $where)
    {
        $sqlud = '';
        foreach ($row as $key=>$value)
        {
            $sqlud .= '`'.$key."`='".$value."',";
        }
        return "UPDATE `".$this->tb_prefix.$tbname."` SET ".substr($sqlud, 0, -1)." WHERE ".$where;
    }

    //删除数据sql
    function sql_delete($tbname, $where)
    {
        return 'DELETE FROM `'.$this->tb_prefix.$tbname.'` WHERE '.$where;
    }

    //插入一行
    function row_insert($tbname, $row, $replace=0)
    {
        $sql = $this->sql_insert($tbname, $row, $replace);
        return $this->query_unbuffered($sql);
    }

    //插入多行
    function arr_insert($tbname,$row,$replace=0)
    {
        $sql = $this->sql_arr_insert($tbname,$row,$replace);
        return $this->query_unbuffered($sql);
    }

    //更新记录
    function row_update($tbname, $row, $where)
    {
        $sql = $this->sql_update($tbname, $row, $where);
        return $this->query_unbuffered($sql);
    }

    //删除记录
    function row_delete($tbname, $where)
    {
        $sql = $this->sql_delete($tbname, $where);
        return $this->query_unbuffered($sql);
    }

    //查找
    function row_select($tbname, $where='', $fields='*', $limit=0, $order_by='', $group_by='')
    {
        $sql = $this->sql_select($tbname, $where, $fields, $limit, $order_by, $group_by);       
        return $this->row_query($sql);
    }

    //查找一行
    function row_select_one($tbname, $where='', $fields='*', $order_by='', $group_by='')
    {
        $sql = $this->sql_select($tbname, $where, $fields, 1, $order_by, $group_by);
        return $this->row_query_one($sql);
    }

    //执行sql并取出所有结果
    function row_query($sql)
    {
        $rs = $this->query($sql);
        $rows = array();
        $rs_num = $this->num_rows($rs);
        for($i=0; $i<$rs_num; $i++)
        {
            $rows[] = $this->fetch_assoc($rs);
        }
        $this->free_result($rs);
        return $rows;
    }

    //执行sql并只取一行结果
    function row_query_one($sql)
    {
        $rs = $this->query($sql);
        $row = $this->fetch_assoc($rs);
        $this->free_result($rs);
        return $row;
    }

    //查看符合条件的记录数
    function row_count($tbname,$where='')
    {
        $sql = 'SELECT count(*) as row_num FROM `'.$this->tb_prefix.$tbname.'` '.($where ? ' WHERE '.$where : '');
        $row = $this->row_query_one($sql);
        return $row['row_num'];
    }
  
	   //获得错误描述
    function GetError()
    {
        $str = mysql_error();
        return $str;
    }
	//显示表结构
   function viewinfo_table($tbname)
   {
	   
	  $sql= 'SHOW CREATE TABLE `'.$tbname.'`';
	  $rs= mysql_query($sql) OR die("SQL Error!");
	  return $rs;
   }
   //查询表字段个数
   function fields_count($tbname)
   {
	   $sql = "SELECT * FROM ".$tbname;
       $result = mysql_query($sql);
	   $filedscount=mysql_num_fields($result);
	  return $filedscount;
   }
   
}
?>