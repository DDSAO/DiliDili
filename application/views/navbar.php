<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<? echo base_url().'homepage' ?>">DiliDili</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="<? echo base_url()."category/index/funny/"?>">Funny </a>
      </li>
      <li class="nav-item active">
          <a class="nav-link" href="<? echo base_url()."category/index/animal"?>">Animal </a>
      </li>
      <li class="nav-item active">
          <a class="nav-link" href="<? echo base_url()."category/index/music"?>">Music </a>
      </li>
      <li class="nav-item active">
          <a class="nav-link" href="<? echo base_url()."category/index/other"?>">Other</a>
      </li>
      
    </ul>
    <div class="form-inline my-2 my-lg-0">
      <a href="<? echo base_url().'homepage/search/' ?>" class="mr-2" id="autofill"></a>
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="searchWords" id="search">
      <a href="<? echo base_url().'homepage/search/' ?>" class="btn btn-outline-success my-2 mx-lg-2 mx-0" type="submit" id="searchButton">Search</a>
    </div>
    <a class="btn btn-outline-primary my-2 mx-lg-2 mx-0" href="<?echo base_url("chatroom");?>">Chat Room</a>   
    
    <? 
      if ($login===1) {
        echo '
          <a class="btn btn-outline-primary my-2 mx-lg-2 mx-0" href="'.base_url().'dashboard/index/'
          .$id.'">Dashboard';
          if (intval($hasNew) === 1) {
            echo ' (New)';
          } 
          echo '</a><a class="btn btn-outline-primary my-2 mx-lg-2 mx-0" href="'.base_url().'logout" id="logout">Log out</a>';
      } else {
        echo '<a class="btn btn-outline-primary my-2 mx-lg-2 mx-0" href="'.base_url().'login">Log in</a>';
        echo '<a class="btn btn-outline-primary my-2 mx-lg-2 mx-0" href="'.base_url().'signup">Sign up</a>';
      }
    ?>
    
    
    
  </div>
</nav>
<script>
  $(document).ready(function(){
    console.log('ready')
    $("#search").tooltip();
    $("input[type='search']").on('input',function(){
      $("#searchButton").attr('href',"<? echo base_url().'homepage/search/' ?>"+$(this).val());
      fetch('<?echo base_url('homepage/searchWord/')?>'+$(this).val())
      .then(res=>res.text())
      .then(result=>{
        if(result){
          $("#autofill").text(result+" ?").attr('href','<?echo base_url('homepage/search/')?>'+result)
        } else {
          $("#autofill").text("")
        }
        
      })
    })

    $("#logout").click(function(e){
      Object.keys(Cookies.get()).forEach(function(cookieName) {
      Cookies.remove(cookieName);
    });
    })
  })
</script>