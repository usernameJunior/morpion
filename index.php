<!DOCTYPE html>
<html lang="fr" dir="ltr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'imbattable morpion du Dr. Genel</title>
    <link rel="stylesheet" href="./style.css">
    <?php
    function afficheStat($stat) {
      if ($stat == 'true') { $file = './stats/victoires'; }
      elseif ($stat == 'false') { $file = './stats/defaites'; }
      elseif ($stat == 'null') { $file = './stats/nuls'; }

      $statFile = fopen($file, 'r') or die('Erreur');
      $chiffre = (int) fread($statFile, filesize($file));
      fclose($statFile);
      echo $chiffre;
    }
    ?>

  </head>

  <body>

    <h1>L'imbattable morpion du Dr. Genel</h1>
    <h3>Victoires : <span id="statsVictoires"><?php afficheStat('true'); ?></span>
      | Matchs nuls : <span id="statsNuls"><?php afficheStat('null'); ?></span>
      | Défaites : <span id="statsDefaites"><?php afficheStat('false'); ?></span>
    </h3>
    <h3 id="verdict">Cliquez sur une case :</h3>

    <table>
      <tr>
        <td id="A1" class="coin" onclick="clic(a1)"></td>
        <td id="A2" class="cote" onclick="clic(a2)"></td>
        <td id="A3" class="coin" onclick="clic(a3)"></td>
      </tr>
      <tr>
        <td id="B1" class="cote" onclick="clic(b1)"></td>
        <td id="B2" class="milieu" onclick="clic(b2)"></td>
        <td id="B3" class="cote" onclick="clic(b3)"></td>
      </tr>
      <tr>
        <td id="C1" class="coin" onclick="clic(c1)"></td>
        <td id="C2" class="cote" onclick="clic(c2)"></td>
        <td id="C3" class="coin" onclick="clic(c3)"></td>
      </tr>
    </table>

    <input id="btnRecommencer" type="button" value="Recommencer" onclick="location.reload()">

    <script type="text/javascript">
      //VARIABLES

      let a1 = document.getElementById('A1');
      let a2 = document.getElementById('A2');
      let a3 = document.getElementById('A3');
      let b1 = document.getElementById('B1');
      let b2 = document.getElementById('B2');
      let b3 = document.getElementById('B3');
      let c1 = document.getElementById('C1');
      let c2 = document.getElementById('C2');
      let c3 = document.getElementById('C3');
      let plateau = {
        entier: [a1, a2, a3, b1, b2, b3, c1, c2, c3],
        coin: [a1, a3, c1, c3],
        cote: [a2, b1, b3, c2]
      };
      let alignements = {
        ligneA: [a1, a2, a3],
        ligneB: [b1, b2, b3],
        ligneC: [c1, c2, c3],
        col1: [a1, b1, c1],
        col2: [a2, b2, c2],
        col3: [a3, b3, c3],
        diagMont: [a3, b2, c1],
        diagDesc: [a1, b2, c3]
      };
      let player = {
        //La plupart de ces propriétés ont été faites pour que le joueur puisse
        //choisir son signe et si il commence.
        //Ce sera développé plus tard
        dealer: true, //true = c'est le joueur qui commence
        sign: 'x', //sign : signe du joueur
        nosign: 'o', //nosign : signe de l'ordi
        turn: true //true = c'est le tour du joueur
      };
      let tour = 1; //numéro du tour (une partie = 5 tours)

      //FONCTIONS

      //case cliquée vide ? Si oui, addSign et tourJS, si non, rien
      function clic(cell) {
        if (player.turn === true && !cell.hasChildNodes()) {
          player.turn= false;
          addSign(cell, player.sign);
          if (!checkWin(player.sign)) {
            setTimeout(tourJS, 300);
          }
        }
      }

      //Afficher le signe sur la case si la case est libre
      function addSign(cell, sign) {
        if (!cell.hasChildNodes()) {
          let img = signElt(sign);
          cell.appendChild(img);
        }
        else { //pour débuggage, ne sert à rien sinon
          console.log('Erreur : JS a voulu jouer une case qui est déjà remplie.')
          return false;
        }
      }
      //retourne l'élément HTML contenant X ou O
      function signElt(sign) {
        let img = document.createElement('img');
        img.setAttribute('width', '100');
        img.setAttribute('height', '100');
        if (sign === 'x') {
          img.setAttribute('src', 'img/x.png');
          return img;
        }
        else {
          img.setAttribute('src', 'img/o.png');
          return img;
        }
      }

      function tourJS() {
        //addSign(randCell(), player.nosign);
        switch (tour) {
          case 5:
            fin(null);
          break;
          case 1:
            if (b2.hasChildNodes()) { addSign(randCell(plateau.coin), player.nosign) }
            else { addSign(b2, player.nosign); }
          break;
          case 2:
            if (stringTrad(alignements.diagMont) === player.sign + player.sign + player.nosign
            || stringTrad(alignements.diagMont) === player.nosign + player.sign + player.sign
            || stringTrad(alignements.diagDesc) === player.sign + player.sign + player.nosign
            || stringTrad(alignements.diagDesc) === player.nosign + player.sign + player.sign) {
              addSign(randCell(plateau.coin), player.nosign)
            }
            else if (stringTrad(alignements.diagMont) === player.sign + player.nosign + player.sign
            || stringTrad(alignements.diagDesc) === player.sign + player.nosign + player.sign) {
              addSign(randCell(plateau.cote), player.nosign)
            }
            else {
              //1 : empecher le joueur de gagner
              if (checkDangerVictoire(player.sign)) {
                addSign(checkDangerVictoire(player.sign), player.nosign)
              }
              //2 : empecher le joueur de gagner au tour suivant
              else if (checkAlign(player.sign)) {
                addSign(checkAlign(player.sign), player.nosign);
              }
              //3 : faire en sorte de gagner au tour suivant
              else if (checkAlign(player.nosign)) {
                addSign(checkAlign(player.nosign), player.nosign);
              }
              else {
                addSign(randCell(plateau.coin), player.nosign)
              }
            }
          break;
          default:
            //1 : jouer la victoire
            if (checkDangerVictoire(player.nosign)) {
              addSign(checkDangerVictoire(player.nosign), player.nosign)
            }
            //2 : empecher le joueur de gagner
            else if (checkDangerVictoire(player.sign)) {
              addSign(checkDangerVictoire(player.sign), player.nosign)
            }
            //3 : empecher le joueur de gagner au tour suivant
            else if (checkAlign(player.sign)) {
              addSign(checkAlign(player.sign), player.nosign);
            }
            //4 : faire en sorte de gagner au tour suivant
            else if (checkAlign(player.nosign)) {
              addSign(checkAlign(player.nosign), player.nosign);
            }
            //5 : sinon, au pif
            else {
              addSign(randCell(plateau.entier), player.nosign)
            }
        }
        tour++;
        player.turn = true;
        checkWin(player.nosign);
      }

      //retourne une cellule libre aléatoire à partir d'un tableau
      function randCell(array) {
        //créer un tableau avec toutes les cellules libres du tableau en argument
        cellsLibres = array.filter(checkLibre);
        //lancer de dé
        let de = Math.floor(Math.random() * cellsLibres.length)
        return cellsLibres[de];
      }
      function checkLibre(cell) {
        if (!cell.hasChildNodes()) {
          return cell;
        }
      }

      //traduit un tableau type "alignements.ligne" en string type "xo_"
      function stringTrad(ligneArray) {
        let ligneString = '';
        for (let cell of ligneArray) {
          ligneString += signTrad(cell);
        }
        return ligneString;
      }
      function signTrad(cell) {
        if (cell.hasChildNodes()) {
          if (cell.firstChild.src.endsWith('x.png')) {
            return 'x';
          }
          else if (cell.firstChild.src.endsWith('o.png')) {
            return 'o';
          }
        }
        else { return '_'; }
      }

      //Cette fonction regarde si il y a une possibilité immédiate de défaite ou victoire
      //c'est-à-dire, checke les lignes de type "xx_" ou "oo_"
      //et retourne une case vide correspondante au pif
      function checkDangerVictoire(sign) {
        let dangerVictoire = [];
        for (let ligne in alignements) {
          let ligneString = stringTrad(alignements[ligne]);
          switch (ligneString) {
            case '_' + sign + sign:
              dangerVictoire.push(alignements[ligne][0]);
            break;
            case sign+'_'+sign:
              dangerVictoire.push(alignements[ligne][1]);
            break;
            case sign+sign+'_':
              dangerVictoire.push(alignements[ligne][2]);
            break;
          }
        }
        if (dangerVictoire.length > 0) {
          return randCell(dangerVictoire);
        }
      }

      function checkWin(sign) {
        for (let ligne in alignements) {
          let ligneString = stringTrad(alignements[ligne]);
          if (ligneString === sign + sign + sign) {
            if (sign === player.sign) { fin (false) }
            else { fin(true) }
            for (let cell of alignements[ligne]) {
              cell.firstChild.classList.add('clignote');
            }
            return true;
          }
        }
        return false;
      }

      function checkCheat() {
        for (let ligne in alignements) {
          let ligneString = stringTrad(alignements[ligne]);
          if (ligneString === player.sign + player.sign + player.sign) {
            return true;
          }
        }
        return false;
      }

      function checkWin(sign) {
        for (let ligne in alignements) {
          let ligneString = stringTrad(alignements[ligne]);
          if (ligneString === sign + sign + sign) {
            if (sign === player.sign) { fin (false) }
            else { fin(true) }
            for (let cell of alignements[ligne]) {
              cell.firstChild.classList.add('clignote');
            }
            return true;
          }
        }
        return false;
      }

      //pour checker les alignements de type "x _ _" ou "o _ _"
      function checkAlign(sign) {
        let alignementsDanger = [];
        for (let ligne in alignements) {
          let ligneString = stringTrad(alignements[ligne]);
          switch (ligneString) {
            case '_' + '_' + sign:
              alignementsDanger.push(alignements[ligne][0]);
              alignementsDanger.push(alignements[ligne][1]);
            break;
            case sign + '_' + '_':
              alignementsDanger.push(alignements[ligne][1]);
              alignementsDanger.push(alignements[ligne][2]);
            break;
            case '_' + sign + '_':
              alignementsDanger.push(alignements[ligne][0]);
              alignementsDanger.push(alignements[ligne][2]);
            break;
          }
        }
        //faire un tableau avec tout ce qui est pas en double dans alignementsDanger
        let filtered = alignementsDanger.filter((elt, index) => index !== alignementsDanger.indexOf(elt));
        //puis lancer de dé
        if (alignementsDanger.length > 0) {
          return randCell(filtered);
        }
      }

      function fin(commentQuoi) {
        player.turn = false;
        let verdict = document.getElementById('verdict');
        switch (commentQuoi) {
          case true:
            verdict.innerText = "L'IMBATTABLE MORPION DU DR. GENEL A ENCORE FRAPPÉ !";
            updateStats(true);
          break;
          case false:
            verdict.innerText = "L'impossible est donc arrivé... VOUS ÊTES L'ÉLU.";
            updateStats(false);
          break;
          case null:
            verdict.innerText = "Voilà.";
            updateStats(null);
          break;
        }
      }

      function updateStats(stat) {
        let eltID;
        if (stat === true) { eltID = 'statsVictoires'}
        else if (stat === false && checkCheat() === true) { eltID = 'statsDefaites'}
        else if (stat === null) { eltID = 'statsNuls'}
        else {
          document.getElementById('verdict').innerText = "Vilain geek ! L'imbattable morpion du Dr. Genel ne sera pas battu ainsi !!!"
          return;
        }
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
          document.getElementById(eltID).innerHTML = this.responseText;
          }
        }
        xhttp.open('POST', 'nope/stats.php', true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('stat=' + String(stat));
      }
    </script>

    <!-- Ce tag de commentaire non fermé sert à contourner les bannières de commentaire ajoutées par l'hébergeur.
    La plupart des navigateurs ferment automatiquement les balises "oubliées" et ne seront pas gênés -->
<!--
  </body>

</html>
