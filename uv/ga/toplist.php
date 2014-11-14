<?php

require_once __DIR__ . '/bootstrap.php';

$dateY = new DateTime();
$dateY->sub(new DateInterval('P1D'));
$dateY=$dateY->format('Y-m-d'); 
$dateT = new DateTime();
$dateT=$dateT->format('Y-m-d');

use Gapi\Gapi;

$ga = new Gapi(ga_email,ga_password);
$ga->requestReportData(
	ga_profile_id,
    array('pagePath'),
    array('pageviews', 'visits'),
	array('-pageviews'),
	"ga:pagePath!=/",
	$dateY,
	$dateT,
	1,
	5);
	

	
?>
<h4>The Idiot Index | <small>Most <em>unvis.it</em>-ed yesterday</small></h4>
<table style="table-layout:fixed;width:100%">
<?php
$nr = 0;
foreach($ga->getResults() as $result):
	$nr++;
?>
<tr>
	<td style="width:10px" class="toplistNo"><?php echo $nr;?></td>
	<td class="toplistLink" style="text-overflow:ellipsis;max-width:590px;min-width:350px;overflow:hidden;white-space:nowrap;width:100%"><a href="http://unvis.it<?php echo $result ?>"><?php echo $result ?></td>
	<td><?php //echo $result->getPageviews() ?></td>
</tr>
<?php
endforeach

?>
</table>

