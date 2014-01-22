<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010-2013  CoreManager Project
    Copyright (C) 2009-2010  ArcManager Project

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if ( !function_exists("mysql_connect") )
  die("This PHP environment doesn't have MySQL support built in.");

class SQL //MySQL
{
  var $link_id;
  var $query_result;
  var $num_queries = 0;

  function connect($db_host, $db_username, $db_password, $db_name = "", $use_names = "", $pconnect = false, $newlink = true)
  {
    global $lang_global;

    if ( $pconnect )
      $this->link_id = @mysql_pconnect($db_host, $db_username, $db_password);
    else
      $this->link_id = @mysql_connect($db_host, $db_username, $db_password, $newlink);

    if ( $this->link_id )
    {
      if ( $db_name )
      {
        if ( !empty($use_names) )
          $this->query("SET NAMES '".$use_names."'");
        if ( @mysql_select_db($db_name, $this->link_id) )
          return $this->link_id;
        else
          die(error($db_name."\r\n".mysql_error()."\r\n".$lang_global['err_sql_open_db']." ('".$db_name."')"));
      }
    }
    else
      die($db_name."\r\n".mysql_error()."\r\n".$lang_global['err_sql_conn_db']);
  }

  function db($db_name)
  {
    global $lang_global;

    if ( $this->link_id )
    {
      if ( @mysql_select_db($db_name, $this->link_id) )
        return $this->link_id;
      else
        die(error($db_name."\r\n".mysql_error()."\r\n".$lang_global['err_sql_open_db']." ('".$db_name."')"));
    }
    else
      die($db_name."\r\n".mysql_error()."\r\n".$lang_global['err_sql_conn_db']);
  }

  function query($sql)
  {
    $this->query_result = @mysql_query($sql, $this->link_id);

    if ( $this->query_result )
    {
      ++$this->num_queries;
      return $this->query_result;
    }
    else
    {
      die($sql."\r\n".mysql_error($this->link_id));
      return false;
    }
  }

  function result($query_id = 0, $row = 0, $field = NULL)
  {
    if ( $query_id )
      return @mysql_result($query_id, $row, $field);
    else
      return false;

    //return ($query_id) ? @mysql_result($query_id, $row, $field) : false;
  }

  function fetch_row($query_id = 0)
  {
    return ( ( $query_id ) ? @mysql_fetch_row($query_id) : false );
  }

  function fetch_array($query_id = 0)
  {
    return ( ( $query_id ) ? @mysql_fetch_array($query_id) : false );
  }

  function fetch_assoc($query_id = 0)
  {
    return ( ( $query_id ) ? @mysql_fetch_assoc($query_id) : false );
  }

  function num_rows($query_id = 0)
  {
    return ( ( $query_id ) ? @mysql_num_rows($query_id) : false );
  }

  function num_fields($query_id = 0)
  {
    return ( ( $query_id ) ? @mysql_num_fields($query_id) : false );
  }

  function affected_rows()
  {
    return ( ( $this->link_id ) ? @mysql_affected_rows($this->link_id) : false );
  }

  function insert_id()
  {
    return ( ( $this->link_id ) ? @mysql_insert_id($this->link_id) : false );
  }

  function get_num_queries()
  {
    return $this->num_queries;
  }

  function free_result($query_id = false)
  {
    return ( ( $query_id ) ? @mysql_free_result($query_id) : false );
  }

  function field_type($query_id = 0, $field_offset)
  {
    return ( ( $query_id ) ? @mysql_field_type($query_id, $field_offset) : false );
  }

  function field_name($query_id = 0, $field_offset)
  {
    return ( ( $query_id ) ? @mysql_field_name($query_id, $field_offset) : false );
  }

  function quote_smart($value)
  {
    if( is_array($value) )
    {
      return array_map(array(&$this, "quote_smart"), $value);
    }
    else
    {
      if( get_magic_quotes_gpc() )
        $value = stripslashes($value);
      if( $value === "" )
        $value = NULL;
      if ( function_exists("mysql_real_escape_string") )
        return mysql_real_escape_string($value, $this->link_id);
      else
        return mysql_escape_string($value);
    }
  }

  function error()
  {
    return mysql_error($this->link_id);
  }

  function close()
  {
    global $tot_queries;

    $tot_queries += $this->num_queries;
    if ( $this->link_id )
    {
      if ( $this->query_result )
        @mysql_free_result($this->query_result);
      return
        @mysql_close($this->link_id);
    }
    else
      return false;
  }

  function start_transaction()
  {
    return;
  }

  function end_transaction()
  {
    return;
  }
}

?>
