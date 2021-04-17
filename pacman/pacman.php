<!DOCTYPE html>
<html>
<head>
<title>Pacman</title>
</head>
<body>

    <canvas id="platno" width="560" height="600" style="border:1px solid black; float:left"></canvas>
    <div style="float: left">
        <table id = "skorovi"></table>
        <form method="POST" action = "register.php">
            <input type="text" placeholder="Nickname" name="nickname">
            <input type="number"  id="labela" name="score" readonly>
            <input type="submit">
        </form>
    </div>

    

    <script>
      var ajax = new XMLHttpRequest();
          ajax.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                  var niz = JSON.parse(ajax.responseText);
                  var tabela = document.getElementById("skorovi");
                  tabela.innerHTML = "";
                  for (var i = 0; i < niz.length; i++) {
                      var tr = document.createElement("tr");
                      var tdIme = document.createElement("td");
                      tdIme.innerHTML = niz[i].nickname;
                      tr.appendChild(tdIme);
                      var tdPrezime = document.createElement("td");
                      tdPrezime.innerHTML = niz[i].score;
                      tr.appendChild(tdPrezime);
                      tabela.appendChild(tr);
                  }
              }
          };
          ajax.open("GET", "db.php", true);
          ajax.send();
    </script>
    
    <script> // ghost movements
        let successors = (root, matrica) => {
            let connectedCells = [      // susedna polja root-a
                [root[0] - 1, root[1]],
                [root[0], (root[1]+m)%m - 1],
                [root[0] + 1, root[1]],
                [root[0], (root[1]+m)%m + 1]
            ]

            const validCells = connectedCells.filter(   //celije koje odgovaraju matrici
                (cell) => (
                cell[0] >= 0 && cell[0] < n 
                && (cell[1]+m)%m >= 0 && (cell[1]+m)%m <m)
            )

            const successors = validCells.filter(   //da se krece samo po poljima istog tipa kao root
                (cell) => (matrica[cell[0]][(cell[1]+m)%m] != 1)  // od 1;0 jer je rotirano
            )

            return successors
            }

            const buildPath = (traversalTree, to) => {
                let path = [to]
                let parent = traversalTree[to]
                while (parent) {
                    path.push(parent)
                    parent = traversalTree[parent]
                }
                return path.reverse()
            }

            const bfs = (from, to) => {
                let traversalTree = []
                let visited = new Set
                let queue = []
                queue.push(from)

                while (queue.length) {
                    let subtreeRoot = queue.shift()
                    visited.add(subtreeRoot.toString())
                   // console.log(subtreeRoot+"  aa");
                    if (subtreeRoot[0] == to[0]&&subtreeRoot[1] == to[1]) return buildPath(traversalTree, to)

                    for (child of successors(subtreeRoot, matrica)) {
                       // console.log(visited.has(child.toString()));
                        if (!visited.has(child.toString())){
                        //  console.log("uso");
                            traversalTree[child] = subtreeRoot
                            queue.push(child);
                    }
                }
            }
    }
    </script>

    <script> //timerTick-ovi
        function pacman_timerTick() 
        {
            var labela= document.getElementById("labela");
            labela.setAttribute("value",score);
            //console.log(pdx,pdy);
            //kretanje pacmana:
            if((blinkyX == pacmanX && blinkyY == pacmanY) || (clydeX == pacmanX && clydeY == pacmanY) || (inkyX == pacmanX && inkyY == pacmanY)) //dal je pacman pojeden
            {  
                if(chase==0)
                {
                    console.log("game over");
                    console.log("usoooo inky");
                    clearInterval(ghost_timer);
                    clearInterval(pacman_timer);
                }
                else
                {
                    if(blinkyX == pacmanX && blinkyY == pacmanY) {score += 200; blinkyX = 14; blinkyY = 12; console.log("blinky: ",blinkyX,blinkyY);}
                    if(inkyX == pacmanX && inkyY == pacmanY) {score += 200; inkyX = 15; inkyY = 13;}
                    if(clydeX == pacmanX && clydeY == pacmanY) {score += 200; clydeX = 13; clydeY = 14;}
                }
            } 
            draw_map(); // refresh mape
            if(matrica[pacmanY+wy][pacmanX+wx] == 0) pdx = wx , pdy = wy;
            if(matrica[pacmanY+pdy][(pacmanX+pdx+m)%m] == 0)
            {
                //console.log("uso");
                pacmanX = (pacmanX+pdx+m)%m;
                pacmanY += pdy;
                if(vockice[pacmanY][pacmanX] == 1) score+=cookie;
                else if(vockice[pacmanY][pacmanX] == 2)
                {
                    score+=fruit;
                    chase = 1;
                    countdown = 0;
                }
                vockice[pacmanY][pacmanX] = 0;
                console.log(score);
            }
            draw_pacman(pacmanX,pacmanY);
        }

        function ghost_timerTick()
        {
            if(chase == 0)
            {

                    //kretanje blinky-a
                if((blinkyX == pacmanX && blinkyY == pacmanY) || (clydeX == pacmanX && clydeY == pacmanY) || (inkyX == pacmanX && inkyY == pacmanY))
                {   console.log("game over");
                    console.log("usoooo");
                    clearInterval(ghost_timer);
                    clearInterval(pacman_timer);
                }
                else
                {
                    var blinky_next = bfs([blinkyY,blinkyX],[pacmanY,pacmanX]);
                    var pom1 = blinky_next[1];
                    blinkyX = pom1[1];
                    blinkyY = pom1[0];
                    draw_ghost(blinkyX,blinkyY,"red");
                    //draw_blinky(blinkyX,blinkyY);
                }
                //else chase == 1

                //kretanje clyde-a
                if((blinkyX == pacmanX && blinkyY == pacmanY) || (clydeX == pacmanX && clydeY == pacmanY) || (inkyX == pacmanX && inkyY == pacmanY))
                {   console.log("game over");
                    console.log("usoooo clyde");
                    clearInterval(ghost_timer);
                    clearInterval(pacman_timer);
                }
                else
                {
                    if((Math.abs(clydeX-pacmanX) + Math.abs(clydeY-pacmanY))> 8 && stigao == 1)  // 1 je da jeste stigao
                    {
                        var clyde_next= bfs([clydeY,clydeX],[pacmanY,pacmanX]);
                        var pom2=clyde_next[1];
                        clydeX = pom2[1];
                        clydeY = pom2[0];
                        draw_ghost(clydeX,clydeY,"#FF8F43");
                    }
                    else
                    {
                        stigao = 0;
                        if(Math.abs(clydeY-28)>=1 || Math.abs(clydeX-1)>=1)
                        {
                            var clyde_next = bfs([clydeY,clydeX],[28,1]);
                            var pom2=clyde_next[1];
                            clydeX = pom2[1];
                            clydeY = pom2[0];
                        }
                        draw_ghost(clydeX,clydeY,"#FF8F43");
                        if(clydeX == 1 && clydeY == 28) stigao = 1;
                    }
                    
                }

                //kretanje inky-a
                if((blinkyX == pacmanX && blinkyY == pacmanY) || (clydeX == pacmanX && clydeY == pacmanY) || (inkyX == pacmanX && inkyY == pacmanY)) //dal je pacman pojeden
                {  
                    console.log("game over");
                    console.log("usoooo inky");
                    clearInterval(ghost_timer);
                    clearInterval(pacman_timer);
                } 
                else
                {
                    var tx = pacmanX - blinkyX;
                    var ty = pacmanY - blinkyY;
                    if(moze(pacmanY+ty,(pacmanX+tx+m)%m) && matrica[pacmanY+ty][pacmanX+tx] == 0) // ako je polje na koje treba da ide ispravno (ovaj if)
                    {
                        if(Math.abs(inkyY-(pacmanY+ty))>=1 || Math.abs(inkyX-(pacmanX+tx))>=1) // ovde mzd i ne treba (pacmanX+tx+m)%m nego bez %n
                        {
                            var inky_next = bfs([inkyY,inkyX],[pacmanY+ty,pacmanX+tx]);
                            var pom1 = inky_next[1];
                            inkyX = pom1[1];
                            inkyY = pom1[0];
                            inky_mozeX = pacmanX+tx; // ovde mzd ako ne radi stavi (pacmanX+tx+m)%m
                            inky_mozeY = pacmanY+ty;
                        }
                        
                    }
                    else
                    {
                        if(inky_mozeX != 0 && inky_mozeY != 0) // ovaj if je = ako je postavljeno polje na koje da ide
                        {
                            if(inkyX != inky_mozeX && inkyY != inky_mozeY) //ovaj if je ako nije dosao do tog poslednjeg valjanog polja
                            {
                                var inky_next = bfs([inkyY,inkyX],[inky_mozeY,inky_mozeX]);
                                var pom1 = inky_next[1];
                                if(Math.abs(inkyY-inky_mozeY)>=1 || Math.abs(inkyX-inky_mozeX)>=1) // stavili smo || ili umesto && jer kod clyda tako radi
                                {
                                    inkyX = pom1[1];
                                    inkyY = pom1[0];
                                }
                            }
                            else // ovaj else je ako je dosao do poslednjeg dobrog polja, da ne bi stajao, nek onda prati pacmana, do promene situacije
                            {
                                inky_mozeX = inkyX; // da ne bi uvek ulazio u prethodni if
                                inky_mozeY = inkyY;
                                var inky_next = bfs([inkyY,inkyX],[pacmanY,pacmanX]);
                                var pom1 = inky_next[1];
                                inkyX = pom1[1];
                                inkyY = pom1[0];
                            }
                            
                        }
                    }
                    draw_ghost(inkyX,inkyY,"#2fe5a8");
                    //draw_inky(inkyX,inkyY);
                }

            }
            else
            {
                if(countdown == 80) // kad pojede fruit, ako opet pojede novi fruit za vreme trajanja bivseg, treba countdown da se resetuje
                {
                    countdown=0;
                    chase = 0;
                }
                countdown++;

                if(blinkyX == pacmanX && blinkyY == pacmanY) {score += 200; blinkyX = 14; blinkyY = 12; console.log("blinky: ",blinkyX,blinkyY);}
                if(inkyX == pacmanX && inkyY == pacmanY) {score += 200; inkyX = 15; inkyY = 13;}
                if(clydeX == pacmanX && clydeY == pacmanY) {score += 200; clydeX = 13; clydeY = 14;}

                //kretanje clyda //x = 10 y = 25
                if(clydeX!= 13 || clydeY != 14)
                {
                    if((Math.abs(clydeY-25)>=1 || Math.abs(clydeX-10)>=1) && clyde_stigao_kruzi == 1)
                    {
                        var clyde_next = bfs([clydeY,clydeX],[25,10]);
                        var pom2=clyde_next[1];
                        clydeX = pom2[1];
                        clydeY = pom2[0];
                        if(clydeX == 10 && clydeY == 25) clyde_stigao_kruzi = 0;
                    }
                    else
                    {
                        if((Math.abs(clydeY-28)>=1 || Math.abs(clydeX-1)>=1) && clyde_stigao_kruzi == 0)
                        {
                            var clyde_next = bfs([clydeY,clydeX],[28,1]);
                            var pom2=clyde_next[1];
                            clydeX = pom2[1];
                            clydeY = pom2[0];
                            if(clydeX == 1 && clydeY == 28) clyde_stigao_kruzi = 1;
                        }
                    }
                    draw_ghost(clydeX,clydeY,"#2424ff");
                }
                else draw_ghost(clydeX,clydeY,"#FF8F43");

                //kretanje blinky-a 
                if(blinkyX!= 14 || blinkyY != 12)
                {
                    if((Math.abs(blinkyY-1)>=1 || Math.abs(blinkyX-1)>=1) && blinky_stigao_kruzi == 1)
                    {
                        var blinky_next = bfs([blinkyY,blinkyX],[1,1]);
                        var pom2=blinky_next[1];
                        blinkyX = pom2[1];
                        blinkyY = pom2[0];
                        if(blinkyX == 1 && blinkyY == 1) blinky_stigao_kruzi = 0;
                    }
                    else
                    {
                        if((Math.abs(blinkyY-4)>=1 || Math.abs(blinkyX-6)>=1) && blinky_stigao_kruzi == 0)
                        {
                            var blinky_next = bfs([blinkyY,blinkyX],[4,6]);
                            var pom2=blinky_next[1];
                            blinkyX = pom2[1];
                            blinkyY = pom2[0];
                            if(blinkyX == 6 && blinkyY == 4) blinky_stigao_kruzi = 1;
                        }
                    }
                    draw_ghost(blinkyX,blinkyY,"#2424ff");
                }
                else draw_ghost(blinkyX,blinkyY,"red");

                //kretanje inky-a  x= 18 y 25
                if(inkyX!= 15 || inkyY != 13)
                {
                    if((Math.abs(inkyY-28)>=1 || Math.abs(inkyX-26)>=1) && inky_stigao_kruzi == 1)
                    {
                        var inky_next = bfs([inkyY,inkyX],[28,26]);
                        var pom2=inky_next[1];
                        inkyX = pom2[1];
                        inkyY = pom2[0];
                        if(inkyX == 26 && inkyY == 28) inky_stigao_kruzi = 0;
                    }
                    else
                    {
                        if((Math.abs(inkyY-25)>=1 || Math.abs(inkyX-18)>=1) && inky_stigao_kruzi == 0)
                        {
                            var inky_next = bfs([inkyY,inkyX],[25,18]);
                            var pom2=inky_next[1];
                            inkyX = pom2[1];
                            inkyY = pom2[0];
                            if(inkyX == 18 && inkyY == 25) inky_stigao_kruzi = 1;
                        }
                    }
                    draw_ghost(inkyX,inkyY,"#2424ff");
                }
                else draw_ghost(inkyX,inkyY,"#2fe5a8");
            }
        }
        

    </script>

    <script>// crtanje mape, pacmana, i vockica, timer tick
        function moze(i,j)
        {
            if(i>=0 && i<n && j>=0 && j<m) return true;
            return false;
        }
        function draw_line(x1,y1,x2,y2)
        {
            ctx.strokeStyle = block_color;
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        }
        function draw_map()
        {
            ctx.fillStyle = path_color;
            for(var i = 0; i < n; i++)
            {
                for(var j = 0; j < m; j++)
                {
                    ctx.fillRect(j*block_size, i*block_size, block_size, block_size);
                    ctx.lineWidth = 3;
                    if(matrica[i][j] == 1)
                    {
                        if(i == 0) draw_line(j*block_size,i*block_size,j*block_size+block_size,i*block_size);
                        if(i == n-1) draw_line(j*block_size,i*block_size+block_size,j*block_size+block_size,i*block_size+block_size);
                        if(j == 0) draw_line(j*block_size,i*block_size,j*block_size,i*block_size+block_size);
                        if(j == m-1) draw_line(j*block_size+block_size,i*block_size,j*block_size+block_size,i*block_size+block_size);
                    } 
                }
            } // okvir je prvi for
            for(var i = 0; i < n; i++) // blokovi je drugi okvir
            {
                for(var j = 0; j < m; j++)
                {
                    if(matrica[i][j] == 1)
                    {
                        if(moze(i,j+1) && matrica[i][j+1] != 1) draw_line(j*block_size+block_size,i*block_size,j*block_size+block_size,i*block_size+block_size);
                        if(moze(i,j-1) && matrica[i][j-1] != 1) draw_line(j*block_size,i*block_size,j*block_size,i*block_size+block_size);
                        if(moze(i+1,j) && matrica[i+1][j] != 1) draw_line(j*block_size,i*block_size+block_size,j*block_size+block_size,i*block_size+block_size);
                        if(moze(i-1,j) && matrica[i-1][j] != 1) draw_line(j*block_size,i*block_size,j*block_size+block_size,i*block_size);
                    }
                    else if(matrica[i][j] == 2)
                    {
                        if(moze(i-1,j) && matrica[i-1][j] == 0)
                        {
                            ctx.strokeStyle = "#d8b2d8";
                            ctx.beginPath();
                            ctx.moveTo(j*block_size, i*block_size);
                            ctx.lineTo(j*block_size+block_size,i*block_size);
                            ctx.stroke();
                        }
                    }

                    if(vockice[i][j] == 1) draw_cookie(j,i);
                    else if(vockice[i][j] == 2) draw_fruit(j,i);
                }
            }
        }

        function draw_pacman(i,j)
        {
            //draw_map();
            //console.log(i,j);

            ctx.beginPath();
	        ctx.fillStyle = "#ebe834";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, ((block_size/2)*3)/4, 0, 2 * Math.PI);
	        ctx.fill();
        }

        function draw_fruit(i,j)
        {
            ctx.beginPath();
	        ctx.fillStyle = "#ffe8b6";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, block_size/3, 0, 2 * Math.PI);
	        ctx.fill();
        }
        function draw_cookie(i,j)
        {
            ctx.beginPath();
	        ctx.fillStyle = "#fff0cf";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, block_size/8, 0, 2 * Math.PI);
	        ctx.fill();
        }

        /*function draw_blinky(i,j)
        {
            ctx.beginPath();
	        ctx.fillStyle = "red";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, ((block_size/2)*3)/4, 0, 2 * Math.PI);
	        ctx.fill();
        }

        function draw_clyde(i,j)
        {
            ctx.beginPath();
	        ctx.fillStyle = "#FF8F43";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, ((block_size/2)*3)/4, 0, 2 * Math.PI);
	        ctx.fill();
        }

        function draw_inky(i,j)
        {
            ctx.beginPath();
	        ctx.fillStyle = "#2fe5a8";
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, ((block_size/2)*3)/4, 0, 2 * Math.PI);
	        ctx.fill();
        }*/

        function draw_ghost(i,j,color)
        {
            ctx.beginPath();
	        ctx.fillStyle = color;
	        ctx.arc(i*block_size+block_size/2, j*block_size+block_size/2, ((block_size/2)*3)/4, 0, 2 * Math.PI);
	        ctx.fill();
        }
        

        //kraj funkcija skripte
    </script>


    <script>//promenljive
        var n,m;
        n = 30; m = 28;
        var pacmanX,pacmanY;
        var block_size = 21;
        pacmanX = 21; // x = j
        pacmanY = 28; // y = i
        var pdx = 0;
        var pdy = 0;
        var wx, wy;
        wx = 0; wy = 0;
        var blinkyX,blinkyY,pinkyX,pinkyY,clydeX,clydeY,inkyX,inkyY;
        blinkyX = 14;
        blinkyY = 10;
        pinkyX = 14;
        pinkyY = 14;
        clydeX = 13;
        clydeY = 14;
        inkyX = 15;
        inkyY = 13;
        var canvas = document.getElementById("platno");
        canvas.width = block_size*28;
        canvas.height = block_size*30;
        var ctx = canvas.getContext("2d");
        var block_color = "#0419CD";
        var path_color = "#000000";
        var mapa = "111111111111111111111111111110000000000001100000000000011011110111110110111110111101101111011111011011111011110110000000000000000000000000011011110110111111110110111101101111011011111111011011110110000001100001100001100000011111110111110110111110111111333331011111011011111013333333333101100000000001101333333333310110111221110110133333111111011013333331011011111100000000001333333100000000001111110110133333310110111111333331011011111111011013333333333101100000000001101333333333310110111111110110133333111111011011111111011011111110000000000001100000000000011011110111110110111110111101101111011111011011111011110110001100000000000000001100011110110110111111110110110111111011011011111111011011011110000001100001100001100000011011111111110110111111111101101111111111011011111111110110000000000000000000000000011111111111111111111111111111";
        var matrica = [];
        var vockice = [];
        var score = 0;
        var cookie = 10;
        var fruit = 50;
        var stigao = 1; //1 je true
        var inky_mozeX = 0;
        var inky_mozeY = 0;
        var chase = 0;  // 0 = du jede pacmana,  1 = pacman jede duha
        var combo = 0;
        var countdown = 0;
        var blinky_stigao_kruzi = 1; 
        var clyde_stigao_kruzi = 1;
        var inky_stigao_kruzi = 1;

        
        
        for(var i=0; i<30; i++) 
        {                                                   
            matrica[i] = [];                               
            for(var j=0; j<28; j++)                       
            {
                matrica[i][j] = mapa[i*28+j];
            }
        }

        for(var i=0; i<30; i++) 
        {                                                   
            vockice[i] = [];                               
            for(var j=0; j<28; j++)                       
            {
                if(matrica[i][j] == 0) vockice[i][j] = 1; 
                else vockice[i][j] = 0; // cookie = 1, fruit = 2, empty = 0 
            }
        }
        vockice[3][1] = 2; vockice[3][26] = 2; vockice[22][1] = 2; vockice[22][26] = 2;
        
       // console.log(matrica[28][26]);
        
        //console.log(matrica);
        draw_map();
        draw_pacman(21,28);// j,i
        draw_ghost(blinkyX,blinkyY,"red");
        draw_ghost(clydeX,clydeY,"#FF8F43");
        draw_ghost(inkyX,inkyY,"#2fe5a8");

        
        var pacman_timer = setInterval(pacman_timerTick,125);
        var ghost_timer = setInterval(ghost_timerTick,125);

        /*var blinky_next = bfs([blinkyY,blinkyX],[pacmanY,pacmanX]);
            blinkyX = blinky_next[0][1];
            blinkyY = blinky_next[0][0];
            draw_blinky(blinkyX,blinkyY);*/
        

        document.addEventListener("keydown", function(event) {
            // event.preventDefault();
            //console.log("KEY DOWN");
            //console.log(event);
            
            if(event.key=="ArrowRight")
            {
                wx = 1; wy = 0;
                if(matrica[pacmanY][pacmanX+1] == 0) pdx = 1,pdy = 0;
            }
            if(event.key=="ArrowLeft")
            {
                wx = -1; wy = 0;
                if(matrica[pacmanY][pacmanX-1] == 0) pdx = -1,pdy = 0;
            }
            if(event.key=="ArrowDown")
            {
                wx = 0; wy = 1;
                if(matrica[pacmanY+1][pacmanX] == 0) pdx = 0,pdy = 1;
            }
            if(event.key=="ArrowUp")
            {
                wx = 0; wy = -1;
                if(matrica[pacmanY-1][pacmanX] == 0) pdx = 0,pdy = -1;
            }
                
             
        })
        
        

    </script>

</body>
</html>