<?php
define("API_KEY","1905kalem");
class warehouse implements JsonSerializable{
    public string $id;
    public string $name;
    public int $order;
    public bool $visible;
    function __construct($id,$name,$order,$visible)
    {
        $this->id=$id;
        $this->name=$name;
        $this->order=$order;
        $this->visible=$visible;
    }
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' =>$this->order,
            'visible' => $this->visible
        ];
    }
}
class warehouse_info{
    public int $revision;
    public array $collection;
    public function __construct($revision,$collection){
        $this->revision=$revision;
        $this->collection=$collection;
    }
}
$warehouse_central=new warehouse("1","Sanayi",1,true);
$warehouse_kardeskoy=new warehouse("2","Kardeşköy",2,true);
$warehouse_3=new warehouse("3","3",3,false);
$warehouse_4=new warehouse("4","4",4,false);
$warehouse_5=new warehouse("5","5",5,false);
$warehouse_6=new warehouse("6","6",6,false);
$warehouse_total = new warehouse("0","Toplam",3,true);
$warehouses =array();
array_push($warehouses,$warehouse_central);
array_push($warehouses,$warehouse_kardeskoy);
array_push($warehouses,$warehouse_3);
array_push($warehouses,$warehouse_4);
array_push($warehouses,$warehouse_5);
array_push($warehouses,$warehouse_6);
array_push($warehouses,$warehouse_total);
$warehouse_info=new warehouse_info(1,$warehouses);
?>