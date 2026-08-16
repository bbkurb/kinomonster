<?php
/**
 * Поля новости/поста — общие для create и edit, новостей и постов.
 *
 * В CI3 эта разметка была продублирована четыре раза
 * (news/create, news/edit, posts/create, posts/edit).
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
  <label for="title">Заголовок</label>
  <input type="text" class="form-control" id="title" name="title" value="<?= esc($value('title')) ?>">
</div>

<div class="form-group">
  <label for="text">Текст</label>
  <textarea class="form-control" id="text" name="text" rows="12"><?= esc($value('text')) ?></textarea>
</div>
