<?php
set_time_limit( 180);

// require 'dbfunc.php';

function isql_getmicrotime()
{
	list( $usec, $sec)= explode( " ", microtime());

	return ( ( float) $usec + ( float) $sec);
}

$time_start = isql_getmicrotime();

function QFetchRowA( $Rs){
	return mysql_fetch_array( $Rs);
}
function QFetchRowAHT( $Rs){
	if( $row= QFetchRowA( $Rs))
		while( list( $k, $v)= each( $row)){
			$v= chop( HtmlSpecialChars( $v));
			if( $v== '') $v= '&nbsp;';
		}
	return $row;
}

$Qry= StripSlashes($_POST[Qry]);
?>
<html>
<body bgcolor=#3f3f3f>
<form method=post>
<?php
echo "<font color=white>". date( "D m/d H:i")."</font><br>";
?>
<textarea rows=12 cols=100 name='Qry'>
<?php
echo HTMLSpecialChars( $Qry);
?>
</textarea><br>
<input type=submit>
</form>
<?php
	if ($Qry)
{
	$AQry= split( ";[\n\r]+", $Qry);

	$conn = mysql_connect('localhost', 'root', 'SafeStart!@');
	if (!$conn)
	{
		echo 'Database host: Connection Error!';
		exit();
	}

	$db = mysql_select_db('safe-start', $conn);
	if (!$db)
	{
		echo 'Database: Select Db Error!';
		exit();
	}

	// QRExec($Conn, 'SET NAMES UTF8');

	foreach( $AQry as $Qry)
	{
		if( !chop( $Qry))
		{
			continue;
		}

		$rs = mysql_query($Qry, $conn);
		if (!$rs)
		{
			echo 'Query Error!';
			exit();
		}

		$time_end = isql_getmicrotime();
		$time = $time_end - $time_start;

		echo "<p><font color=#dfdfdf>time of query = $time sec",
			"<p>";

		if ($rs && ($retRows = @mysql_num_rows($rs))> -1)
		{
			echo "<font color=white><b>$retRows rows affected</b></font><br>";

		}
		else if (($retRows = @mysql_affected_rows($conn))> -1)
		{
			echo "<font color=white><b>$retRows rows affected</b></font><br>";
		}
	}

echo  'client encoding '. mysql_client_encoding();
echo "\n<br>\n";

	if ($rs && ($cols = @mysql_num_fields($rs)))
	{
		echo "<table bgcolor=#9fbfcf border=1 cellpadding=1 cellspacing=0><tr bgcolor=#cfcfcf><th>N/n</th>\n";
		for( $i= 0; $i< $cols; $i++)
		{
			echo '<th>'. mysql_field_name($rs, $i). "</th>\n";
		}

		echo "</tr>\n";
		echo '<tr bgcolor=#cfcfcf><th>&nbsp;</th>';

		for( $i= 0; $i< $cols; $i++)
		{
			echo '<th>'. mysql_field_type($rs, $i). '( '. mysql_field_len($rs, $i). ")</th>\n";
		}

		echo "</tr>\n";

		for ($n= 1; $row = QFetchRowAHT($rs); $n++)
		{
			echo "<tr><td bgcolor=#cfcfcf>$n</td>\n";

			for ($i= 0; $i < $cols; $i++)
			{
				echo "<td>{$row[$i]}</td>\n";
			}

			echo '</tr>';
			echo "\n";
		}
		echo '</table>';
	}
}?>
</body>
</html>
