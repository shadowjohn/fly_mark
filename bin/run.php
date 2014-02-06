<?php
  $PWD=dirname(__FILE__);
  $SOURCE_DIR="{$PWD}/../source";
  $OUTPUT_DIR="{$PWD}/../output";
  $font="{$PWD}/msjhbd.ttf";
  $fly_mark = "僅供辦理逢甲大學GIS中心2014員工海外旅遊使用.";
  $fly_mark = str_repeat($fly_mark,50);    
  function globr($sDir, $sPattern, $nFlags = NULL)
  {
    //$sDir = escapeshellcmd($sDir);
    // Get the list of all matching files currently in the
    // directory.
    $aFiles = glob("$sDir/$sPattern", $nFlags);
    // Then get a list of all directories in this directory, and
    // run ourselves on the resulting array.  This is the
    // recursion step, which will not execute if there are no
    // directories.
    foreach (glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir)
    {
      $aSubFiles = globr($sSubDir, $sPattern, $nFlags);
      $aFiles = array_merge($aFiles, $aSubFiles);
    }
    // The array we return contains the files we found, and the
    // files all of our children found.
    return $aFiles;
  }
  function mainname($fname){
    $pathinfo=pathinfo($fname);
    return $pathinfo['filename'];           
  }  
  $image_filter="png,jpg,tif,pdf";
  $f = globr($SOURCE_DIR,"*.*");
    
  natcasesort($f);
  $f=array_values($f);
  for($i=0,$max_i=count($f);$i<$max_i;$i++)
  {
    
    $dn=dirname($f[$i]);    
    $m_d = explode("/",$dn);
    $d = end($m_d);
    
    //$d = addslashes($d);
    //$d = mb_convert_encoding($d,'UTF-8','BIG5');
    
    @mkdir("{$OUTPUT_DIR}/{$d}",0777);
    $mn = mainname($f[$i]);    
    //$mn = mb_convert_encoding($mn,'UTF-8','BIG5');
    if(strtoupper($mn)=='THUMBS.DB'||strtoupper($mn)=='THUMBS.PNG')
    {
      continue;
    }    
    $on = "{$OUTPUT_DIR}/{$d}/{$mn}.png";        
    `/usr/bin/convert -density 300 -resize 2048x2048 "{$f[$i]}" "{$on}"`;    
    //浮水印咧~        
    $data = file_get_contents($on);    
    list($w,$h)=getimagesize($on);
    $im = imagecreatefromstring($data);
    $ttlr=imagecolorallocatealpha($im,0,0,0,85); //字型顏色設定
    for($j=0;$j<$h;$j+=$h/10)
    {
      ImageTTFText ($im, 50, 5, 20, $j, $ttlr, $font ,$fly_mark);
    }                    
     
    imagepng($im, $on);
    imagedestroy($im);
    echo sprintf("%d / %d\n",($i+1),$max_i); 
  }
  echo sprintf("%d / %d\n",($i+1),$max_i);
  echo "Done.";