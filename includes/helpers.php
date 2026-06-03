<?php 
 function h($texto) { 
     return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'); 
 } 
 
 function limpar($texto) { 
     return trim(strip_tags($texto)); 
 } 
 
 function redirecionar($url) { 
     header('Location: ' . $url); 
     exit; 
 } 
 
 function calcular_noites($data_inicio, $data_fim) { 
     $inicio = new DateTime($data_inicio); 
     $fim = new DateTime($data_fim); 
     return $inicio->diff($fim)->days; 
 } 
 
 function calcular_total($noites, $num_hospedes, $capacidade_base, $preco_diaria, $preco_extra, $preco_pa, $pequeno_almoco) { 
     $total = $noites * $preco_diaria; 
     if ($num_hospedes > $capacidade_base) { 
         $total += ($num_hospedes - $capacidade_base) * $preco_extra * $noites; 
     } 
     if ($pequeno_almoco) { 
         $total += $num_hospedes * $preco_pa * $noites; 
     } 
     return $total; 
 } 
 ?>