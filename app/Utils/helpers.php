<?php
  
function dateTimeFormat($date){
    return \Carbon\Carbon::parse($date)->format('d M Y - g:i A');
}

function dateFormat($date){
  return \Carbon\Carbon::parse($date)->format('d M Y');
}

function dateTimeSlashFormat($date){
  return \Carbon\Carbon::parse($date)->format('d/m/Y g:i A');
}

function dateRangeFormFormat($startDate, $finishDate){
  $dateConvert = dateTimeSlashFormat($startDate).' - '.dateTimeSlashFormat($finishDate);
  return $dateConvert;
}

function trimString($string, $repl, $limit) 
{
  if(strlen($string) > $limit) 
  {
    return substr($string, 0, $limit) . $repl; 
  }
  else 
  {
    return $string;
  }
}

function sluggify($string,$separator = '-')
{
   return strtolower(str_replace(" ",$separator,$string));
}

function generateUuid()
{
   return Illuminate\Support\Str::uuid();
}

function numberSpacer($str, $separator = ' ', $spatial = 4) {
  return wordwrap($str, $spatial, $separator, true);
}

function formatAmount($str, $separator = '.') {
  return 'Rp'.number_format($str,0,".",".");
}

function removeExceptNumber($str) {
  return preg_replace("/[^0-9]/","",$str);
}

function numberClearence($str) {
  return intval(removeExceptNumber($str));
}

function generateCode($prefix, $suffix = '-') {
  $characters = '0123456789';
  $suffix = $suffix === '-' ? substr(str_shuffle($characters), 0, 3) : $suffix;
  $date = \Carbon\Carbon::now()->format('ymdHi');
  return $prefix.$date.$suffix;
}

function dateTimeRangeFormatToSave($date){
  return \Carbon\Carbon::createFromFormat('d/m/Y g:i A', trim($date))->toDateTimeString();
}

function genrateLetterNumber($title,$number) {
  $monthletterformat = array('','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
  $year = date('Y');
  $month = $monthletterformat[date('n')];
  $setnumber = str_pad($number, 5, '0', STR_PAD_LEFT);
  $result = $title.'-J99/'.$year.'/'.$month.'/'.$setnumber;

  return $result;
}

function console_log($output, $with_script_tags = true) {
  $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .');';
  if ($with_script_tags) {
  $js_code = '<script>' . $js_code . '</script>';
  }
  echo $js_code;
}

function formatPhone($nomorhp) {
  $nomorhp = trim($nomorhp);
  $nomorhp = strip_tags($nomorhp);     
  $nomorhp = str_replace(" ","",$nomorhp);
  $nomorhp = str_replace("(","",$nomorhp);
  $nomorhp = str_replace(")","",$nomorhp);
  $nomorhp = str_replace(".","",$nomorhp); 

  if(!preg_match('/[^+0-9]/',trim($nomorhp))){
      if(substr(trim($nomorhp), 0, 3)=='+62'){
          $nomorhp= trim($nomorhp);
      }
      else if(substr(trim($nomorhp), 0, 2)=='62'){
          $nomorhp= '+'.$nomorhp;
      }
      else if(substr($nomorhp, 0, 1)=='0'){
          $nomorhp= '+62'.substr($nomorhp, 1);
      }
  }
  return $nomorhp;
}
