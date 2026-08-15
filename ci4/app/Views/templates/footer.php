<?php
/**
 * Подвал сайта. Подключает сайдбар и закрывает разметку из header.php.
 */
?>
       </div>

       <?= view('templates/menu', [
           'sidebarNews'  => $sidebarNews ?? [],
           'sidebarFilms' => $sidebarFilms ?? [],
           'auth'         => $auth ?? service('auth'),
       ]) ?>

       </div>
      </div>

      <div class="clear"></div>
    </div>

    <footer>
      <div class="container">
        <p class="text-center"> <a href="/">&copy; <?= date('Y') ?> Bahrom Kurbanov</a> </p>
      </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
  </body>
</html>
