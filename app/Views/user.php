<H1>Hello User</H1>

<?php    
echo('thank to visiting <h1>'.$sitename.'</h1>');

echo("<h2>Collection informations</h2>");?>
<h3> Provenance Cumulative String</h3>
<p>please save this information</p>
<textarea id="provenanceCumulativeString" name="provenanceCumulativeString" rows="5" cols="127">
<?php 
echo($cumulativeString);
?>
</textarea>
<div>
    Recorded cumulative string hash : <?php echo ("$provenanceCumulativeHash");?>
</div>
<hr>
<H2>Collection </H2>
<?php 

foreach($cardCollection as $card){ 

?>
<div class="row">
<div class="left">
    <?php 
    foreach($card as $key=>$value){
        echo <<< EOF
        
            [$key] => $value <br>
            
EOF; ?>        <?php } ?>   
</div>

<div class="right"> <a href="<?php echo $card['imagePath']; ?>" target="_blank"><img src="<?php echo $card['imagePath']; ?>" width="10%" ></a></div>


</div>
<hr>

<?php
}

?>




