<h2>Поиск (найдено <?php echo $tCount; ?>)</h2>

<div class="row">
  <?php foreach ($search_result as $key => $value): ?>
    <div class="films_block col-lg-3 col-md-3 col-sm-3 col-xs-6">
      <a href="/movies/view/<?php echo $value['slug']; ?>"><img height="275" src="<?php echo $value['poster']; ?>" alt="<?php echo $value['name']; ?>"></a> 
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
<?php echo $pagination; ?>