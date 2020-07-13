<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
var listFeatures;


listFeatures = $('.features');

listFeatures.click(function(){
    window.location = " ";
});
</script>



<?php if($page === 'main'){?>
        <script>
          let feat = $('#feat');
          $('li').removeClass('active');
          feat.addClass('active');
        </script>
      <?php }?>
      <?php if($page === 'code'){?>
        <script>
          let codepage = $('#code');
          $('li').removeClass('active');
          codepage.addClass('active');
        </script>
      <?php }?>
      <?php if($page === 'result'){?>
        <script>
          let resultPage = $('#result');
          $('li').removeClass('active');
          resultPage.addClass('active');
        </script>
      <?php }?>

</body>
</html>