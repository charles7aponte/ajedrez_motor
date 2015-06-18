<?php
        session_start();
        
        $moviento="";
        $mover="";
        $board="";// contendra el tablero
        

        $resultado=array(
            "resultado"=>'error'
            ,"movimiento"=>''
            ,'fen'=>''
         
        );
        
        
        
     ///parametros 
     if(isset($_REQUEST['accion']))   
        switch ($_REQUEST['accion'])
        {
            //CERRAR JUEGO 
            case "cerrar":
               session_destroy();
               header("Location: index.php");
               exit;
            break;
            
            
            //MOVER FICHA
            case "mover":
                if(isset($_REQUEST['move']))
                {

                 $mover= $_REQUEST['move'];  
                
              

                   if(isset($_SESSION['sesion']))       
                    {

                        $descriptorspec = array(
                        0 => array("pipe", "r"), 
                        1 => array("pipe", "w"), 
                        2 => array("pipe", "r")
                        );

                       
                          
                        $process = proc_open('/usr/games/stockfish', $descriptorspec, $pipes, null, null); //run test_gen.php

                        if (is_resource($process)) 
                        {

                            $moviento=$_SESSION['moves_ant']." ".$mover;


                            fwrite($pipes[0], "setoption name Skill Level value ".$_SESSION['level']."\n");    // send start
                            fwrite($pipes[0], "setoption name MultiPV  value ".$_SESSION['multipv']."\n");    // send start
                            fwrite($pipes[0], "setoption name Threads value ".$_SESSION['thread']."\n");    // send start
                            fwrite($pipes[0], "setoption name Aggressiveness value ".$_SESSION['agre']."\n");    // send start
                            fwrite($pipes[0], "setoption name Best Book Move value ".$_SESSION['book']."\n");    // send start
                                                                                                               

                            fwrite($pipes[0], "'ucinewgame\n");    // send start


                            fwrite($pipes[0], "position startpos moves {$moviento}\n");    // send start


                            //BUSCA EL FEN 
                            fwrite($pipes[0], "d\n");    //                  

                            ///busca en un maximo de 1000
                            $contador=0;
                            do
                            {
                              $fila=fgets($pipes[1]);    //get answer
                              $contador++;
                              if($contador>1000)
                              {
                                  fclose($pipes[0]);
                                  fclose($pipes[1]);
                                  fclose($pipes[2]);
                                
                                  
                                  
                                  exit;
                              }
                            }while(strpos($fila, "Fen is")===false);


                            $Fen=substr($fila,8);

                            // compara los el nuevo Fen con el anterior
                            if($Fen==$_SESSION['fen'])
                            {
                                //echo "<label style='color:red'>Error en el moviento <b>({$mover}) </b></label><br>";
                                $moviento=$_SESSION['moves_ant'];
                                
                            }
                            else {

                                // SOLICITA EL MEJOR MOVIENTO AL MOTOR
                                fwrite($pipes[0], "go movetime ".$_SESSION['time']." mate\n");    // la mejor opcion                 

                                ///busca en un maximo de 1000
                                $contador=0;
                                do
                                {
                                  $fila=fgets($pipes[1]);    //get answer
                                  $contador++;
                                  if($contador>1000)
                                  {
                                      fclose($pipes[0]);
                                      fclose($pipes[1]);
                                      fclose($pipes[2]);
                                      exit;
                                  }
                                }while(strpos($fila, "bestmove")===false);


                                $moveMaq=  substr($fila,9,4);
                                $moviento.=" {$moveMaq}";


                                fwrite($pipes[0], "position startpos moves {$moviento}\n");    //send stop

                                
                                
                                //se guarda el moviento nuevo
                                  $resultado['movimiento']=$moveMaq;
                                  $resultado['resultado']='Y';
                                  
                                  
                            }





                            //DATOS DEL TABLERO 
                            fwrite($pipes[0], "d\n");    //send stop

                            fclose($pipes[0]);



                           $_SESSION['moves_ant']= $moviento;



                           $fila=fgets($pipes[1]);// CUIDAR POSIBLE ERROR
                           $contador=8;
                           while (!feof($pipes[1]))
                                {

                                    $fila=fgets($pipes[1]);

                                    //nuevo Fen
                                    if(strpos($fila, "Fen is")!==false)
                                    {
                                        $Fen=substr($fila,8);/// cambia por el espacion que le coloque 
                                        $_SESSION['fen']=$Fen;
                                        
                                        
                                        
                                        $resultado['sesion_fen']=$_SESSION['fen'];

                                        //solo es parte grafica para la filas del tablero
                                      //   $fila="<b>| a | b | c | d | e | f | g | h |</b><br>\n $fila";
                                    }




                                   $board.=$fila;

                                }


                           // fclose($pipes[0]);
                            fclose($pipes[1]);
                            fclose($pipes[2]);
                            $return_value = proc_close($process);  //stop test_gen.php
                           // echo ("Returned:".$return_value."\n");

                            
                             $resultado['fen']=$Fen;
                             
                            
                           // echo "<br>movientos : {$moviento} ";

                        }

                    }
                 
                 
                 
                 
                }
                
                
                
                
                
                break;
            
            
            //INICIAR JUEGO
            case "iniciar":
                  $_SESSION['sesion']="S";
                  $_SESSION['moves_ant']="";
                  $_SESSION['fen']="";
             
                break;


        } 
        
        
        
    echo ( json_encode($resultado));
        
        
 ?>
        




