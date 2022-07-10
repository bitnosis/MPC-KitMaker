<?php 

namespace App\Imports;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TradeImport implements ToCollection
{
    
    private $columnArray = [

    ];

    private $headingKey = 0;

    private $validArray = ["account", "buysell", "buy", "sell", "long","short","price","qtytofill", "qtyfilled", "qtyworking","pricetype", "symbol", "ordernumber","order","limitprice","limit","exit","entry","ticker","contracts","lots","shares","duration","orderduration","avgfill","avgfillprice","orderupdatetime","time","timestamp","createtime","updatetime","createdate","updatedate"];


    public function cleanName($value){
        return strtolower(str_replace(["/","\\","'",","," "],'',$value));
    }
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $key=>$row) 
        {  
            if($row[0]!="" && $row[0]!="Working Orders" && $row[0]!="Completed Orders"){
                for($c=0; $c < count($row); $c++){
                    if (in_array($this->cleanName($row[$c]), $this->validArray)){
                        $this->headingKey = $key;
                        $this->columnArray[$this->cleanName($row[$c])] = ["name"=>$this->cleanName($row[$c]), "key"=>$c];
                    }
                }
            }
            
        }


        foreach ($rows as $k=>$row) 
        {   
            if($k>$this->headingKey){

                // Check if trade with this timestamp exists.
                $check = Trade::where('entered_trade', $row[$this->columnArray["createtime"]["key"]])->first();
                if(!$check){
                    $trade = New Trade;
                    $trade->user()->associate(auth()->user());
                    if(isset($this->columnArray["price"])){
                        $trade->price = $row[$this->columnArray["price"]["key"]];
                    }
    
                    if(isset($this->columnArray["limitprice"])){
                        $trade->price = $row[$this->columnArray["limitprice"]["key"]];
                    }
    
                    if(isset($this->columnArray["avgfillprice"])){
                        $trade->price = $row[$this->columnArray["avgfillprice"]["key"]];
                    }
                    
                    $trade->direction = $row[$this->columnArray["buysell"]["key"]];
                    $trade->amount = $row[$this->columnArray["qtytofill"]["key"]];
                    $trade->transaction_id = $row[$this->columnArray["ordernumber"]["key"]];
    
                    if(isset($this->columnArray["pricetype"])){
                        if(strtolower($row[$this->columnArray["pricetype"]["key"]]) ==="l" || strtolower($row[$this->columnArray["pricetype"]["key"]])==="limit" ){
                            $trade->price_type = "limit";
                        }
    
                        if(strtolower($row[$this->columnArray["pricetype"]["key"]]) ==="m" || strtolower($row[$this->columnArray["pricetype"]["key"]])==="market" ){
                            $trade->price_type = "market";
                        }
                    }
                   
                    // Todo, lookup symbol and get market
                    $trade->market_type = "futures";
                    // Todo lookup symbol and get commission
                    $trade->commission = 1.99;
                    $trade->status = "processing";
                    $trade->entered_trade = $row[$this->columnArray["createtime"]["key"]];
                    $trade->symbol = $row[$this->columnArray["symbol"]["key"]];
                    $trade->save();
                }
            }
        }

        $trades = Trade::where('user_id', auth()->user()->id)
        ->where('status','processing')
        ->orderBy('entered_trade')->get();

        $open_trades = [];

        if($trades){
            foreach($trades as $key=>$val){
                if(isset($open_trades[strtolower($val->symbol)])){
                    // Check if it closes, or opens
                    if($open_trades[strtolower($val->symbol)]["side"]!=$val->direction){
                        // close the trade, or deduct
                        $val->parent_id = $open_trades[strtolower($val->symbol)]["id"];
                        $open_trades[strtolower($val->symbol)]["amt"]-=$val->amount;
                        if($open_trades[strtolower($val->symbol)]["amt"]<=0){
                            $t = Trade::find($open_trades[strtolower($val->symbol)]["id"]);
                            if($t){
                                if($t->direction=="B"){
                                    $val->profit_loss = ($val->price-$t->price);
                                } else {
                                    $val->profit_loss = $t->price-$val->price;
                                }
                                $val->profit_loss_dollar = Trade::getSymbolTickValue(strtolower($val->symbol), $val->profit_loss);
                                $t->status = "completed";
                                $t->save();
                            }
                            unset($open_trades[strtolower($val->symbol)]);
                        }
                        $val->status = "completed";
                        $val->save();
                    }

                } else {
                    $open_trades[strtolower($val->symbol)] = ["id"=>$val->id, "amt"=>$val->amount, "side"=>$val->direction];
                }
            }
        }

        print_r($trades);




    }


}
