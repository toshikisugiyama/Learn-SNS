<?php 

  //関数演習問題をここに回答しましょう。
echo "<br>"."演習問題１"."<br>";
function multiplication($num1,$num2){
  echo $num1*$num2;
}
multiplication(3,4);

echo "<br>"."演習問題2"."<br>";

function average($num3,$num4){
  $average = ($num3 + $num4) / 2;
  if ($average >= 10) {
    return $average;
  }else{
    return 0;
  }
}
  echo average(2,6);

echo "<br>"."演習問題3"."<br>";

function shopping($bringing,$purchase){
  $remainder = $bringing-$purchase;
  if ($remainder>=0) {
    return $remainder;
  }else{
    echo "足りません";
  }
}
echo shopping(1000,200);

echo "<br>"."演習問題4"."<br>";
function result($num5,$num6,$result){
  if ($num5>=$num6) {
    return $result =$num5;
  }else{
    return $result = $num6;
  }
}
echo result(3,9,0);

 ?>