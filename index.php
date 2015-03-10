<?php
if(!ob_start("ob_gzhandler")) ob_start(); //gzip-e-di-doo-da

// Try to remove http:// from bookmarklet and direct links. 
$urlz = $_GET['u'];
$urlz = $_SERVER['REQUEST_URI'];
$urlz = substr($urlz, 1);
if (strpos($urlz, "unvis.") !== false) {header("Location: http://unvis.it", true, 303);}
if(strpos($urlz, "http:") !== false) {
	$str = $urlz;
	$str = preg_replace('#^https?:/#', '', $str);
	header("Location: http://".$_SERVER['HTTP_HOST'].$str, true, 303); 
}

use Readability\Readability;
require_once 'uv/Readability.php';
require_once 'uv/JSLikeHTMLElement.php';
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php if ($urlz) { echo 'UV : '.$urlz;} else { echo "unvis.it – avoid endorsing idiots";} ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" type="text/css" media="screen" href="/uv/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/uv/css/bootstrap-theme.min.css" type="text/css" media="screen">
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/uv/img/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/uv/img/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/uv/img/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="/uv/img/apple-touch-icon-57-precomposed.png">
	<link rel="shortcut icon" href="/uv/img/favicon.png">
	<script type="text/javascript">
	window.google_analytics_uacct = "UA";
	</script>
</head>
<body>
	<div class="container">
		<div id="head">
			<div class="row">
				<br>
				<div class="col-md-2"></div>
				<div class="col-md-8" id="theInputForm">
					<form class="form-inline">
					  <div class="form-group">
					    <label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
					    <div class="input-group">
					      <div class="input-group-addon"><a href="http://unvis.it" id="logo" ><strong>unvis.it/</strong></a> </div>
					      <input class="form-control" type="text" name="u" id="uv" placeholder="Url you want to read without giving a pageview" value="<?php if ($urlz) { echo $urlz;} ?>" >
					    </div>
					  </div>
					 
					</form>
					
					<hr>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2"><?php if ($urlz) { ?><a href="javascript:(function(){sq=window.sq=window.sq||{};if(sq.script){sq.again();}else{sq.bookmarkletVersion='0.3.0';sq.iframeQueryParams={host:'//squirt.io',userId:'8a94e519-7e9a-4939-a023-593b24c64a2f',};sq.script=document.createElement('script');sq.script.src=sq.iframeQueryParams.host+'/bookmarklet/frame.outer.js';document.body.appendChild(sq.script);}})();" class="btn btn-default btn-mini hidden-phone" style="position: relative;top: 20px;" id="squirt">Speed read this</a><?php } ?></div>
			<?php
			
			include_once("dbhandler.php");
			$db = new DBHandler();
			$cachevalue = $db->read($urlz);
			if (!$cachevalue && $urlz){
			?>
			<div id="theContent" class="col-md-8">
				<?php 
					echo "Looks like we couldn't find the content ¯\_(ツ)_/¯";
					// User agent switcheroo
					$UAnum = Rand (0,3) ; 

					switch ($UAnum) 
 					{ 
 					case 0: 
						$UAstring = "User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)\r\n"; 
 					break; 
 
					case 1: 
				 		$UAstring = "Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)\r\n"; 
					break; 
 
 					case 2: 
 						$UAstring = "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)\r\n"; 
 					break; 
 
 					case 3: 
	 					$UAstring = "Baiduspider+(+http://www.baidu.com/search/spider.htm)  \r\n"; 
 					break;
					
					// If this works, many lolz acquired.
					
					} 

					if ($_GET["u"]) {
					

					$url = urldecode($urlz);
					
					if (!preg_match('!^https?://!i', $url)) $url = 'http://'.$url;
					
					// Create a stream
					$opts = array(
					  'http'=>array(
					    'method'=>"GET",
					    'header'=>$UAstring
					  )
					);

					$context = stream_context_create($opts);
					$html = @file_get_contents($url, false, $context);
					
				}
 
					// PHP Readability works with UTF-8 encoded content. 
					// If $html is not UTF-8 encoded, use iconv() or 
					// mb_convert_encoding() to convert to UTF-8.

					// If we've got Tidy, let's clean up input.
					// This step is highly recommended - PHP's default HTML parser
					// often does a terrible job and results in strange output.
					if (function_exists('tidy_parse_string')) {
						$tidy = tidy_parse_string($html, array(), 'UTF8');
						$tidy->cleanRepair();
						$html = $tidy->value;
					}

					// give it to Readability
					$readability = new Readability($html, $url);

					// print debug output? 
					// useful to compare against Arc90's original JS version - 
					// simply click the bookmarklet with FireBug's 
					// console window open
					$readability->debug = false;

					// convert links to footnotes?
					$readability->convertLinksToFootnotes = true;

					// process it
					$result = $readability->init();

					// does it look like we found what we wanted?
					if ($result) {
						$header = "<h1>";
						$header .= $readability->getTitle()->textContent;
						$header .= "</h1><a href='http://unvis.it/". $urlz."' class='perma'>". $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."</a>";
						$header .=  "<hr>";
						echo $header;
						$content = $readability->getContent()->innerHTML;

						// if we've got Tidy, let's clean it up for output
						if (function_exists('tidy_parse_string')) {
							$tidy = tidy_parse_string($content, 
								array('indent'=>true, 'show-body-only'=>true), 
								'UTF8');
							$tidy->cleanRepair();
							$content = $tidy->value;
							$content = trim(preg_replace('/\s\s+/', ' ', $content));
						}
						
						
						echo $content;
						$toCache = "<div id=\"theContent\" class=\"col-md-8\">";
						$toCache .= $header.$content;
						$toCache .= "</div>";
						$db->cache($urlz,$toCache);
					}
				}else{
				//echo "From cache:";
				echo $cachevalue;
			}
			$fourohfour = False;
		?>
			</div>

			<div class="col-md-2"></div>
	</div>

		
	</div>
  	
	
	</div>
	
	<div id="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8"><?php if ($urlz) {?><hr><?php }?><?php if ( $urlz) {?>
					<small><em><b>Source:</b> <a href="https://linkonym.appspot.com/?http://<?php echo $urlz; ?>"><?php echo $urlz; ?></a></em></small>
					<hr>
					
					<p style="text-align:center"><a href="/" class="btn btn-default" >What is unvis.it?</a></p>
					<br><br><?php } else {?>
					<?php //require_once('uv/ga/toplist.php');?>
					
					<h1 id="about">What is unvis.it?</h1>				
					<p>Unvis.it is a tool to escape linkbaits, trolls, idiots and asshats. </p>
					<p>What the tool does is to try to capture the content of an article or blog post without passing on your visit as a pageview. Effectively this means that you're not paying with your attention, so you can <strong>read and share</strong> the idiocy that it contains.</p>
					<p><small>Now with a speed reading options from <a href="http://www.squirt.io/">Squirt</a>, so you can get dumbfounded quicker!</small></p>
					<br>
					<p><b>FAQ:</b>
						<ul>
							<li><b>Is this legal?</b> Probably not. </li>
							<li><b>Does it work with any website?</b> Certainly not. </li>
							<li><b>Do we track you?</b> Only through Google <del>Echelon</del> Analytics.</li>
							<li><b>Is it open source?</b> <a href="https://github.com/phixofor/unvis.it">Sure, why not?</a></li>
							<li><b>I heard someone made a Firefox add-on?</b> <a href="https://addons.mozilla.org/en-US/firefox/addon/unvisit/">Indeed!</a></li>
							<li><b>I need anonymous file hosting?</b> Check out <a href="http://minfil.org">Minfil.org</a></li>
						</ul>
					<p>Enjoy literally not feeding the trolls!</p>
					<br>
					<p style="text-align:center"> <a href="javascript:var orig%3Dlocation.href%3Blocation.replace(%27http://unvis.it/%27%2Borig)%3B" class="btn btn-sm btn-info">Drag <b>this</b> to your bookmarks bar to unvis.it any page</a></p>
					<hr>
					<h2>Now: the same info in infographics</h2>
					<p style="text-align:center;"><img src="/uv/img/unvisit-xplaind.png" alt="What's this, I don't even…" title="What's this, I don't even…" ></p>
					<hr>
					<p style="text-align:center">	
						<img src="/uv/img/icon_large.png" alt="OMG LOGOTYPE" title="OMG LOGOTYPE" style="width:150px;height:150px">
						<br><br><br>
						<?php //<a href="http://www.lolontai.re"><img src="/uv/img/lulz.png" id="lulz" alt="Sir Lulz-a-Lot approves" title="Sir Lulz-a-Lot approves"></a>?>
						<br><br><br><br><br><br><br><br>
					</p>
					<?php } ?>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
	<script type="text/javascript" src="/uv/js/bootstrap.min.js"></script>
<script type="text/javascript" >
	$(document).ready(function() {
		$("#uv").change(function() {
			theURL = $("#uv").val();
			theURL = theURL.replace(/.*?:\/\//g, "");
			theURL = decodeURIComponent(theURL);
			$("#uv").val(theURL);
		});
		
		$("#uv").click(function() {
			$(this).select();
		});
		
		function leSwitcheroo(){
			var orig=$("#uv").val();
			var urlz=location.host;
			location.replace("http://"+urlz+"/"+orig);
		};
		
		$("#uv").keyup(function(event){
		    if(event.keyCode == 13){
  				leSwitcheroo()
		    }
		});
		
		$('.toplistLink a').on('click', function() {
			var a_href = $(this).attr('href');
			ga('send', 'event', 'toplist', 'click', a_href);
		});
		$('a#squirt').on('click', function(){
			ga('send', 'event', 'article', 'click', "squirt");
		});
	});
	</script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA', 'unvis.it');
	  ga('require', 'linkid', 'linkid.js');
	  ga('send', 'pageview');
	  
	  

	</script>
	<noscript><img src="http://nojsstats.appspot.com/UA/<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?><?php if($_SERVER['HTTP_REFERER']){echo '?r='.$_SERVER['HTTP_REFERER'];}; ?>&dummy=<?php echo rand(); ?>" /></noscript>
	<!-- Begin Creeper tracker code -->
	<a href="http://gnuheter.com/creeper/senaste" title="Creeper"><img src="http://gnuheter.com/creeper/image" alt="Creeper" width="1" height="1" border="0"/></a>
	<!-- End Creeper tracker code -->
	
	

</body>
</html>
