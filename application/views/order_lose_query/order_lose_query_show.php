<script type="text/javascript">
var xmlHttp;
function request(trans_id,order_ping_tai){
	xmlHttp = GetXmlHttpObject();
	if(xmlHttp == null){
		return;
	}
	var dest = "/index.php/order_lose_query/execute/"+trans_id+"/"+order_ping_tai;
	xmlHttp.onreadystatechange = stateChanged;
	xmlHttp.open("get", dest, true);//true：异步方式  false：同步方式
	xmlHttp.send(null);
	document.getElementById("result").innerHTML = "<?php echo LG_QUERY_PROMPT?>";
}

function stateChanged(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("result").innerHTML = xmlHttp.responseText + "<br>";
	}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 // Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}

function execute(){
	var trans_id = document.getElementById("trans_id").value;
	if(!trans_id){
		return;
	}
	var query_type_select = document.getElementById("order_lose_query_type_list");
	var query_type = escape(query_type_select.options[query_type_select.selectedIndex].getAttribute("order_lose_query_type_id"));
	if (!query_type) {
		return;
	}
	request(trans_id,query_type);
}
</script>
<head>
</head>
<body>
<h1 align="center"><?php echo LG_ORDER_LOSE_QUERY?></h1>
<div align="center">


<div>
<?php echo LG_ORDER_SOURCE?><select id="order_lose_query_type_list">
<?php 
	foreach($order_lose_query_type_list as $order_lose_query_type_info){
		$id = $order_lose_query_type_info['id'];
		$name = $order_lose_query_type_info['name'];
		echo "<option order_lose_query_type_id=$id>".$name."</option>";
	}
?>
</select>
</div>

<div>
<?php echo LG_PLAT_FORM_TRANS_ID?><input type="text" id="trans_id" value=""></input>
</div>
<button type="button" onclick="execute()"><?php echo LG_EXECUTE?></button>

<div id="result"></div>
</div>
</body>