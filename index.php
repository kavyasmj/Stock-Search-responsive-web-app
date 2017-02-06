<?php

header("Access-Control-Allow-Origin: *");

date_default_timezone_set('UTC');            
$tz = new DateTimeZone('America/Los_Angeles'); 

if(isset($_GET["symb"])) { 

                    $accountKey = 'V/YUj1lLxbkoXauYoL+uTPuLXBlzQMpE8HLvE5VVxz4';                    
                    $WebSearchURL = 'https://api.datamarket.azure.com/Bing/Search/v1/News?$format=json&Query=';
                    
                    $context = stream_context_create(array(
                        'http' => array(
                            'request_fulluri' => true,
                            'header'  => "Authorization: Basic " . base64_encode($accountKey . ":" . $accountKey)
                        )
                    ));

                    $request = $WebSearchURL . urlencode( '\'' . $_GET["symb"] . '\'');                     
                    $response = file_get_contents($request, 0, $context);
                    $jsonobj = json_decode($response);
    
                    $res_news = array();
                    foreach($jsonobj->d->results as $value)
                    {                        
                        // begin of date logic
                        $var1 = gmdate('Y-m-d H:i:s', strtotime($value->Date));
                        $dt1 = new DateTime($var1);            
                        $dt1->setTimezone($tz);
                        $date = $dt1->format('d F Y, H:i:s');
                        //end of date logic
                        
                        $res_news[] = array(
                            'Title'           => $value->Title, 
                            'URL'             => $value->Url, 
                            'Description'     => $value->Description, 
                            'Source'          => $value->Source, 
                            'Date'            => $date);
                    }
                    
                    if(isset($res_news)){
                           echo json_encode($res_news); 
                        }
                    else{
                        echo "ERROR"; 
                    }
                    exit;

}

   if(isset($_GET["sym"])) {   
       
        $quote_url = 'http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=' .  $_GET["sym"]; 
        $data = file_get_contents($quote_url); 
 
        $content = json_decode($data, true);
       
        $stat = strtoupper($content['Status']); 

        if($content['Status'] !== "SUCCESS"){
            echo "ERROR";
            exit;
        }
        
        $nam = $content['Name'];
        $symbol = $content['Symbol'];

        $lp = '$'. number_format($content['LastPrice'],2);
        $ch = number_format($content['Change'],2);
        $cp = number_format($content['ChangePercent'],2);
        
        $chp_str = $ch. "(" . $cp . "%)"; 

        $ts = $content['Timestamp'];

        $mc = $content['MarketCap'];
        if($content['MarketCap'] < 10000000 && $content['MarketCap'] > 100000){
             $mc = number_format(($content['MarketCap']/1000000),2) .' Million';
        }
        else if($content['MarketCap'] > 10000000) {
            $mc = number_format(($content['MarketCap']/1000000000),2) .' Billion'; 
        }
       
//        $vol = number_format($content['Volume'],0,".",",");
        $vol = $content['Volume'];
       
        $cytd = number_format($content['ChangeYTD'],2); 
        $cptyd = number_format(($content['ChangePercentYTD']),2);
        
        $chp_ytd_str = $cytd. "(" . $cptyd . "%)"; 

        $high = '$'. number_format($content['High'],2);
        $low  = '$'. number_format($content['Low'],2);
        $open = '$'. number_format($content['Open'],2);
        
        $res_arr = array('Status'           => $stat, 
                         'Name'             => $nam, 
                         'Symbol'           => $symbol, 
                         'LastPrice'        => $lp, 
                         'Change'           => $ch, 
                         'ChangePercent'    => $cp, 
                         'Str'              => $chp_str, 
                         'Timestamp'        => $ts, 
                         'MarketCap'        => $mc, 
                         'Volume'           => $vol, 
                         'ChangeYTD'        => $cytd, 
                         'ChangePercentYTD' => $cptyd, 
                         'YTDStr'           => $chp_ytd_str, 
                         'High'             => $high,
                         'Low'              => $low,
                         'Open'             => $open);

       echo json_encode($res_arr);

   }
 ?>
