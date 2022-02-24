<?php if(count($search_result) > 0): ?>
  <h3>Результаты поиска (найдено <?php echo count($search_result); ?>)</h3>
  <hr>
<?php else: ?>
  <h3>Результаты поиска (найдено <?php echo count($search_result); ?>)</h3>
  <hr>
  <h2>По вашему запросу ничего не найдено, попробуйте изменить условия поиска</h2>
<?php endif ?>


<div class="row">

  <?php foreach ($search_result as $key => $value): ?>

    <div class="films_block col-lg-3 col-md-3 col-sm-3 col-xs-6">
      <a href="/movies/view/<?php echo $value['slug']; ?>"><img height="270" src="<?php echo $value['poster']; ?>" alt="<?php echo $value['name']; ?>"></a>
      <div class="film_label"><a href="/movies/view/<?php echo $value['slug']; ?>">

      	<?php if(strlen($value['name']) > 19) {
      		echo mb_substr($value['name'], 0, 19)."...";
      	} else {
      		echo $value['name'];
      	}
      	?>

      </a></div>
    </div>

  <?php endforeach ?>

</div>
