<?php
include('./httpful.phar');
include "db.php";
include "functions.php";
if(!isset($_GET['action'])){
  $messages=[];
//  $btcData=json_decode(CallAPI('GET','https://api.coinbase.com/v2/exchange-rates?currency=BTC'));
if($_GET['currency']=="ETH"){
  $btcData = \Httpful\Request::get('https://api.coinbase.com/v2/exchange-rates?currency=ETH')->send();
  $messages[]=chatfuelText("the etherium rate is ".$btcData->body->data->rates->USD."\$");

}
else{
  $btcData = \Httpful\Request::get('https://api.coinbase.com/v2/exchange-rates?currency=BTC')->send();
  $messages[]=chatfuelText("the bitcoin rate is ".$btcData->body->data->rates->USD."\$");

}

  chatfuelSend($messages);
}
if($_GET['action']=="register"){
  if (!($stmt = $mysqli->prepare("INSERT INTO user (facebook,coinbase_key,coinbase_secret) VALUES (?,?,?)"))) {
   echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$coinbase_key=$_GET['coinbase_key'];
$coinbase_secret=$_GET['coinbase_secret'];
$facebook=$_GET['facebook'];
if (!$stmt->bind_param("sss", $facebook,$coinbase_key,$coinbase_secret)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
$messages=[];
$messages[]=chatfuelText("registration successful");
chatfuelSend($messages);
}
if($_GET['action']=="flags"){

  if (!($stmt = $mysqli->prepare("INSERT INTO flags (facebook,sum,operator,currency) VALUES (?,?,?,?)"))) {
   echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
  if (!($stmtdelete = $mysqli->prepare("DELETE FROM flags WHERE facebook=? & operator=? &currency=?"))) {
   echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$operator=$_GET['operator'];
$sum=$_GET['sum'];
$facebook=$_GET['facebook'];
$currency=isset($_GET['currency'])?$_GET['currency']:"BTC";
if (!$stmt->bind_param("ssss", $facebook,$sum,$operator,$currency)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmtdelete->bind_param("sss", $facebook,$operator,$currency)) {
    echo "Binding2 parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmtdelete->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
$messages=[];
$messages[]=chatfuelText("We will notify you");
chatfuelSend($messages);
}
if($_GET['action']=="job"){
  $result = mysqli_query($mysqli,"SELECT * FROM flags");
  $all=[];
  while($row = mysqli_fetch_array($result)){
     $all[]=$row;
  }
  echo json_encode($all);
}
if($_GET['action']=="cronejob"){
 $current=[];
  $btcData = \Httpful\Request::get('https://api.coinbase.com/v2/exchange-rates?currency=ETH')->send();
  $current['ETH']=$btcData->body->data->rates->USD;
  $btcData = \Httpful\Request::get('https://api.coinbase.com/v2/exchange-rates?currency=BTC')->send();
  $current['BTC']=$btcData->body->data->rates->USD;
var_dump($current);
  foreach ($current as $key => $value) {
    if($value==NULL){
      die();
    }else{
      echo $value;
    }
  }
  $result = mysqli_query($mysqli,"SELECT * FROM flags");
  $status=-2;
  while($row = mysqli_fetch_array($result)){
    var_dump($row);
    if($row['status']!=1&&($current[strtoupper($row['currency'])]>$row['sum']&&$row['operator']=="Up")||($current[strtoupper($row['currency'])]<$row['sum']&&$row['operator']=="Lo")){
      $url='https://api.chatfuel.com/bots/594e6b06e4b0712385fed2eb/users/'.$row['facebook'].'/send?chatfuel_token=mELtlMAHYqR0BvgEiMq8zVek3uYUK3OJMbtyrdNPTrQB9ndV0fM7lWTFZbM4MZvD&chatfuel_block_name=hit'.$row['currency'].'&current_'.$row['currency'].'='.$current[$row['currency']];
      //echo $url;
      $response=\Httpful\Request::post($url)->send();
      echo $response->body;
      $status=1;
    }else{
      $status=0;
    }
    if (!($stmt = $mysqli->prepare("DELETE FROM flags  WHERE id=?"))) {
     echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  $id=$row['id'];
  if (!$stmt->bind_param("i", $id)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  if (!$stmt->execute()) {
      echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
}
}
if($_GET['action']=="myflags"){
  $sql="SELECT * FROM flags WHERE facebook LIKE '".$_GET['facebook']."'";

  $result = mysqli_query($mysqli,$sql);
  $messages=[];
while($row = mysqli_fetch_array($result))
{

  $ul=$row['operator']=="Lo"?" is under ":" is over ";
  $messages[]=chatfuelText("If ".$row['currency'].$ul."than ".$row['sum']." \$");
}
chatfuelSend($messages);
}

 ?>
