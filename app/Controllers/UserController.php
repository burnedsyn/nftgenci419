<?php

namespace App\Controllers;
use App\Libraries\TarotCard;
use DateTime;

ini_set('max_execution_time', 0); // 0 = Unlimited      
date_default_timezone_set('Europe/Brussels');
class UserController extends BaseController
{        
     public $cardCollection=[];   
     public $db;
     /* 
     * InitImage permet de générer les layers a partir des images en ressources
     * 
     * */
    function initImage(string $source, string $format) {
       //on crée les objets images des layers de la scène finale      
        $im = new \Imagick();
        $im->setFormat($format);
        $im->readImage($source);

        return $im;
    }

public function verifScene(array $card, TarotCard $baseCard){
    $config=config('NftConfig');
    $adn='';
    foreach($config->layers as $layer){
        if(is_dir($card[$layer]) || is_null($card[$layer])){
            echo("<hr> Bad layer $layer <br> here : ".$card[$layer]."<br>Restoring $layer<br>");
                $tmppath=$card[$layer];
                while(is_dir($tmppath) || is_null($tmppath)) {
                    if(is_null($tmppath)) $tmppath=$config->ressourcePath.$layer;
                    $card[$layer]=$baseCard->getOne($tmppath);     
                    $tmppath=$card[$layer];
                } //fin while
            echo("layer $layer restored with : ".$card[$layer]."<br>");
                 
        } //fin if is dir or is null

        $adn=$adn.$card[$layer]; 
        
        $card["clearDna"] =$adn;
        
        $card["dna"]=hash('sha512',$adn);
        
    } //fin foreach configlayer
    
    return $card;
}//fin verifScene


    public function index()
    {
       $config=config('NftConfig');
       $baseCard=new TarotCard();
       $count=intval($config->nftCollectionSize);

       for($i=0; $i < $count ; $i++) {
            
        $this->cardCollection[]=$baseCard->getCard();

       }//fin boucle edition size
       $i=0;
       foreach($this->cardCollection as $card){
           //on boucle sur les layer dans la carte
           $adn='';
        
        foreach($config->layers as $layer){
            $tmppath=$card[$layer];
            if(is_dir($tmppath) || is_null($tmppath)) $card[$layer]=$baseCard->getOne($tmppath);          
            $adn=$adn.$card[$layer];

        }
        $this->cardCollection[$i]+=["clearDna" => $adn ];
        $adn=hash('sha512',$adn);
        $this->cardCollection[$i]+=["dna" => $adn];
        $i++;
       }

       $this->db = \Config\Database::connect();
       $builder = $this->db->table('cards');
       $i=0;
       
       $provenanceCumulativeString="";
       
       foreach($this->cardCollection as $card)
       {            
           $images=array();
           $imgformat=$config->imgformat;
           //ici modif verif finale
           $card=$this->verifScene($card,$baseCard);
           $this->cardCollection[$i]=$card;
           $images=array();
           foreach($config->layers as $layer) {

               $images[]= $this->initImage($card[$layer],$imgformat);

           }
           
          
           
           $card["sig"]="0x00";
           $query = $builder->getWhere(['dna' => $card['dna']]);
           
            while ( $query->currentRow > 0 ) {
                d($query);

                echo "<h1>DNA EXIST !!!</h1><hr>";
                $images=null;
                $card=$baseCard->getCard();
                $card=$this->verifScene($card,$baseCard);
                foreach($config->layers as $layer) {

                    $images[]= $this->initImage($card[$layer],$imgformat);
    
                }
                $this->cardCollection[$i]=$card;
                $query = $builder->getWhere(['dna' => $card['dna']]);
                d($query);
                d($card);
                
            } 
                 
        
           if($builder->insert($card)){
               // here we got a unic adn stored in db we create the image in  build/images directory
               echo("added : ".$card['dna']."<hr>");
               //adding dna to cumulative provenance hash
               $provenanceCumulativeString.=$card['dna'];
               $provenanceCumulativeString2=hash('sha512',$provenanceCumulativeString);
               $card+=["provenancestring" => $provenanceCumulativeString2];
               $elem=count($images);
               $baseimage=$images[0];
               for($k=1; $k <=$elem-1; $k++){
                   $baseimage->compositeImage($images[$k],$images[$k]->getImageCompose(), 0, 0 );
               }
               
               $pathtoimg=$config->buildPath."images/";
                
               $baseimage->writeImage($pathtoimg.$i.".png");
               $card["sig"]=hash_file('sha512', $pathtoimg.$i.".png");
               $card["creationDate"]=date("Y-m-d H:i:s");
               $card["imagePath"]="/build/images/".$i.".png";
               $this->cardCollection[$i]=$card;
               $i++;
               continue;
          }else{
            echo("<hr>ICI ERROR<hr>");
            echo $this->db->error();
            continue;
          } 
          ;
       }
        $data['cumulativeString']=$provenanceCumulativeString;
        $data['provenanceCumulativeHash']=hash("sha512",$provenanceCumulativeString);
        $data['sitename']= $config->siteName;
        $data['cardCollection']=$this->cardCollection;
        return view('Views/user', $data);
        //return view('Views/user');
    }

    
}
