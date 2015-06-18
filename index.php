<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="css/chessboard-0.3.0.css" />
        
        <script type="text/javascript" src="js/chessboard-0.3.0.js"> </script>
        <script type="text/javascript" src="js/jquery-1.10.1.min.js"> </script>
        <script type="text/javascript" src="js/chess.js"> </script>
        <script type="text/javascript" src="js/miJs.js"> </script>
        
        <script>
            var IS_EXISTE=null;
        </script>
        
     <title></title>
    
    </head>
    
    <body>
    <?php
        session_start();

        ///parametros 
        $moviento="";
        
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
                     $_SESSION['moves_ant']=$_SESSION['moves_ant'] ." ".$_REQUEST['move'];
                     $moviento=$_SESSION['moves_ant'];
                     
                }
                
                break;
            
            
            //INICIAR JUEGO
            case "iniciar":
                  $_SESSION['sesion']="S";
                  $_SESSION['moves_ant']="";
                  $_SESSION['fen']="";

                  $_SESSION['multipv']=$_REQUEST['multipv'];

                   $_SESSION['thread']=$_REQUEST['thread'];
                   $_SESSION['agre']=$_REQUEST['agre'];
                   $_SESSION['book']=$_REQUEST['book'];
                   $_SESSION['time']=$_REQUEST['time'];

				  $valor_level="40";
				  if(isset($_REQUEST['level']))
				  {
					$valor_level=$_REQUEST['level'];
				  }
				  $_SESSION['level']=$valor_level;
             
                break;


        } 
    
        
     
        


    ?>
        
        
           
        <?php
        if(isset($_SESSION['sesion']))
        {
            
            
            echo "<script> IS_EXISTE=1;</script>";
           
               echo " <div id='board' style='width: 400px'> </div>";
            
            echo "<label id='estado'></label>";
            echo "<br>";
            echo "<label id='fen'></label>";
            
            
            echo "<form method='get' action='index.php'> " ;
            echo "<input value='cerrar' type='submit'><input type='hidden' value='cerrar' name='accion'>";
            echo "</form>";
        }
        else {
            echo "<form method='get' action='index.php'> " ;
			echo "multipv <input type='number' value='1' name='multipv'> , rango 1 a 500 <br>";
            echo "Thread : <input type='number' value='1' name='thread'> rango  1 - 128 <br>";
            echo "Agres : <input type='number' value='100' name='agre'> rango  0 - 200 <br>";
            echo "Best book  <select name='book'> <option value='false' selected>false</option><option value='true' >true</option></select><br>";
            echo "*Level <input type='number' value='10' name='level'> rango 0 a 40<br>";
            echo "*time move  <input type='number' value='1000' name='time'> rango 1 a Infinito <br>";
            
            echo "<input value='inicio' type='submit'><input type='hidden' value='iniciar' name='accion'>";
            echo "</form>";
            
            
        }
        
        ?>
        
        
       
    </body>
</html>
