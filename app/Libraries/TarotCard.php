<?php
namespace App\Libraries;

use CodeIgniter\Publisher\Exceptions\PublisherException;
use PhpParser\Node\Stmt\Else_;

use function PHPUnit\Framework\isEmpty;

defined('APPPATH') OR exit('No direct script access allowed');
helper('filesystem');


class TarotCard {
    
public $layer;

public $config;
public $ressourcePath;

    public function __construct () {
        $this->config=config('NftConfig');
        $this->ressourcePath=$this->config->ressourcePath;
       
    } // fin construct

    public function getOne($ressourcesPath){
        //donne un élément de layer un élément unique et avec la rareté
       
    
        if (is_dir($ressourcesPath)){
            $layer_info = get_dir_file_info($ressourcesPath);

        }// ressource path est un repertoire.

        $currentProb=random_int(0,100);
    
        foreach($layer_info as $currentDir => $key ){
            $test = print_r($currentDir,true);
            $currentDirProb=explode("#", $test);
            $currentDirRarity=$currentDirProb[1];
            $currentProb -= intval($currentDirRarity);
            if ($currentProb <1){
            $relative_path=print_r($key['relative_path'],true);
            
           
           $testingPath=$relative_path."/".$test;

           return $testingPath;
           
             
             break;
        }//fin if
            else {
                continue;
            }
        }//fin foreach
              
    }
    public function getCard() {

        /*
        ** ici on selectionne les éléments de l'image finale on compile l'image
        */

        $card=[];

        foreach($this->config->layers as $currentLayer) {
            $pathtoscan=$this->ressourcePath.$currentLayer;
            $test=$this->getOne($pathtoscan);
            
            if(is_dir($test)) {
               
                $test=$this->getOne($test);
                
                
               
            } //$test is dir
            
            if(is_file($test)){
               
                $card[$currentLayer]=$test;
                
            }

            
        } // fin foreach layer
        

       foreach($this->config->layers as $veriflayer){
        
          if(isset($card[$veriflayer]) && $card[$veriflayer] !=null) {
            //if(is_dir($this->ressourcePath.$veriflayer)) $card[$veriflayer]=$this->getOne($this->ressourcePath.$veriflayer);
            continue; }//fin if veriflayer
           else {
               $card[$veriflayer]=$this->getOne($this->ressourcePath.$veriflayer);
               
           }

        } // veriflayer
        
        
        


       





        return $card;
       /*  if(!is_null($card))
        return $card;
        else $card=$this->getCard();
        */
    }//fin getCard


} // fin Class