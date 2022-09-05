<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);


/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */


// REMOVE THIS BLOCK - used for DataTables test environment only!
/*$file = $_SERVER['DOCUMENT_ROOT'].'/datatables/mysql.php';
if ( is_file( $file ) ) {
	include( $file );
}*/


class SSP {
	/**
	 * Create the data output array for the DataTables rows
	 *
	 *  @param  array $columns Column information array
	 *  @param  array $data    Data from the SQL get
	 *  @return array          Formatted data in a row based format
	 */
	static function data_output ( $columns, $data )
	{
		$out = array();
		// $columns = self::pluck($columns, 'db');

		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				//if ($colu)

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
				}
				else {
					$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
				}
			}

			$out[] = $row;
		}
		
		return $out;
	}


	/**
	 * Database connection
	 *
	 * Obtain an PHP PDO connection from a connection details array
	 *
	 *  @param  array $conn SQL connection details. The array should have
	 *    the following properties
	 *     * host - host name
	 *     * db   - database name
	 *     * user - user name
	 *     * pass - user password
	 *  @return resource PDO connection
	 */
	static function db ( $conn )
	{
		if ( is_array( $conn ) ) {
			return self::sql_connect( $conn );
		}

		return $conn;
	}


	/**
	 * Paging
	 *
	 * Construct the LIMIT clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL limit clause
	 */
	static function limit ( $request, $columns )
	{
		$limit = '';

		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']) . " ";
		}

		return $limit;
	}


	/**
	 * Ordering
	 *
	 * Construct the ORDER BY clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL order by clause
	 */
	static function order ( $request, $columns )
	{
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';
					$larr_tablefield = explode(" as", $column['db']);	
					$orderBy[] = $larr_tablefield[0] .' '.$dir;	
					//$orderBy[] = '`' . $request['tablename'] . '`' .  '.' . '`'.$column['db'].'` '.$dir;
				}
			}

			$order = 'ORDER BY '.implode(', ', $orderBy);
		}

		return $order;
	}


	/**
	 * Searching / Filtering
	 *
	 * Construct the WHERE clause for server-side processing SQL query.
	 *
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here performance on large
	 * databases would be very poor
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @param  array $bindings Array of values for PDO bindings, used in the
	 *    sql_exec() function
	 *  @return string SQL where clause
	 */
	static function filter ( $request, $columns, &$bindings )
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );

		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];
			$str = stripslashes($str);
			//$pinoutName = mysql_real_escape_string($pinoutName);
			//$pinoutName = mysql_escape_string($pinoutName);
			//$str = addslashes($str);

			//$str = str_replace("'","\\'",$str);
			$str = mysql_escape_string($str); 

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				$larr_tablefield = explode(" as", $column['db']);
				
				if ( $requestColumn['searchable'] == 'true' &&
			 			$str != '' )  //COMMENT BY CLEO : To Filter All
				{
					$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					$globalSearch[] = "LCASE(". $larr_tablefield[0]. ") LIKE LCASE('". '%'. $str .'%'."')";
				}
			}
		}

		// Individual column filtering
		for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $request['columns'][$i];
			$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $columns[ $columnIdx ];

			$str = $requestColumn['search']['value'];
			$str = stripslashes($str);
			$str = addslashes($str);
			$str = str_replace("'","\\'",$str);

			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
				$columnSearch[] = "LCASE(".$column['db'].") LIKE LCASE(".$binding.")";
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
		}

		return $where;
	}



	/* 88888888888888888888888888888888 */

static function InnerJoin ( $request)
	{ 
		if (isset($request["innerjoinTable"]) && $request["innerjoinTable"] !== "")
				{
			$sanitizedInnerjoinTable = explode("|",$request["innerjoinTable"]);		
				
			$larr_table = array();
			$lch_tempTablename = "`" . $request["tablename"] . "`" ;
			$innerjoinTable = "";
			foreach ($sanitizedInnerjoinTable as $keyName => $keyValue) {
					$larr_table = explode(",",$keyValue);
					/*
					if ($lch_tempTablename!= $larr_table[0])  
					{
					$innerjoinTable = $innerjoinTable . " LEFT JOIN " . $larr_table[0]  . " ON " . $larr_table[1] . " ";
					}
					else */
					{
					$innerjoinTable = $innerjoinTable . " LEFT JOIN " . $larr_table[0]  . " ON " . $larr_table[1] . " ";	
					}
			}

			if($innerjoinTable !=="")
			{
					return $innerjoinTable ;
					//	$query = $query . $innerjoinTable ;
						// var_dump($query);
			}
			else
			{
				return "" ;
			}

		}
		else
		{
			return "" ;
		}			
	}


	static function LeftJoin ( $request)
	{ 
		if (isset($request["leftjoinTable"]) && $request["leftjoinTable"] !== "")
				{
			$sanitizedleftjoinTable = explode("|",$request["leftjoinTable"]);		
				
			$larr_table = array();
			$lch_tempTablename = "`" . $request["tablename"] . "`" ;
			$leftjoinTable = "";
			foreach ($sanitizedleftjoinTable as $keyName => $keyValue) {
					$larr_table = explode(",",$keyValue);
					/*
					if ($lch_tempTablename!= $larr_table[0])  
					{
					$leftjoinTable = $leftjoinTable . " LEFT JOIN " . $larr_table[0]  . " ON " . $larr_table[1] . " ";
					}
					else */
					{
					$leftjoinTable = $leftjoinTable . " LEFT JOIN " . $larr_table[0]  . " ON " . $larr_table[1] . " ";	
					}
			}

			if($leftjoinTable !=="")
			{
					return $leftjoinTable ;
					//	$query = $query . $leftjoinTable ;
						// var_dump($query);
			}
			else
			{
				return "" ;
			}

		}
		else
		{
			return "" ;
		}			
	}



/*	8888888888888888888888888888888888 */





	/**
	 * Perform the SQL queries needed for an server-side processing requested,
	 * utilising the helper functions of this class, limit(), order() and
	 * filter() among others. The returned array is ready to be encoded as JSON
	 * in response to an SSP request, or can be modified if needed before
	 * sending back to the client.
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @return array          Server-side processing response array
	 */


	/**
	 * Perform the SQL queries needed for an server-side processing requested,
	 * utilising the helper functions of this class, limit(), order() and
	 * filter() among others. The returned array is ready to be encoded as JSON
	 * in response to an SSP request, or can be modified if needed before
	 * sending back to the client.
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @param  array $filterNamesAndTypes - field_table_name and data_type from filter of Masterfiles - specific to IISAAC/IISAIAH
	 *  @param  array $filterValues - value of filter from filter of Masterfiles - specific to IISAAC/IISAIAH
	 *  @return array          Server-side processing response array
	 */
	static function customizedSimple ( $request, $conn, $table, $primaryKey, $columns, $filterNamesAndTypes, $filterValues )
	{
		$bindings = array();
		$db = self::db( $conn );

		// Build the SQL query string from the request
		$limit = self::limit( $request, $columns );
		$order = self::order( $request, $columns );
		//$innerjoin = "";
		$innerjoin = self::InnerJoin( $request);
		$leftjoin = self::LeftJoin( $request);
		$where = self::filter( $request, $columns, $bindings );
		$where2 = self::filterWhere ($where,$filterNamesAndTypes,$filterValues,$request,$table);
		$where3 = self::filterWhereLeftJoin ($where,$where2,$request,$table);
		$groupby = self::groupBy($request);

		//echo json_encode($columns);
		//echo "SELECT SQL_CALC_FOUND_ROWS ".  implode(",", self::pluck($columns, 'db')) ." FROM `$table` $innerjoin $where $where2 $order $limit\n\n\n";
		// Main query to actually get the data

		// implode(",", self::pluck($columns, 'db'))
		$larr_Columns = self::pluck($columns, 'db');
		$lch_ColumnName = "";
		foreach ($larr_Columns as $lch_FieldName) {
			//echo $lch_FieldName;
			$lch_ColumnName = $lch_ColumnName . $lch_FieldName . "," ;
		}
		$lch_ColumnName = rtrim($lch_ColumnName,",");
		//echo $lch_ColumnName;
		$data = self::sql_exec( $db, $bindings,
			"SELECT SQL_CALC_FOUND_ROWS ".  $lch_ColumnName ."
		
			 FROM `$table`
			 $innerjoin
			 $leftjoin
			 $where
			 $where2
			 $where3
			 $groupby
			 $order
			 $limit"
		);



		// Data set length after filtering
		$resFilterLength = self::sql_exec2( $db,
			"SELECT FOUND_ROWS()"
		);

		$recordsFiltered = $resFilterLength[0][0];


		// Total data set length
		// $resTotalLength = self::sql_exec2( $db,
		// 	"SELECT COUNT($primaryKey)
		// 	 FROM   `$table`"
		// );
		// $recordsTotal = $resTotalLength[0][0];
		$recordsTotal = $resFilterLength[0][0];

		foreach ($columns as $key => $value) {
			$lch_field = explode(".", $value["db"]);
			if (count($lch_field)>1){

				$lch_alias = explode(" ",str_replace("`","",$lch_field[count($lch_field) - 1]));
				if(count($lch_alias)>1){
				$columns[$key]["db"] = $lch_alias[2];
				}
				else{
				$columns[$key]["db"] = str_replace("`","",$lch_field[count($lch_field) - 1]);
				}
			}
			

		}

		$db = null;
		
		/*
		 * Output
		 */
		return array(
			//"query-output"	  => "SELECT SQL_CALC_FOUND_ROWS $lch_ColumnName FROM `$table` $innerjoin $leftjoin $where $where2 $where3 $order $limit",
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $data )
			//"sample22"		  => $leftjoin
			//"sample333"		  =>     $data,
			//"sample33"		  => $columns

			

		);

		//echo "SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", self::pluck($columns, 'db'))."` FROM `$table` $where $where2 $order $limit";
		/* "sample11"		  => $primaryKey,
			"sample222"		  =>    $columns,
			"sample333"		  =>     $data,
			"sample444"		  => $bindings
			"sample" 		  => $where,
			"sample22"		  => $innerjoin,
			"sample33"		  => $columns
			*/

	}
	//"SELECT *" . " FROM " . $table .  $innerjoin . $where .  $where2 .  $order . $limit

	/**
	* Construct SQL Where clause for filters - specific to IISAAC/IISAIAH
	* 
	* @param String $where - from self::filter -> used to determine if still need to affix 'WHERE' string
	* @param array $filterNamesAndTypes - field_table_name and data_type from filter of Masterfiles - specific to IISAAC/IISAIAH
	* @param array $filterValues - value of filter from filter of Masterfiles - specific to IISAAC/IISAIAH
	* @return String $filterWhere - filter SQL Where Clause
	*
	*/


	static function filterWhere($where,$filterNamesAndTypes,$filterValues,$request,$table){
		$filterWhere = '';

		// Match Name and Value here
		if ($filterNamesAndTypes!==''){
			$filterNamesAndTypesArray = explode("|",$filterNamesAndTypes);

			$filterValueArray = array();
			if ($filterValues!==''){
				$filterValueArray = explode("|",$filterValues);
			} // if ($filterValues!==''){

			// cycle through the array to match values
			foreach ($filterNamesAndTypesArray as $filter_name_key => $filter_name_value){
				$filter_attributes = explode("-",$filter_name_value);

				// make only if $filterValueArray[$filter_name_key] has value
				if (array_key_exists($filter_name_key, $filterValueArray)==true&&$filterValueArray[$filter_name_key]!=""){

					// precede AND if more than 2 ang query
					if ($filterWhere!=""){
						//echo "$filterValueArray[$filter_name_key] pasok wtf? ";
						$filterWhere = $filterWhere.' AND ';
					}

					// if data type = 9 (Checkbox), use IN()
					if ($filter_attributes["1"]=="9"){
						// fix IN() query
						$checkboxArray = explode(",",$filterValueArray[$filter_name_key]);
						$inConditions  = "";
						if (count($checkboxArray)>0){
							$inConditions = $inConditions . $table . "." . $filter_attributes["0"] . " IN (";
							foreach ($checkboxArray as $str){
								$inConditions = $inConditions . "'$str',";
							}
							$inConditions = rtrim($inConditions,",");
							$inConditions = $inConditions . ") ";
							$filterWhere = $filterWhere . " " . $inConditions;
						} // if (count($checkboxArray)>0){
					} // if ($filter_attributes["1"]=="9"){

					// CHECKBOX BOT NOT_IN CONDITION
						
					else if ($filter_attributes["1"]=="12"){
						// fix IN() query
						$checkboxArray = explode(",",$filterValueArray[$filter_name_key]);
						$inConditions  = "";
						if (count($checkboxArray)>0){
							$inConditions = $inConditions . " NOT " . $table . "." . $filter_attributes["0"] . " IN (";
							foreach ($checkboxArray as $str){
								$inConditions = $inConditions . "'$str',";
							}
							$inConditions = rtrim($inConditions,",");
							$inConditions = $inConditions . ") ";
							$filterWhere = $filterWhere . " " . $inConditions;
						} // if (count($checkboxArray)>0){
					} // else if ($filter_attributes["1"]=="12"){

					else if ($filter_attributes["1"]=="13"){

						// fix IN() query
						$checkboxArray = explode(",",$filterValueArray[$filter_name_key]);
						$findinsetConditions  = "";
						if (count($checkboxArray)>0){
							$findinsetConditions = $findinsetConditions . " (";
							foreach ($checkboxArray as $str){
								//$findinsetConditions = $findinsetConditions . "'$str',";
								$findinsetConditions = $findinsetConditions . " FIND_IN_SET ('".$str."',".$table . "." . $filter_attributes["0"].") OR ";
								// $table . "." . $filter_attributes["0"]
							}
							$findinsetConditions = rtrim($findinsetConditions, "OR ");
							$findinsetConditions = $findinsetConditions . ")";
							$filterWhere = $filterWhere . " " . $findinsetConditions;
						} // if (count($checkboxArray)>0){

					} // else if ($filter_attributes["1"]=="13"){

					// NOT EQUALS
					else if ($filter_attributes["1"]=="14"){

						$larr_str = explode(".",$filter_attributes["0"]);
						if (count($larr_str)>1) {
							$filterWhere = $filterWhere . " " . '`' . $larr_str['0'] . '`' .  '.' .  $larr_str["1"] . " <>  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // if (count($larr_str)>1) {
						else {
							$filterWhere = $filterWhere . " " . '`' . $request['tablename'] . '`' .  '.' .  $filter_attributes["0"] . " <>  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // ELSE ng if (count($larr_str)>1) {


					} // else if ($filter_attributes["1"]=="14"){

					// GREATER THAN ONLY ( > not >= )
					else if ($filter_attributes["1"]=="15"){

						$larr_str = explode(".",$filter_attributes["0"]);
						if (count($larr_str)>1) {
							$filterWhere = $filterWhere . " " . '`' . $larr_str['0'] . '`' .  '.' .  $larr_str["1"] . " >  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // if (count($larr_str)>1) {
						else {
							$filterWhere = $filterWhere . " " . '`' . $request['tablename'] . '`' .  '.' .  $filter_attributes["0"] . " >  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // ELSE ng if (count($larr_str)>1) {

					} // else if ($filter_attributes["1"]=="15"){

					// LESS THAN ONLY ( < not <= )
					else if ($filter_attributes["1"]=="16"){

						$larr_str = explode(".",$filter_attributes["0"]);
						if (count($larr_str)>1) {
							$filterWhere = $filterWhere . " " . '`' . $larr_str['0'] . '`' .  '.' .  $larr_str["1"] . " <  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // if (count($larr_str)>1) {
						else {
							$filterWhere = $filterWhere . " " . '`' . $request['tablename'] . '`' .  '.' .  $filter_attributes["0"] . " < '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // ELSE ng if (count($larr_str)>1) {

					} // else if ($filter_attributes["1"]=="16"){


					// if data type = 1,5,6 (text, textarea, texteditor) use LIKE
					else if ($filter_attributes["1"]=="1" || $filter_attributes["1"]=="5" || $filter_attributes["1"]=="6") {
						//if ()
						$filterWhere = $filterWhere . " LCASE(" . '`' . $request['tablename'] . '`' .  '.' .  $filter_attributes["0"] . ") LIKE LCASE('%".str_replace("'","\\'",$filterValueArray[$filter_name_key])."%') ";
					} // else if ($filter_attributes["1"]=="1" || $filter_attributes["1"]=="5" || $filter_attributes["1"]=="6") {
					else {

						$larr_str = explode(".",$filter_attributes["0"]);
						if (count($larr_str)>1) {
							$filterWhere = $filterWhere . " " . '`' . $larr_str['0'] . '`' .  '.' .  $larr_str["1"] . " =  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // if (count($larr_str)>1) {
						else {
							$filterWhere = $filterWhere . " " . '`' . $request['tablename'] . '`' .  '.' .  $filter_attributes["0"] . " =  '". str_replace("'","\\'",$filterValueArray[$filter_name_key]) ."' ";
						} // ELSE ng if (count($larr_str)>1) {

						
					} // ELSE ng else if ($filter_attributes["1"]=="1" || $filter_attributes["1"]=="5" || $filter_attributes["1"]=="6") {
				} //if ($filterValueArray[$filter_name_key]!=""){

			} // foreach ($filterNamesAndTypesArray as $filter_name_key => $filter_name_value){

		} // if ($filterNamesAndTypes!==''){

		// finalize query
		if ( $where === '' && $filterWhere!=='') {
			$filterWhere = 'WHERE '.$filterWhere;
		} // if ( $where === '' && $filterWhere!=='') {
		else if ($where!==''&&$filterWhere!=='') {
			$filterWhere = ' AND '.$filterWhere;
		} // else if ($where!==''&&$filterWhere!=='') {

		return $filterWhere;
	} // static function filterWhere($where,$filterNamesAndTypes,$filterValues){



	static function filterWhereLeftJoin($where,$where2,$request,$table){
		$filterWhere = '';


		if (isset($request["leftjoinWhereClause"]) && $request["leftjoinWhereClause"] !== "") {
			$sanitizedleftjoinTable = explode(",",$request["leftjoinWhereClause"]);		
			//echo $request["leftjoinWhereClause"];
			foreach ($sanitizedleftjoinTable as $keyName => $keyValue) {
				$filterWhere = $filterWhere . ' '. $keyValue . ' AND';
			}
			$filterWhere = rtrim($filterWhere,'AND');

			if ($filterWhere!='') {
				if ($where!='' || $where2!='') {
					$filterWhere = ' AND ' . $filterWhere;
				} // if ($where!='' || $where2!='') {
				else if ($where=='' && $where2=='') {
					$filterWhere = ' WHERE ' . $filterWhere;
				} // else if ($where=='' && $where2=='') {
			} // if ($filterWhere!='') {

			

		} // if (isset($request["leftjoinWhereClause"]) && $request["leftjoinWhereClause"] !== "") {
		else {
			return "" ;
		} // ELSE ng if (isset($request["leftjoinWhereClause"]) && $request["leftjoinWhereClause"] !== "") {		

		

		return $filterWhere;
	} // static function filterWhere($where,$filterNamesAndTypes,$filterValues){


	static function groupBy($request){
		$groupBy = '';


		if (isset($request["groupby"]) && $request["groupby"] !== "") {

			$groupBy = "GROUP BY " . $request["groupby"];


		} // if (isset($request["groupby"]) && $request["groupby"] !== "") {
		else {
			return "" ;
		} // ELSE ng if (isset($request["groupby"]) && $request["groupby"] !== "") {		

		

		return $groupBy;
	} // static function groupBy($where,$filterNamesAndTypes,$filterValues){


	/**
	 * The difference between this method and the `simple` one, is that you can
	 * apply additional `where` conditions to the SQL queries. These can be in
	 * one of two forms:
	 *
	 * * 'Result condition' - This is applied to the result set, but not the
	 *   overall paging information query - i.e. it will not effect the number
	 *   of records that a user sees they can have access to. This should be
	 *   used when you want apply a filtering condition that the user has sent.
	 * * 'All condition' - This is applied to all queries that are made and
	 *   reduces the number of records that the user can access. This should be
	 *   used in conditions where you don't want the user to ever have access to
	 *   particular records (for example, restricting by a login id).
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @param  string $whereResult WHERE condition to apply to the result set
	 *  @param  string $whereAll WHERE condition to apply to all queries
	 *  @return array          Server-side processing response array
	 */
	static function complex ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
	{
		$bindings = array();
		$db = self::db( $conn );
		$localWhereResult = array();
		$localWhereAll = array();
		$whereAllSql = '';

		// Build the SQL query string from the request
		$limit = self::limit( $request, $columns );
		$order = self::order( $request, $columns );
		$where = self::filter( $request, $columns, $bindings );

		$whereResult = self::_flatten( $whereResult );
		$whereAll = self::_flatten( $whereAll );

		if ( $whereResult ) {
			$where = $where ?
				$where .' AND '.$whereResult :
				'WHERE '.$whereResult;
		}

		if ( $whereAll ) {
			$where = $where ?
				$where .' AND '.$whereAll :
				'WHERE '.$whereAll;

			$whereAllSql = 'WHERE '.$whereAll;
		}

		// Main query to actually get the data
		$data = self::sql_exec( $db, $bindings,
			"SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", self::pluck($columns, 'db'))."`
			 FROM `$table`
			 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = self::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = self::sql_exec( $db, $bindings,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table` ".
			$whereAllSql
		);
		$recordsTotal = $resTotalLength[0][0];

		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $data )
		);
	}


	/**
	 * Connect to the database
	 *
	 * @param  array $sql_details SQL server connection details array, with the
	 *   properties:
	 *     * host - host name
	 *     * db   - database name
	 *     * user - user name
	 *     * pass - user password
	 * @return resource Database connection handle
	 */
	static function sql_connect ( $sql_details )
	{
		try {
			$db = @new PDO(
				"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
				$sql_details['user'],
				$sql_details['pass'],
				array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
			);
		}
		catch (PDOException $e) {
			self::fatal(
				"An error occurred while connecting to the database. ".
				"The error reported by the server was: ".$e->getMessage()
			);
		}

		return $db;
	}


	/**
	 * Execute an SQL query on the database
	 *
	 * @param  resource $db  Database handler
	 * @param  array    $bindings Array of PDO binding values from bind() to be
	 *   used for safely escaping strings. Note that this can be given as the
	 *   SQL query string if no bindings are required.
	 * @param  string   $sql SQL query to execute.
	 * @return array         Result from the query (all rows)
	 */
	static function sql_exec ( $db, $bindings, $sql=null )
	{

		// Argument shifting
		if ( $sql === null ) {
			$sql = $bindings;
			$valid = 1;
		}
		else{
			$valid = 0;
		}

		$stmt = $db->prepare( $sql );
		//echo $sql;
	
		// Bind parameters
		if ( is_array( $bindings ) ) {
			for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
				$binding = $bindings[$i];
				$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
			}
		}
		
		// Execute
		try {
			$stmt->execute();
		//	echo "hey";
		}
		catch (PDOException $e) {
			self::fatal( "An SQL error occurred: ".$e->getMessage() );
		//	echo "nono";
		}


		// Return all
		
		return $stmt->fetchAll(PDO::FETCH_NAMED);
	
	
	
	}


	static function sql_exec2 ( $db, $bindings, $sql=null )
	{

		// Argument shifting
		if ( $sql === null ) {
			$sql = $bindings;
			
		}
		

		$stmt = $db->prepare( $sql );
		//echo $sql;
	
		// Bind parameters
		if ( is_array( $bindings ) ) {
			for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
				$binding = $bindings[$i];
				$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
			}
		}
		
		// Execute
		try {
			$stmt->execute();
		//	echo "hey";
		}
		catch (PDOException $e) {
			self::fatal( "An SQL error occurred: ".$e->getMessage() );
		//	echo "nono";
		}

		
		return $stmt->fetchAll();
	}





	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Internal methods
	 */

	/**
	 * Throw a fatal error.
	 *
	 * This writes out an error message in a JSON string which DataTables will
	 * see and show to the user in the browser.
	 *
	 * @param  string $msg Message to send to the client
	 */
	static function fatal ( $msg )
	{
		echo json_encode( array( 
			"error" => $msg
		) );

		exit(0);
	}

	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_exec()
	 *
	 * @param  array &$a    Array of bindings
	 * @param  *      $val  Value to bind
	 * @param  int    $type PDO field type
	 * @return string       Bound key to be used in the SQL where this parameter
	 *   would be used.
	 */
	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}


	/**
	 * Pull a particular property from each assoc. array in a numeric array, 
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}

		return $out;
	}


	/**
	 * Return a string from an array or a string
	 *
	 * @param  array|string $a Array to join
	 * @param  string $join Glue for the concatenation
	 * @return string Joined string
	 */
	static function _flatten ( $a, $join = ' AND ' )
	{
		if ( ! $a ) {
			return '';
		}
		else if ( $a && is_array($a) ) {
			return implode( $join, $a );
		}
		return $a;
	}
}
?>