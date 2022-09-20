<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class NftConfig extends BaseConfig
{
    public $siteName  = '';
    public $siteEmail = '';
    public $ressourcePath = APPPATH.'ressources/';
    public $layers=["Background","Border","Card"];
    public $imgformat= 'png';
    public $buildPath='/var/www/html/build/';
    public $nftCollectionSize=0;



}
