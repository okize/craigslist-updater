<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Craigslist update log</title>
	<style type="text/css">
		body { background-color: #fff; font-family: Consolas, Courier, monospace; font-size: 100%; margin: 0; padding: 0; }
		ul { margin: 0; padding: 0; list-style: none; }
		li { padding: 1em; border-bottom: 3px dashed #ccc; }		
		li a { display: block; font-size: 1.5em; color: #3c4fcf; font-weight: bold; text-decoration: none; }
		li em { color: #666; font-size: 1em; }
		li:hover { background-color: #efefef; }
		p { font-weight: bold; color: #f00; margin: 20px; }
	</style>
</head>
<body>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript">
				
	$(document).ready(function ($) {
		
		$.getJSON('log.json')
			.success(function (response) {
				
				// check if the log is empty
				if ($.isEmptyObject(response)) {
					
					$('body').append('<p>The log file is blank!</p>');
					
				} 
				// ...or mostly empty
				else if (!response[0].title) {
					
					$('body').append('<p>The log is basically empty!</p>');
					
				}
				// if there's data, display it
				else {

					// sort the json by date
					response.sort(sortByDate);
			
		            var items = [];
					items[0] = '<ul id="logList">';
					var dateStamp;
					
	                for (var i in response) {
	         			dateStamp = new Date(response[i].dateStamp);
	                	items.push('<li><a href="' + response[i].url + '"><em>On ' + dateStamp.toLocaleDateString() + ' @ ' + dateStamp.toLocaleTimeString() + ' logged:</em><br />' + response[i].title + '</a></li>');
			        }				
					items[items.length] = '</ul>';				
					$('body').append(items.join(''));
					
				}
				
	        })
			.error(function() { 
			
				$('body').append('<p>Couldn\'t open the log file!</p>');
				
			});
			
		function sortByDate(a, b) {
		    return new Date(b.dateStamp).getTime() - new Date(a.dateStamp).getTime();
		}
					
	});
	
	</script>
	
</body>
</html>