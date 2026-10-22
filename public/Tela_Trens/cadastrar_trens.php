<!DOCTYPE html>
</html><html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>SISTEMA TREMTECH</title>

  <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body class="body_geral">
   <div class="pagina">

    <aside class="menu">
       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/ffffff/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a  href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a class="ativo" href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
  Trem
</a>

<a href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>
  Logout
</a>
    </aside>

    <main class="conteudo">
      <header>
        <div>
          <small>PAINEL ADMINISTRATIVO</small>
          <h1>Controle de trens cadastrados</h1>
          <h2>Trens</h2>
        </div>

        <div class="admin">
          <div class="foto"></div>

          <b>
            Admin<br>
            <span>Usuário</span>
          </b>
        </div>
      </header>

          <form class="formulario" method="POST">
            <div>
              <label for="nome">Nome do trem</label>
              <input type="text" id="nome" placeholder="Nome do trem" required>
            </div>

            <form class="formulario" method="POST">
            <div>
              <label for="nome">Velocidade máxima</label>
              <input type="text" id="nome" placeholder="Velocidade máxima" required>
            </div>
              
            <div>
              <label for="tipo">Tipo/categoria</label>
              <select id="tipo" required>
                <option>Selecione o tipo</option>
                <option>Velocidade</option>
                <option>Temperatura</option>
                <option>Status</option>
                </select>
              </div>
              <div>
              
              <div>
                <label for="rota">Rota</label>
                <select id="rota" required>
                  <option>Rota</option>
                  <option>Rota Norte</option>
              <option>Rota Sul</option>
              <option>Rota Leste</option>
              <option>Rota Oeste</option>
            </select>
          </div>
          <button class="botao" type="submit">Cadastrar Novo Trem</button>
        </form>
      </details>
      
      
      
          </tr>
        </thead>
    </tbody>
  </table>
</section>
</main>

 <script src="validacao.js"></script>
 <link rel="stylesheet" href="../../assets/style/style.css">

</body>
</html>