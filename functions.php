<?php

function chatfuelSend($messages){
echo '  {"messages": '  ;
  echo json_encode($messages);
  echo'}';
}
function chatfuelText($text){
  return ['text'=>$text];
}
function chatfuelImage($url){
  return ['attachment'=>['type'=>'image','payload'=>['url'=>$url]]];
}


 ?>
