<script type="text/javascript">
var xmlHttp;
function request(player_id,resend_type,product_name,trans_id){
	xmlHttp = GetXmlHttpObject();
	if(xmlHttp == null){
		return;
	}
	var dest = "/index.php/order_lose_resend/execute/"+player_id+"/"+resend_type+"/"+product_name+"/"+trans_id;
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
	var player_id = document.getElementById("player_id").value;
	if(isNaN(player_id)){
		return;
	}
	var resend_type_select = document.getElementById("order_lose_resend_type_list");
	var resend_type = escape(resend_type_select.options[resend_type_select.selectedIndex].getAttribute("order_lose_resend_type_name"));
	if (!resend_type) {
		return;
	}
	var product_list_select = document.getElementById("product_list");
	var product_name = escape(product_list_select.options[product_list_select.selectedIndex].getAttribute("product_name"));
	if (!product_name) {
		return;
	}
	var trans_id = document.getElementById("trans_id").value;
	if(!trans_id){
		return;
	}
	request(player_id,resend_type,product_name,trans_id);
}

function update_product_list(resend_type) {
	if (!resend_type) {
		return;
	}
	var player_id = document.getElementById("player_id").value;
	var trans_id = document.getElementById("trans_id").value;	
	if (player_id == '') {
		player_id = '%20';
	}
	if (trans_id == '') {
		trans_id = '%20';
	}
	var form = document.createElement("form");
	form.action = "/index.php/order_lose_resend/update_product_list/_" +
	resend_type + "_/_" + encodeURI(player_id) + "_/_" + encodeURI(trans_id) + "_/";
	form.submit();		
}

</script>
</head>
<body>
<h1 align="center"><?php echo LG_ORDER_LOSE_RESEND?></h1>
<div align="center">

<?php echo LG_PLAYER_ID.' '?><input type="text" id="player_id" value=""></input>

<div>
<?php echo LG_ORDER_LOSE_RESEND_TYPE?><select id="order_lose_resend_type_list" onchange="update_product_list(this.value)">
<?php 
	foreach($order_lose_resend_type_list as $order_lose_resend_type_info){
		$resend_name = $order_lose_resend_type_info['name'];
		echo "<option order_lose_resend_type_name=$resend_name>".$resend_name."</option>";
	}
?>
</select>
</div>

<div>
<?php echo LG_PRODUCT_NAME?><select id="product_list">
<?php 
	foreach($product_list as $product_info){
		echo "<option product_name=". $product_info->name .">".$product_info->name."</option>";
	}
?>
</select>
</div>

<?php echo LG_PLAT_FORM_TRANS_ID.' '?><input type="text" id="trans_id" value=""></input><br></br><button type="button" onclick="execute()"><?php echo LG_EXECUTE?></button>
<div id="result"></div>
</div>
</body>