<?php
 include "data/lib.php";
 $_User_ip = _Ip();
 if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = '';
 if(empty($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'Unknown';
 $user_agent = $_SERVER["HTTP_USER_AGENT"];
 $user_arr   = _Detect_browser ($user_agent);

// имя броузера
 $_BName      = $user_arr[0];

// $content равно пусто
  $content = '';

// если браузер Safari, Firefox, Opera или Robot выводим <object
  if($_BName=='Safari' or $_BName=='Firefox' or $_BName=='Opera' or $_BName=="Robot"){
    // добавляем в переменную $content <script 
    $content .= "
<script type=\"text/javascript\">
   function xflashe(s) {
       var out  = '<object type=\"application/x-shockwave-flash\" data=\"data/audio_player.swf\" height=\"25\" width=\"150\">';
       out += '<param name=\"FlashVars\" value=\"autostart=yes&soundFile='+s+'\">';
       out += '</object><br>';
       return out;
      };";
   }
// для всех других выводим <EMBED 
  else{
    // добавляем в переменную $content <script 
    $content .= "
    <script type=\"text/javascript\">
      function xflashe(s) { return '<EMBED SRC=\"'+s+'\" height=\"25\" width=\"150\" autostart=\"true\"><br>';  };";
   }

  // добавляем в переменную $content function X_playSound
  $content .= "
    // функция проиграть звуки
  function X_playSound(c) {
      var text;
      if (c==1){
          text = '<a href=\"javascript:X_playSound(0);\">close</a><br>';
          // добавялем в переменную текст проиграть три звука в проигрывателе
          text += xflashe(\"data/fire.mp3\");
          text += xflashe(\"data/rain.mp3\");
          text += xflashe(\"data/4_00.mp3\");
        }
      else if (c==2){
          text = '<a href=\"javascript:X_playSound(0);\">close</a><br>';
          // добавялем в переменную текст проиграть три звука в проигрывателе
          text += xflashe(\"data/fire.mp3\");
          text += xflashe(\"data/rain.mp3\");
          text += xflashe(\"data/4_01.mp3\");
        }
      else { text = ''; }
      var X_showdiv = document.getElementById(\"X_showdiv\");
      X_showdiv.innerHTML = text;
    };
  </script>

<a href=\"javascript:X_playSound(1);\">sound 1</a>
<a href=\"javascript:X_playSound(2);\">sound 2</a>
<div id=\"X_showdiv\"></div>
";
  print $content;
 
?>