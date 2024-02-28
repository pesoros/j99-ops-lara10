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

function sluggify($string)
{
   return strtolower(str_replace(" ","-",$string));
}

function generateUuid()
{
   return Illuminate\Support\Str::uuid();
}

function numberSpacer($str, $separator = ' ') {
  return wordwrap($str, 4, $separator, true);
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