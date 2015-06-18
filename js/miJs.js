/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var board=null; 
var fen="";
var movientoUsuario="";
var game=null;
var updateStatus=null;


var makeRandomMove =null;

            window.onload=function (){
           
               
                board,
                game = new Chess();

                  // do not pick up pieces if the game is over
                  // only pick up pieces for White
                  var onDragStart = function(source, piece, position, orientation) {
                    if (game.in_checkmate() === true || game.in_draw() === true ||
                      piece.search(/^b/) !== -1) {
                      return false;
                    }
                  };




               updateStatus = function() {
                    var status = '';

                    var moveColor = 'Blanco';
                    if (game.turn() === 'b') {
                      moveColor = 'Negro';
                    }

                    // checkmate?
                    if (game.in_checkmate() === true) {
                      status = ' ' + moveColor + ' esta en mate.';
                    }

                    // draw?
                    else if (game.in_draw() === true) {
                      status = 'tablas';
                    }

                    // game still on
                    else {
                      status = moveColor + ' mueve';

                      // check?
                      if (game.in_check() === true) {
                        status += ', ' + moveColor + ' jaque';
                      }
                    }

                   // console.log("se inicio :: "+status);
              
              
                  };

    
    
              makeRandomMove = function(move) {
                  
                 
                game.move({ from: move.substring(0,2),
                    to :move.substring(2),  
                    promotion: 'q',
                  });
       
                  board.position(game.fen());
                    
                   fen=game.fen();
                   
                  };




                  updateStatus = function() {
                   var status = '';
                   var $statusEl=$("#estado");
                   var $fenEl=$("#fen");

                    

                    var moveColor = 'blanco';
                    if (game.turn() === 'b') {
                      moveColor = 'negro';
                    }

                    // checkmate?
                    if (game.in_checkmate() === true) {
                      status = 'has perdido, ' + moveColor + ' esta en jaquemates.';
                    }

                    // draw?
                    else if (game.in_draw() === true) {
                      status = 'tablas ';
                    }

                    // game still on
                    else {
                      status = moveColor + ' mueve';

                      // check?
                      if (game.in_check() === true) {
                        status += ', ' + moveColor + ' es jaque';
                      }
                    }

                    $statusEl.html(status);
                    $fenEl.html(game.fen());
                    
                  };



               var onDrop = function(source, target) {
                    // see if the move is legal
                   
                 var move = game.move({
                      from: source,
                      to: target,
                      promotion: 'q' // NOTE: always promote to a pawn for example simplicity
                    });



                     
                    // illegal move
                    if (move === null) return 'snapback';
                    if(move.color=='b')  return 'snapback';


                   
                   movientoUsuario=source+target;
                   
                      updateStatus(); 
                    
                    // make random legal move for black
                   // window.setTimeout(makeRandomMove, 250);
                    movientoMaquina(fen,movientoUsuario);//le pide el moviento a la maquina
                    
                    
                    
                  
                  };






                  // update the board position after the piece snap
                  // for castling, en passant, pawn promotion
                  var onSnapEnd = function() {
                    board.position(game.fen());
                  };

                  var cfg = {
                    draggable: true,
                    position: 'start',
                  //  onDragStart: onDragStart,
                    onDrop: onDrop,
                  //  onSnapEnd: onSnapEnd
                  };
                
                  
                  if(IS_EXISTE!==null)
                   {
                       board = new ChessBoard('board', cfg);
                   }
               
             
            }
            
            
            
     function movientoMaquina(mifen,miMoviento){
         
         $.ajax({
             type:'GET'
             ,url:'operacion.php'
             ,data:{accion:'mover' , fen:mifen, move:miMoviento}
             ,dataType:'json'
             //,error:function(){alert("error en el servidor")}
             ,success:function (data){
                 
                 if(data!=null)
                 {
                     
                     if(makeRandomMove!==null)
                     {
                        makeRandomMove(data.movimiento);
                        
                        
                        
                        
                        updateStatus();  
                       
                     }
                     
                     
                 }
                }
              ,beforeSend:function (){console.log("se incio");}  
             ,timeout:10000
         });
         
         
     }       