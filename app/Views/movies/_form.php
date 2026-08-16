<?php
/**
 * Поля фильма — общие для форм создания и редактирования.
 *
 * В CI3 эти поля были продублированы в create.php и edit.php.
 *
 * @var array<string, mixed> $item
 */
$value = static fn (string $field): string => (string) (old($field) ?? ($item[$field] ?? ''));
?>

<div class="form-group">
  <label for="slug">Slug (латиницей)</label>
  <input type="text" class="form-control" id="slug" name="slug" value="<?= esc($value('slug')) ?>">
</div>

<div class="form-group">
  <label for="name">Название</label>
  <input type="text" class="form-control" id="name" name="name" value="<?= esc($value('name')) ?>">
</div>

<div class="form-group">
  <label for="descriptions">Описание</label>
  <textarea class="form-control" id="descriptions" name="descriptions" rows="6"><?= esc($value('descriptions')) ?></textarea>
</div>

<div class="form-group">
  <label for="year">Год</label>
  <input type="number" class="form-control" id="year" name="year" value="<?= esc($value('year')) ?>">
</div>

<div class="form-group">
  <label for="rating">Рейтинг</label>
  <input type="text" class="form-control" id="rating" name="rating" value="<?= esc($value('rating')) ?>">
</div>

<div class="form-group">
  <label for="poster">Ссылка на постер</label>
  <input type="text" class="form-control" id="poster" name="poster" value="<?= esc($value('poster')) ?>">
</div>

<div class="form-group">
  <label for="player_code">Ссылка на плеер</label>
  <input type="text" class="form-control" id="player_code" name="player_code" value="<?= esc($value('player_code')) ?>">
</div>

<div class="form-group">
  <label for="director">Режиссёр</label>
  <input type="text" class="form-control" id="director" name="director" value="<?= esc($value('director')) ?>">
</div>

<div class="form-group">
  <label for="add_date">Дата добавления</label>
  <input type="date" class="form-control" id="add_date" name="add_date" value="<?= esc($value('add_date')) ?>">
</div>

<div class="form-group">
  <label for="category_id">Категория</label>
  <select class="form-control" id="category_id" name="category_id">
    <option value="1" <?= $value('category_id') === '1' ? 'selected' : '' ?>>Фильм</option>
    <option value="2" <?= $value('category_id') === '2' ? 'selected' : '' ?>>Сериал</option>
  </select>
</div>
