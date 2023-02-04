<?php
 $base_robot         = 'data/base_robot.dat';
 $base_system        = 'data/base_system.dat';
 $base_browser       = 'data/base_browser.dat';
 $base_for_search_ip = 'data/base_for_ip.dat';
 $base_mobile        = 'data/base_mobile.dat';

 $base_for_search_robot   = base_for_search ($base_robot);
 $base_for_search_system  = base_for_search ($base_system);
 $base_for_search_browser = base_for_search ($base_browser);
 $base_for_search_ip      = base_for_search ($base_for_search_ip);
 $base_for_search_mobile  = base_for_search ($base_mobile);
 $base_for_search_mobile  = str_replace('\^','^',substr ($base_for_search_mobile,0,-2));
 $base_for_search_mobile  = str_replace('176x220','[0-9]{3}x[0-9]{3}',$base_for_search_mobile);
 $base_for_search_mobile  = $base_for_search_mobile."[ /-]?([a-z0-9]*)[ ]?[/]?[ ]?([a-zA-Z]*)(.*)~i";

 function base_for_search ($path){       
    $path = str_replace ("\r","", @trim (@implode("",@file($path))));
    if (strlen ($path) < 1){ return false; }
    return "~(".str_replace ("\n","|", preg_quote($path,"~")).")~i";  
   }
  function _Detect_browser($path1='',$path=''){
       global $base_for_search_browser, 
              $base_for_search_system, 
              $base_for_search_robot, 
              $base_for_search_ip, 
              $base_for_search_mobile, 
              $_User_ip;
       $BName=$Brobot=$BVersion=$BPlatform='';
       if(empty($path)){$path = _Replace_Bad_simbol ($_SERVER['HTTP_USER_AGENT']); }  
       $path = preg_replace("~[\(;\)]+~",'',$path);
       $path = preg_replace("~[ ]+~"," ",$path);       
       if(empty($base_for_search_ip)){$base_for_search_ip='~1\.1\.1\.1~s';} 
       if(preg_match($base_for_search_ip,$_User_ip)){
         $path = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:0.9.2) Gecko/20010726 Netscape/10.0";       
         }
       if(preg_match($base_for_search_mobile,$path,$match)){
          $BName     = ucfirst($match[1]);
          if(preg_match("~^mot$~i",$BName)){ $BName = "Motorola";}
          if(preg_match("~^sie$~i",$BName)){ $BName = "Siemens";}
          if(preg_match("~^nokia|pcl$~i",$BName) and strlen($match[2])>4){$match[2] = substr ($match[2],0,4); }
          $BVersion  = $match[2];
          $BPlatform = 'MobilePhone';
          $Brobot    = 'Browser';  
         }
       if(empty($BName)){
          if(preg_match($base_for_search_robot, $path, $arr_r)){
            $Brobot = ucfirst($arr_r[0]); 
            $BName     = 'Robot';
            $BVersion  = 'non';                                                         
            $BPlatform = 'non';  
            }
         }
       if(empty($BName)){
          $base_for_search_browser = trim (str_replace (")~i", "", $base_for_search_browser));
          if(preg_match($base_for_search_browser.")[0-9]{0,1}[ ]?[-]?[/]?[ ]?[v]?([0-9]{0,3}[\.]?[0-9]{0,1})~i",$path,$match)){
             $BName = ucfirst($match[1]); 
             $BVersion = $match[2];
             }
          elseif(preg_match("~ms[ ]?ie[ ]?([0-9]{0,1}[\.]?[0-9]{0,1})~i", $path, $match)){
             $BName    = "Explorer";     
             $BVersion = str_replace(' .','',$match[1]);
            }
          elseif(preg_match("~mozilla[0-9]{0,1}[ ]?[/]?[ ]?([0-9]{0,3}[\.]?[0-9]{0,1})~i",$path,$match)){
             $BName = "Mozilla"; 
             $BVersion = $match[1];
             }
          if(empty($BName)){$BName = 'Unknown'; $BVersion='Unknown';} 
         }
       if($BPlatform != 'MobilePhone'){ 
          if(preg_match("~win[dows]{0,4}[ ]{0,1}(nt|xp|me|ce|9x|95|98|2000|32|3\.1)[ ]?[0-9]{0,1}[\.]?[0-9]{0,1}~i",$path,$matches)){
             $matches[0] = preg_replace("~[ ]+~i",'',trim($matches[0]));
             $matches[0] = preg_replace("~dows~i",'',$matches[0]);
             if(preg_match("~win(9x|98)~i",$matches[0])){$matches[0]='Win32';}
             $BPlatform = $matches[0];
           }
          elseif(preg_match("~windows~i",$path)){ $BPlatform = 'Win32';  }
          elseif(preg_match($base_for_search_system,$path,$matches)){  
             if(preg_match("~mac~i",$matches[0])){$matches[0]='MacOS';}
             $BPlatform = $matches[0]; 
             }
          if(empty($BPlatform)){$BPlatform = 'Unknown';} 
        }
       if($BName=='Unknown' and empty($Brobot)){
         if(preg_match($base_for_search_robot, $path1, $arr_r)){
           $Brobot = ucfirst($arr_r[0]); 
           $BName     = 'Robot';
           $BVersion  = 'non';                                                         
           $BPlatform = 'non';  
          }
         }
       if($BName!='Robot' and $BVersion!='Unknown' and $BPlatform!='Unknown'){$Brobot = 'Browser';}
       else{ $BVersion='non'; $BPlatform='non'; $BName='Robot'; if(empty($Brobot)){$Brobot='Unknown';} }

       if($BName!='Robot' and $BPlatform!='MobilePhone' and
         !preg_match($base_for_search_robot, $_SERVER['HTTP_REFERER']) and 
         !preg_match($base_for_search_ip,$_User_ip)){
          $_array = array();
          if(!empty($_SERVER['HTTP_ACCEPT']))         { $_array[] = 1; } else $_SERVER['HTTP_ACCEPT']='';
          if(!empty($_SERVER['HTTP_ACCEPT_ENCODING'])){ $_array[] = 2; } else $_SERVER['HTTP_ACCEPT_ENCODING']='';
          if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){ $_array[] = 3; } else $_SERVER['HTTP_ACCEPT_LANGUAGE']='';
          if(!empty($_SERVER['HTTP_CONNECTION']))     { $_array[] = 4; } else $_SERVER['HTTP_CONNECTION']='';
          if(count($_array) < 3){
             $BVersion  = "bad; ACCEPT="._Replace_Bad_simbol ($_SERVER['HTTP_ACCEPT']).
                          "; ENCODING="._Replace_Bad_simbol ($_SERVER['HTTP_ACCEPT_ENCODING']).
                          "; LANGUAGE="._Replace_Bad_simbol ($_SERVER['HTTP_ACCEPT_LANGUAGE']).
                          "; CONNECTION="._Replace_Bad_simbol ($_SERVER['HTTP_CONNECTION']); 
             }                                   
        }
   # print $BName." ".$BPlatform." ".$BVersion." ".$Brobot."<br>";
    return array($BName,$BVersion,$BPlatform,$Brobot);
   }
  function _Replace_Bad_simbol ($path){
     $path = @trim ( preg_replace ("/[^\x20-\xFF]/","", @strval ($path)));
     $path = str_replace ("'",   ''   ,$path);
     $path = str_replace ('"',   ''   ,$path);
     $path = str_replace ('<',   '&lt;',$path);
     $path = str_replace ('>',   '&gt;',$path);
     $path = _Replace_Bad_simbol_for_user_agent ($path);
    return trim ( preg_replace ("~[ ]+~s"," ",$path) );
   }
  function _Replace_Bad_simbol_for_user_agent ($path){
     return preg_replace ("~Yandex Browser;|GoogleToolbar|GoogleT5;|Google Wireless Transcoder~","",$path);
   }
  function _Ip(){
     if(getenv('REMOTE_ADDR'))               { $_User_ip = getenv('REMOTE_ADDR');          }
      elseif(getenv('HTTP_FORWARDED_FOR'))   { $_User_ip = getenv('HTTP_FORWARDED_FOR');   } 
      elseif(getenv('HTTP_X_FORWARDED_FOR')) { $_User_ip = getenv('HTTP_X_FORWARDED_FOR'); } 
      elseif(getenv('HTTP_X_COMING_FROM'))   { $_User_ip = getenv('HTTP_X_COMING_FROM');   }  
      elseif(getenv('HTTP_VIA'))             { $_User_ip = getenv('HTTP_VIA');             } 
      elseif(getenv('HTTP_XROXY_CONNECTION')){ $_User_ip = getenv('HTTP_XROXY_CONNECTION');} 
      elseif(getenv('HTTP_CLIENT_IP'))       { $_User_ip = getenv('HTTP_CLIENT_IP');       } 
     else{$_User_ip='unknown';}
     if(preg_match('/[a-zA-Zà-ÿÀ-ß]/', $_User_ip)){$_User_ip = 'unknown';}
     return $_User_ip;
   }                      
?>
