<?php
  session_start();
  header("Content-type: text/html; charset=utf-8");
  
  require_once 'Nfse.php';
  $Listar = new Nfse();
  
  $Cnpj = '15454962000198';
  $InscricaoMunicipal = '32106042';
  $CodigoMunicipioEmpresa = '4313300';
  $RazaoSocial = 'Teste Ltda - ME';
  $Valorservico = '110.00';
  
  //Criar a nfse nova
  
  echo '<a href="envio.php">Gerar Nova NFSE</a><br>';
 
  foreach($Listar->listNotas() as $X){ 
    $numeronota = $X["numeronota"];
	$linknota = $X["linknota"];
	echo '<br>Numero Nota: '.$X["numeronota"].' - <a href="cancelarnota.php?nrNota='.$numeronota.'">Cancelar Nota</a>';  
    echo '<br>Protocolo: '.$X['protocolo'];
    echo '<br>Codigo Verificação: '.$X['codigoverificacao'];	
	echo '<br>Link Nota: <a href="'.$linknota.'" target="_blank">Ver Nota</a><br>';  
  }
  
?>
